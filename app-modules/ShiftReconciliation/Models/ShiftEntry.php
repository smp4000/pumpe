<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Models;

use App\Tenancy\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ShiftReconciliation\Database\Factories\ShiftEntryFactory;
use Modules\ShiftReconciliation\Enums\PaymentMethod;
use Modules\ShiftReconciliation\Enums\ShiftStatus;
use Modules\ShiftReconciliation\Exceptions\ShiftImmutableException;

/**
 * Soll/Ist-Position einer Schichtabrechnung je Zahlart.
 * Positionen sind nur bearbeitbar, solange die Abrechnung offen ist.
 *
 * @property PaymentMethod $payment_method
 * @property int $expected_amount_cents
 * @property int $counted_amount_cents
 * @property string|null $note
 */
class ShiftEntry extends Model
{
    use BelongsToTenant;

    /** @use HasFactory<ShiftEntryFactory> */
    use HasFactory;

    use HasUlids;

    protected $table = 'shift_reconciliation_entries';

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'shift_id',
        'payment_method',
        'expected_amount_cents',
        'counted_amount_cents',
        'note',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payment_method' => PaymentMethod::class,
            'expected_amount_cents' => 'integer',
            'counted_amount_cents' => 'integer',
        ];
    }

    protected static function newFactory(): ShiftEntryFactory
    {
        return ShiftEntryFactory::new();
    }

    protected static function booted(): void
    {
        // Positionen erben den Unveränderlichkeits-Schutz der Abrechnung
        $guard = function (ShiftEntry $entry): void {
            $shift = $entry->shift()->first();

            if ($shift !== null && $shift->status !== ShiftStatus::Open) {
                throw ShiftImmutableException::entries($shift);
            }
        };

        static::creating($guard);
        static::updating($guard);
        static::deleting($guard);
    }

    /**
     * @return BelongsTo<Shift, $this>
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
