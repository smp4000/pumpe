<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Actions;

use App\Models\User;
use InvalidArgumentException;
use Modules\ShiftReconciliation\Enums\ShiftStatus;
use Modules\ShiftReconciliation\Models\Shift;

/**
 * Reicht eine offene Abrechnung zur Freigabe ein und friert die
 * Summen der Positionen ein.
 */
final readonly class SubmitShift
{
    public function execute(Shift $shift, User $user): Shift
    {
        if (! $shift->isOpen()) {
            throw new InvalidArgumentException('Nur offene Abrechnungen können eingereicht werden.');
        }

        $shift->recalculateTotals();

        $shift->forceFill([
            'status' => ShiftStatus::Submitted,
            'submitted_at' => now(),
            'submitted_by' => $user->getKey(),
        ])->save();

        return $shift;
    }
}
