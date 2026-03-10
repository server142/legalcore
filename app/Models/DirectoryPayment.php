<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectoryPayment extends Model
{
    protected $fillable = [
        'directory_profile_id',
        'plan',
        'amount',
        'currency',
        'status',
        'reference',
        'method',
        'paid_at',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'paid_at'      => 'datetime',
        'period_start' => 'date',
        'period_end'   => 'date',
        'amount'       => 'decimal:2',
    ];

    public function profile()
    {
        return $this->belongsTo(DirectoryProfile::class, 'directory_profile_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'paid'      => 'Pagado',
            'pending'   => 'Pendiente',
            'cancelled' => 'Cancelado',
            'refunded'  => 'Reembolsado',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'paid'      => 'emerald',
            'pending'   => 'amber',
            'cancelled' => 'red',
            'refunded'  => 'indigo',
            default     => 'gray',
        };
    }

    public function getPlanLabelAttribute(): string
    {
        return match($this->plan) {
            'directory-free'    => 'Plan Gratuito',
            'directory-basic'   => 'Plan Básico',
            'directory-premium' => 'Plan Premium',
            default             => ucfirst($this->plan),
        };
    }
}
