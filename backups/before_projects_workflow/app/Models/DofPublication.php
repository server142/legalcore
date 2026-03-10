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
    // Helper to calculate cosine similarity between two vectors
    public static function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($vecA as $key => $value) {
            $valB = $vecB[$key] ?? 0;
            $dotProduct += $value * $valB;
            $normA += $value * $value;
            $normB += $valB * $valB;
        }

        if ($normA == 0 || $normB == 0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
