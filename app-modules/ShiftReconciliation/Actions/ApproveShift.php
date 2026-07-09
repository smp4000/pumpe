<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Actions;

use App\Models\User;
use InvalidArgumentException;
use Modules\ShiftReconciliation\Enums\ShiftStatus;
use Modules\ShiftReconciliation\Events\ShiftApproved;
use Modules\ShiftReconciliation\Models\Shift;

/**
 * Gibt eine eingereichte Abrechnung frei. Ab hier ist sie unveränderlich
 * (GoBD) — Korrekturen nur noch über Storno plus neue Abrechnung.
 */
final readonly class ApproveShift
{
    public function execute(Shift $shift, User $user): Shift
    {
        if (! $shift->isSubmitted()) {
            throw new InvalidArgumentException('Nur eingereichte Abrechnungen können freigegeben werden.');
        }

        $shift->recalculateTotals();

        $shift->forceFill([
            'status' => ShiftStatus::Approved,
            'approved_at' => now(),
            'approved_by' => $user->getKey(),
        ])->save();

        ShiftApproved::dispatch($shift);

        return $shift;
    }
}
