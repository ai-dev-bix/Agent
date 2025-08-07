<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'credits',
        'free_messages_used',
        'free_messages_limit',
        'last_free_message_reset',
        'is_admin',
        'preferences'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_free_message_reset' => 'datetime',
            'is_admin' => 'boolean',
            'preferences' => 'array'
        ];
    }

    public function aiAgents(): HasMany
    {
        return $this->hasMany(AiAgent::class, 'created_by');
    }

    public function chatThreads(): HasMany
    {
        return $this->hasMany(ChatThread::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function creditHistory(): HasMany
    {
        return $this->hasMany(UserCredit::class);
    }

    public function canSendFreeMessage(): bool
    {
        // Reset daily free messages if needed
        if ($this->last_free_message_reset?->isYesterday() || $this->last_free_message_reset === null) {
            $this->update([
                'free_messages_used' => 0,
                'last_free_message_reset' => now()
            ]);
            return true;
        }

        return $this->free_messages_used < $this->free_messages_limit;
    }

    public function hasCredits(int $amount = 1): bool
    {
        return $this->credits >= $amount;
    }

    public function deductCredits(int $amount, string $description, string $referenceType = null, int $referenceId = null): bool
    {
        if (!$this->hasCredits($amount)) {
            return false;
        }

        $this->decrement('credits', $amount);
        
        UserCredit::create([
            'user_id' => $this->id,
            'type' => 'usage',
            'credits' => -$amount,
            'balance_after' => $this->fresh()->credits,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId
        ]);

        return true;
    }

    public function addCredits(int $amount, string $description, string $type = 'purchase', string $referenceType = null, int $referenceId = null): void
    {
        $this->increment('credits', $amount);
        
        UserCredit::create([
            'user_id' => $this->id,
            'type' => $type,
            'credits' => $amount,
            'balance_after' => $this->fresh()->credits,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId
        ]);
    }
}
