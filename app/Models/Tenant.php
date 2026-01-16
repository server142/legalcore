<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'status',
        'plan',
        'trial_ends_at',
        'is_active',
        'subscription_ends_at',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'trial_ends_at' => 'date',
        'subscription_ends_at' => 'date',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function isOnTrial()
    {
        return $this->plan === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function trialExpired()
    {
        return $this->plan === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    public function daysLeftInTrial()
    {
        if (!$this->isOnTrial()) return 0;
        return now()->diffInDays($this->trial_ends_at, false);
    }
}
