<?php

namespace App\Livewire\Manual;

use Livewire\Component;
use App\Models\ManualPage;
use Illuminate\Support\Facades\Auth; // Added for Auth::user()

class Index extends Component
{
    public $pages; // Added to store fetched pages
    public $selectedPage; // Added, assuming it's a property for selected page

    // Added mount method to initialize pages
    public function mount()
    {
        $this->fetchPages();
        $this->selectedPage = $this->pages->first(); // Initialize selectedPage
    }

    public function fetchPages()
    {
        $user = Auth::user();
        
        $query = ManualPage::query();

        // Filter by Role Sensitivity
        if ($user && !$user->hasRole('super_admin')) {
            $query->where(function($q) use ($user) {
                $q->whereNull('required_role')
                  ->orWhere('required_role', 'user');
                
                if ($user->hasRole('admin')) {
                    $q->orWhere('required_role', 'admin');
                }
            });
        }
            
        $this->pages = $query->orderBy('order')
            ->orderBy('title') // Added orderBy('title')
            ->get();
            
        // Si la página seleccionada está protegida y el usuario no tiene permiso, redirigir
        if ($this->selectedPage && $this->selectedPage->required_role && $user && !$user->hasRole('super_admin')) {
             if ($this->selectedPage->required_role !== ($user->role ?? 'user')) {
                 $this->selectedPage = $this->pages->first();
             }
        }
    }

    public function render()
    {
        // $pages is now a property, set by fetchPages()
        // $pages = ManualPage::orderBy('order')->get(); // This line is removed as pages are fetched in fetchPages()

        $settings = \Illuminate\Support\Facades\DB::table('global_settings')
            ->whereIn('key', ['welcome_video_url', 'welcome_message'])
            ->pluck('value', 'key')
            ->toArray();

        $videoUrl = $settings['welcome_video_url'] ?? '';
        $embedUrl = '';
        $videoType = 'none';

        if (!empty($videoUrl)) {
             if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $videoUrl, $matches)) {
                $videoType = 'youtube';
                $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
            } elseif (str_ends_with(strtolower($videoUrl), '.mp4')) {
                $videoType = 'mp4';
                $embedUrl = $videoUrl;
            } else {
                $videoType = 'mp4';
                $embedUrl = $videoUrl;
            }
        }

        return view('livewire.manual.index', [
            'pages' => $this->pages,
            'welcomeVideo' => [
                'type' => $videoType,
                'url' => $embedUrl,
                'message' => $settings['welcome_message'] ?? ''
            ]
        ])->layout('layouts.app');
    }
}
