<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class Documento extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'expediente_id',
        'nombre',
        'path',
        'extension',
        'tipo',
        'version',
        'size',
        'uploaded_by',
        'extracted_text',
        'processing_status',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
