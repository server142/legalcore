<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalAcceptance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'legal_document_id',
        'version',
        'fecha_aceptacion',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'fecha_aceptacion' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function legalDocument()
    {
        return $this->belongsTo(LegalDocument::class);
    }
}
