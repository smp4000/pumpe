<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Invitation;
use App\Models\User;
use App\Tenancy\CurrentTenant;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Nimmt eine Einladung an: Mitgliedschaft anlegen, Rolle zuweisen,
 * optional den Personalstammsatz mit dem Konto verknüpfen.
 */
final readonly class AcceptInvitation
{
    public function __construct(private CurrentTenant $currentTenant) {}

    public function execute(Invitation $invitation, User $user): void
    {
        if (! $invitation->isPending()) {
            throw new InvalidArgumentException('Die Einladung ist abgelaufen oder bereits angenommen.');
        }

        DB::transaction(function () use ($invitation, $user): void {
            $invitation->loadMissing('organization');
            $organization = $invitation->organization;

            if (! $organization->users()->whereKey($user->getKey())->exists()) {
                $organization->users()->attach($user);
            }

            $previousTenant = $this->currentTenant->get();
            $this->currentTenant->set($organization);

            try {
                $user->assignRole($invitation->role);

                // Personalstammsatz verknüpfen, falls hinterlegt und noch frei
                // (Laden erst hier — der Employee-Query braucht den Tenant-Kontext)
                $invitation->loadMissing('employee');
                $employee = $invitation->employee;

                if ($employee !== null && $employee->user_id === null) {
                    $employee->update(['user_id' => $user->getKey()]);
                }

                $invitation->forceFill(['accepted_at' => now()])->save();
            } finally {
                $previousTenant !== null
                    ? $this->currentTenant->set($previousTenant)
                    : $this->currentTenant->forget();
            }
        });
    }
}
