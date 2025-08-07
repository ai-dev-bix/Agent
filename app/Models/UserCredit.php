<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'credits',
        'balance_after',
        'description',
        'reference_type',
        'reference_id',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isDeduction(): bool
    {
        return $this->credits < 0;
    }

    public function isAddition(): bool
    {
        return $this->credits > 0;
    }

    public function scopeDeductions($query)
    {
        return $query->where('credits', '<', 0);
    }

    public function scopeAdditions($query)
    {
        return $query->where('credits', '>', 0);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
