<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ai_chat_id',
        'role',
        'content',
        'attachments'
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function chat()
    {
        return $this->belongsTo(AiChat::class, 'ai_chat_id');
    }
}
