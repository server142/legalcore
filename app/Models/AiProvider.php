<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiProvider extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'api_key',
        'default_model',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'api_key' => 'encrypted', // Automatically encrypt/decrypt
        'is_active' => 'boolean',
    ];

    /**
     * Get the active AI provider
     */
    public static function getActive()
    {
        $activeId = \DB::table('global_settings')
            ->where('key', 'active_ai_provider_id')
            ->value('value');

        if ($activeId) {
            return self::find($activeId);
        }

        // Fallback to first active provider
        return self::where('is_active', true)->orderBy('sort_order')->first();
    }

    /**
     * Set this provider as active
     */
    public function setAsActive()
    {
        \DB::table('global_settings')
            ->updateOrInsert(
                ['key' => 'active_ai_provider_id'],
                [
                    'value' => $this->id,
                    'updated_at' => now()
                ]
            );
    }

    /**
     * Scope to get only active providers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get masked API key for display
     */
    public function getMaskedApiKeyAttribute()
    {
        if (empty($this->api_key)) {
            return 'No configurada';
        }

        $key = $this->api_key;
        $length = strlen($key);
        
        if ($length <= 10) {
            return str_repeat('*', $length);
        }

        return substr($key, 0, 7) . str_repeat('*', $length - 10) . substr($key, -3);
    }
}
