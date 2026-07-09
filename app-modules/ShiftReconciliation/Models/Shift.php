<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Models;

use App\Models\Employee;
use App\Models\Station;
use App\Models\User;
use App\Tenancy\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ShiftReconciliation\Database\Factories\ShiftFactory;
use Modules\ShiftReconciliation\Enums\ShiftStatus;
use Modules\ShiftReconciliation\Exceptions\ShiftImmutableException;

/**
 * Schichtabrechnung. Freigegebene und stornierte Abrechnungen sind
 * unveränderlich (GoBD) — Korrekturen laufen über Storno plus neue
 * Abrechnung. Der Schutz ist im Model verankert, nicht nur im UI.
 *
 * @property ShiftStatus $status
 * @property \Illuminate\Support\Carbon $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property int $expected_total_cents
 * @property int $counted_total_cents
 * @property int $difference_cents
 * @property string|null $notes
 * @property string|null $cancel_reason
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 */
class Shift extends Model
{
    use BelongsToTenant;

    /** @use HasFactory<ShiftFactory> */
    use HasFactory;

    use HasUlids;

    protected $table = 'shift_reconciliation_shifts';

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'station_id',
        'employee_id',
        'starts_at',
        'ends_at',
        'status',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ShiftStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'expected_total_cents' => 'integer',
            'counted_total_cents' => 'integer',
            'difference_cents' => 'integer',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    protected static function newFactory(): ShiftFactory
    {
        return ShiftFactory::new();
    }

    protected static function booted(): void
    {
        // GoBD-Schutz: freigegebene Abrechnungen dürfen nur noch storniert
        // werden, stornierte gar nicht mehr verändert. Gelöscht werden darf
        // ausschließlich der Entwurfsstatus.
        static::updating(function (Shift $shift): void {
            /** @var ShiftStatus $originalStatus */
            $originalStatus = $shift->getOriginal('status');

            if ($originalStatus === ShiftStatus::Cancelled) {
                throw ShiftImmutableException::cancelled($shift);
            }

            if ($originalStatus === ShiftStatus::Approved && $shift->status !== ShiftStatus::Cancelled) {
                throw ShiftImmutableException::approved($shift);
            }
        });

        static::deleting(function (Shift $shift): void {
            if ($shift->status !== ShiftStatus::Open) {
                throw ShiftImmutableException::notDeletable($shift);
            }
        });
    }

    /**
     * @return BelongsTo<Station, $this>
     */
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * @return HasMany<ShiftEntry, $this>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(ShiftEntry::class, 'shift_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isOpen(): bool
    {
        return $this->status === ShiftStatus::Open;
    }

    public function isSubmitted(): bool
    {
        return $this->status === ShiftStatus::Submitted;
    }

    public function isApproved(): bool
    {
        return $this->status === ShiftStatus::Approved;
    }

    /**
     * Aggregiert die Positionen in die eingefrorenen Summenfelder.
     * saveQuietly, damit der Unveränderlichkeits-Guard und Events nicht
     * durch interne Neuberechnungen ausgelöst werden.
     */
    public function recalculateTotals(): void
    {
        $expected = (int) $this->entries()->sum('expected_amount_cents');
        $counted = (int) $this->entries()->sum('counted_amount_cents');

        $this->forceFill([
            'expected_total_cents' => $expected,
            'counted_total_cents' => $counted,
            'difference_cents' => $counted - $expected,
        ])->saveQuietly();
    }
}
