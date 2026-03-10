<?php

namespace App\Livewire\Directory;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\DirectoryProfile;
use App\Models\DirectoryAnalytic;

class Dashboard extends Component
{
    public string $period = '30'; // '7', '30', '90'

    public function mount()
    {
        $user = Auth::user();
        // Crear perfil si no existe (cubre usuarios anteriores al directorio)
        DirectoryProfile::firstOrCreate(['user_id' => $user->id]);
    }

    private function profile(): DirectoryProfile
    {
        return Auth::user()->directoryProfile;
    }

    /**
     * ¿Es un usuario de despacho Diogenes (plan completo)?
     * vs. usuario solo del directorio (plan directory-*)
     */
    public function getIsDespachoUserProperty(): bool
    {
        $plan = Auth::user()->tenant->plan ?? '';
        // Si no tiene plan o su plan es de directorio → NO es usuario de despacho
        if (empty($plan) || str_starts_with($plan, 'directory')) {
            return false;
        }
        // Plans de despacho: basic, pro, avanzado, trial, exento, etc.
        return true;
    }

    public function getStatsProperty(): array
    {
        $profile = $this->profile();
        $days    = (int) $this->period;

        $base = $profile->analytics()
                         ->where('event_date', '>=', now()->subDays($days)->toDateString());

        $prev = $profile->analytics()
                         ->whereBetween('event_date', [
                             now()->subDays($days * 2)->toDateString(),
                             now()->subDays($days)->toDateString(),
                         ]);

        $views      = (clone $base)->where('event_type', 'profile_view')->count();
        $prevViews  = (clone $prev)->where('event_type', 'profile_view')->count();

        $impressions     = (clone $base)->where('event_type', 'search_impression')->count();
        $prevImpressions = (clone $prev)->where('event_type', 'search_impression')->count();

        $contacts     = (clone $base)->where('event_type', 'whatsapp_click')->count();
        $prevContacts = (clone $prev)->where('event_type', 'whatsapp_click')->count();

        $shares     = (clone $base)->where('event_type', 'share_click')->count();
        $prevShares = (clone $prev)->where('event_type', 'share_click')->count();

        return [
            'views'       => ['value' => $views,       'prev' => $prevViews,       'label' => 'Visitas al perfil',      'icon' => 'eye',    'color' => 'indigo'],
            'impressions' => ['value' => $impressions,  'prev' => $prevImpressions, 'label' => 'Impresiones en búsqueda','icon' => 'search', 'color' => 'purple'],
            'contacts'    => ['value' => $contacts,     'prev' => $prevContacts,    'label' => 'Clics en WhatsApp',      'icon' => 'phone',  'color' => 'emerald'],
            'shares'      => ['value' => $shares,       'prev' => $prevShares,      'label' => 'Compartidos',            'icon' => 'share',  'color' => 'sky'],
        ];
    }

    public function getChartDataProperty(): array
    {
        $profile = $this->profile();
        $days    = (int) $this->period;

        $rows = $profile->analytics()
            ->where('event_date', '>=', now()->subDays($days)->toDateString())
            ->selectRaw("event_date, event_type, count(*) as total")
            ->groupBy('event_date', 'event_type')
            ->orderBy('event_date')
            ->get();

        $dates = [];
        $views_data = $impressions_data = $contacts_data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $dates[] = now()->subDays($i)->format('d/m');
        }

        foreach ($dates as $label) {
            $dateStr = \Carbon\Carbon::createFromFormat('d/m', $label)->format('Y-m-d');
            $dayRows = $rows->where('event_date', $dateStr);

            $views_data[]       = $dayRows->where('event_type', 'profile_view')->first()->total ?? 0;
            $impressions_data[] = $dayRows->where('event_type', 'search_impression')->first()->total ?? 0;
            $contacts_data[]    = $dayRows->where('event_type', 'whatsapp_click')->first()->total ?? 0;
        }

        return compact('dates', 'views_data', 'impressions_data', 'contacts_data');
    }

    public function getPaymentsProperty()
    {
        return $this->profile()->payments()->latest()->get();
    }

    public function getProfileCompletionProperty(): array
    {
        $profile = $this->profile();
        $fields  = [
            'Foto de perfil'   => !is_null($profile->profile_photo_path),
            'Nombre / Titular' => !empty($profile->headline),
            'Biografía'        => !empty($profile->bio),
            'Especialidades'   => !empty($profile->specialties),
            'Ciudad'           => !empty($profile->city),
            'WhatsApp'         => !empty($profile->whatsapp),
            'Perfil público'   => $profile->is_public,
            'LinkedIn / Web'   => !empty($profile->linkedin) || !empty($profile->website),
        ];

        $completed = collect($fields)->filter()->count();
        $total     = count($fields);
        $pct       = round(($completed / $total) * 100);

        return ['fields' => $fields, 'pct' => $pct, 'completed' => $completed, 'total' => $total];
    }

    public function getTotalsProperty()
    {
        $profile = $this->profile();
        return [
            'views'       => $profile->views_count,
            'contacts'    => $profile->contact_clicks_count,
            'impressions' => $profile->search_impressions_count,
            'shares'      => $profile->share_clicks_count,
        ];
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.directory.dashboard', [
            'profile'       => $this->profile()->load('user', 'user.tenant'),
            'stats'         => $this->stats,
            'chartData'     => $this->chartData,
            'payments'      => $this->payments,
            'completion'    => $this->profileCompletion,
            'totals'        => $this->totals,
            'isDespachoUser'=> $this->isDespachoUser,
        ]);
    }
}
