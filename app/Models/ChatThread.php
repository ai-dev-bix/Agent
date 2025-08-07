<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChatThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'ai_agent_id',
        'user_id',
        'title',
        'is_public',
        'public_token',
        'message_count',
        'total_tokens_used',
        'last_activity_at',
        'metadata'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'last_activity_at' => 'datetime',
        'metadata' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($thread) {
            if (empty($thread->uuid)) {
                $thread->uuid = Str::uuid();
            }
            if ($thread->is_public && empty($thread->public_token)) {
                $thread->public_token = Str::random(32);
            }
        });
    }

    public function aiAgent(): BelongsTo
    {
        return $this->belongsTo(AiAgent::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function updateActivity()
    {
        $this->update([
            'last_activity_at' => now(),
            'message_count' => $this->messages()->count()
        ]);
    }
}
