<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DofPublication extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_publicacion',
        'cod_nota',
        'titulo',
        'resumen',
        'texto_completo',
        'link_pdf',
        'seccion',
        'organismo',
        'embedding_data',
    ];

    protected $casts = [
        'fecha_publicacion' => 'date',
        'embedding_data' => 'array',
    ];

    // Placeholder for semantic search scope
    public function scopeSemanticSearch($query, $vector)
    {
        // This would implement the cosine similarity logic if using pgvector
        // return $query->orderByRaw('embedding <=> ?', [$vector]);
        return $query;
    }
}
