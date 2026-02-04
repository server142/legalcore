<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WelcomeOverlay extends Component
{
    public $show = false;
    public $videoUrl;
    public $message;
    public $title;
    public $videoType = 'none';
    public $embedUrl = '';

    public function mount()
    {
        $user = Auth::user();
        if (!$user) return;

        // If already seen, don't load anything heavy
        if ($user->has_seen_welcome) {
            $this->show = false;
            return;
        }

        $this->show = true;
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $settings = DB::table('global_settings')
            ->whereIn('key', ['welcome_video_url', 'welcome_message', 'welcome_title'])
            ->pluck('value', 'key')
            ->toArray();

        $this->videoUrl = $settings['welcome_video_url'] ?? '';
        $this->message = $settings['welcome_message'] ?? 'Bienvenido a LegalCore.';
        $this->title = $settings['welcome_title'] ?? 'Bienvenido a tu Espacio Legal';

        $this->processVideoUrl();
    }

    public function processVideoUrl()
    {
        if (empty($this->videoUrl)) return;

        if (strpos($this->videoUrl, 'youtube.com') !== false || strpos($this->videoUrl, 'youtu.be') !== false) {
            $this->videoType = 'youtube';
            // Extract Video ID
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->videoUrl, $matches);
            
            if (isset($matches[1])) {
                // Use youtube-nocookie to reduce tracking requests and avoid client-side blocking errors
                $this->embedUrl = "https://www.youtube-nocookie.com/embed/" . $matches[1] . "?autoplay=0&rel=0";
            }
        } elseif (preg_match('/\.(mp4|webm|ogg)$/i', $this->videoUrl)) {
             $this->videoType = 'mp4';
             $this->embedUrl = $this->videoUrl;
        } else {
            $this->videoType = 'mp4';
            $this->embedUrl = $this->videoUrl;
        }
    }

    public function closeAndMarkAsSeen()
    {
        $user = Auth::user();
        if ($user) {
            $user->update(['has_seen_welcome' => true]);
        }
        $this->show = false;
    }

    public function render()
    {
        // Don't render anything if not showing
        if (!$this->show) {
            return <<<'HTML'
            <div></div>
            HTML;
        }

        return view('livewire.welcome-overlay');
    }
}
