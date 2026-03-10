<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\DirectoryAnalytic;
use App\Models\DirectoryPayment;

class DirectoryProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'slug',
        'headline',
        'bio',
        'specialties',
        'city',
        'state',
        'professional_license',
        'is_verified',
        'whatsapp',
        'website',
        'linkedin',
        'profile_photo_path',
        'is_public',
        'views_count',
        'contact_clicks_count',
    ];

    protected $casts = [
        'specialties' => 'array',
        'is_public' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            // Usar asset() que respeta el host real en lugar de Storage::url()
            // que depende de APP_URL del .env
            return asset('storage/' . $this->profile_photo_path);
        }

        return $this->defaultProfilePhotoUrl();
    }

    protected function defaultProfilePhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->user->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }

    // Auto-generate slug on create if empty
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($profile) {
            if (empty($profile->slug)) {
                $baseSlug = Str::slug($profile->user->name);
                $profile->slug = $baseSlug;
                
                // Simple check for uniqueness, could be improved with a loop if many users have same name
                if (static::where('slug', $baseSlug)->exists()) {
                    $profile->slug = $baseSlug . '-' . Str::random(4);
                }
            }
        });
    }

    public function analytics()
    {
        return $this->hasMany(DirectoryAnalytic::class);
    }

    public function payments()
    {
        return $this->hasMany(DirectoryPayment::class);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function trackEvent(string $type, ?string $query = null): void
    {
        $this->analytics()->create([
            'event_type'   => $type,
            'ip_address'   => request()->ip(),
            'session_id'   => session()->getId(),
            'search_query' => $query,
            'event_date'   => today(),
        ]);

        // Actualizar contadores acumulados
        match ($type) {
            'profile_view'      => $this->increment('views_count'),
            'whatsapp_click'    => $this->increment('contact_clicks_count'),
            'search_impression' => $this->increment('search_impressions_count'),
            'share_click'       => $this->increment('share_clicks_count'),
            default             => null,
        };
    }
}
