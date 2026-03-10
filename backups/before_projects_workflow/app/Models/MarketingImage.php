<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class MarketingImage extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'prompt',
        'revised_prompt',
        'style',
        'size',
        'file_path',
        'provider',
        'cost',
    ];

    /**
     * Get the public URL of the generated image.
     */
    public function getUrlAttribute()
    {
        if (filter_var($this->file_path, FILTER_VALIDATE_URL)) {
            return $this->file_path;
        }

        return Storage::url($this->file_path);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
