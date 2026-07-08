<?php

declare(strict_types=1);

namespace App\Models;

use App\Tenancy\Concerns\BelongsToTenant;
use Database\Factories\InvitationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Einladung eines Benutzers in eine Organization mit vorbestimmter Rolle.
 *
 * @property string $email
 * @property string $role
 * @property string $token
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $accepted_at
 */
class Invitation extends Model
{
    use BelongsToTenant;

    /** @use HasFactory<InvitationFactory> */
    use HasFactory;

    use HasUlids;

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'email',
        'role',
        'employee_id',
        'invited_by',
        'token',
        'expires_at',
        'accepted_at',
    ];

    /** @var list<string> */
    protected $hidden = [
        'token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invitation $invitation): void {
            if (blank($invitation->token)) {
                $invitation->token = Str::random(64);
            }

            if (blank($invitation->expires_at)) {
                // Einladungen verfallen nach 7 Tagen
                $invitation->expires_at = now()->addDays(7);
            }
        });
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isPending(): bool
    {
        return $this->accepted_at === null && ! $this->isExpired();
    }
}
