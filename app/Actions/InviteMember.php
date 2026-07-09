<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Employee;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\InvitationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * Lädt eine Person per E-Mail in die aktuelle Organization ein.
 * Eine noch offene Einladung an dieselbe Adresse wird ersetzt.
 */
final readonly class InviteMember
{
    public function execute(
        string $email,
        string $role,
        ?Employee $employee = null,
        ?User $invitedBy = null,
    ): Invitation {
        $email = mb_strtolower(trim($email));

        $invitation = DB::transaction(function () use ($email, $role, $employee, $invitedBy): Invitation {
            // Offene Einladungen an dieselbe Adresse ersetzen (tenant-scoped)
            Invitation::query()
                ->where('email', $email)
                ->whereNull('accepted_at')
                ->delete();

            return Invitation::create([
                'email' => $email,
                'role' => $role,
                'employee_id' => $employee?->getKey(),
                'invited_by' => $invitedBy?->getKey(),
            ]);
        });

        Notification::route('mail', $email)->notify(new InvitationNotification($invitation));

        return $invitation;
    }
}
