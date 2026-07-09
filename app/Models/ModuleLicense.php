<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LicenseStatus;
use App\Tenancy\Concerns\BelongsToTenant;
use Database\Factories\ModuleLicenseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Lizenz eines Moduls für eine Organization.
 *
 * @property LicenseStatus $status
 * @property \Illuminate\Support\Carbon|null $trial_ends_at
 * @property \Illuminate\Support\Carbon|null $activated_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 */
class ModuleLicense extends Model
{
    use BelongsToTenant;

    /** @use HasFactory<ModuleLicenseFactory> */
    use HasFactory;

    use HasUlids;

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'module_id',
        'status',
        'trial_ends_at',
        'activated_at',
        'expires_at',
        'cancelled_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => LicenseStatus::class,
            'trial_ends_at' => 'datetime',
            'activated_at' => 'datetime',
            'expires_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Module, $this>
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Ist die Lizenz zum jetzigen Zeitpunkt nutzbar?
     */
    public function isUsable(): bool
    {
        return match ($this->status) {
            LicenseStatus::Trial => $this->trial_ends_at === null || $this->trial_ends_at->isFuture(),
            LicenseStatus::Active,
            LicenseStatus::Cancelled => $this->expires_at === null || $this->expires_at->isFuture(),
            LicenseStatus::Expired => false,
        };
    }
}
