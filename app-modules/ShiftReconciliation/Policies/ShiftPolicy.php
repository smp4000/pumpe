<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Policies;

use App\Models\User;
use Modules\ShiftReconciliation\Models\Shift;

/**
 * Berechtigungen der Schichtabrechnung. Bearbeiten und Löschen sind
 * zusätzlich an den Entwurfsstatus gebunden — der harte Schutz sitzt
 * im Model (ShiftImmutableException), die Policy steuert das UI.
 */
class ShiftPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('shift-reconciliation.shifts.view');
    }

    public function view(User $user, Shift $shift): bool
    {
        return $user->can('shift-reconciliation.shifts.view');
    }

    public function create(User $user): bool
    {
        return $user->can('shift-reconciliation.shifts.create');
    }

    public function update(User $user, Shift $shift): bool
    {
        return $user->can('shift-reconciliation.shifts.update') && $shift->isOpen();
    }

    public function delete(User $user, Shift $shift): bool
    {
        return $user->can('shift-reconciliation.shifts.delete') && $shift->isOpen();
    }

    public function submit(User $user, Shift $shift): bool
    {
        return $user->can('shift-reconciliation.shifts.submit') && $shift->isOpen();
    }

    public function approve(User $user, Shift $shift): bool
    {
        return $user->can('shift-reconciliation.shifts.approve') && $shift->isSubmitted();
    }

    public function cancel(User $user, Shift $shift): bool
    {
        return $user->can('shift-reconciliation.shifts.cancel') && $shift->isApproved();
    }
}
