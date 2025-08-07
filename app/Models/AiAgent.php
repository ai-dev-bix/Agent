<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AiAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'system_prompt',
        'model',
        'temperature',
        'top_p',
        'frequency_penalty',
        'presence_penalty',
        'max_tokens',
        'avatar',
        'welcome_message',
        'category',
        'output_language',
        'tone',
        'writing_style',
        'is_public',
        'is_active',
        'metadata',
        'created_by'
    ];

    protected $casts = [
        'temperature' => 'decimal:2',
        'top_p' => 'decimal:2',
        'frequency_penalty' => 'decimal:2',
        'presence_penalty' => 'decimal:2',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($agent) {
            if (empty($agent->slug)) {
                $agent->slug = Str::slug($agent->name);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function chatThreads(): HasMany
    {
        return $this->hasMany(ChatThread::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
