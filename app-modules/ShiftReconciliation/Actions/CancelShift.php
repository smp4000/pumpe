<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Actions;

use App\Models\User;
use InvalidArgumentException;
use Modules\ShiftReconciliation\Enums\ShiftStatus;
use Modules\ShiftReconciliation\Models\Shift;

/**
 * Storniert eine freigegebene Abrechnung mit Begründung (GoBD:
 * Storno statt Änderung). Die stornierte Abrechnung bleibt als
 * Beleg erhalten; die Korrektur erfolgt über eine neue Abrechnung.
 */
final readonly class CancelShift
{
    public function execute(Shift $shift, User $user, string $reason): Shift
    {
        if (! $shift->isApproved()) {
            throw new InvalidArgumentException('Nur freigegebene Abrechnungen können storniert werden.');
        }

        if (trim($reason) === '') {
            throw new InvalidArgumentException('Ein Storno erfordert eine Begründung.');
        }

        $shift->forceFill([
            'status' => ShiftStatus::Cancelled,
            'cancelled_at' => now(),
            'cancelled_by' => $user->getKey(),
            'cancel_reason' => $reason,
        ])->save();

        return $shift;
    }
}
