<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_thread_id',
        'role',
        'content',
        'tokens_used',
        'prompt_tokens',
        'completion_tokens',
        'cost',
        'model_used',
        'metadata'
    ];

    protected $casts = [
        'cost' => 'decimal:6',
        'metadata' => 'array'
    ];

    public function chatThread(): BelongsTo
    {
        return $this->belongsTo(ChatThread::class);
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isAssistant(): bool
    {
        return $this->role === 'assistant';
    }

    public function isSystem(): bool
    {
        return $this->role === 'system';
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($message) {
            $message->chatThread->updateActivity();
        });
    }
}
