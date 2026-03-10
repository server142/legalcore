<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectoryAnalytic extends Model
{
    protected $fillable = [
        'directory_profile_id',
        'event_type',
        'ip_address',
        'session_id',
        'search_query',
        'event_date',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function profile()
    {
        return $this->belongsTo(DirectoryProfile::class, 'directory_profile_id');
    }

    // ─── Scopes ────────────────────────────────────────────────────────
    public function scopeViews($q)       { return $q->where('event_type', 'profile_view'); }
    public function scopeImpressions($q) { return $q->where('event_type', 'search_impression'); }
    public function scopeContacts($q)    { return $q->where('event_type', 'whatsapp_click'); }
    public function scopeShares($q)      { return $q->where('event_type', 'share_click'); }

    public function scopeThisMonth($q)
    {
        return $q->whereMonth('event_date', now()->month)
                 ->whereYear('event_date', now()->year);
    }

    public function scopeThisWeek($q)
    {
        return $q->whereBetween('event_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeLast30Days($q)
    {
        return $q->where('event_date', '>=', now()->subDays(30)->toDateString());
    }

    public function scopeLast7Days($q)
    {
        return $q->where('event_date', '>=', now()->subDays(7)->toDateString());
    }
}
