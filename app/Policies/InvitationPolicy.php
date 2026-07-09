<?php

declare(strict_types=1);

namespace App\Policies;

use App\Authorization\CorePermissions;
use App\Models\Invitation;
use App\Models\User;

/**
 * Zugriffsregeln für Einladungen im App-Panel.
 */
class InvitationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(CorePermissions::MEMBERS_VIEW);
    }

    public function view(User $user, Invitation $invitation): bool
    {
        return $user->can(CorePermissions::MEMBERS_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->can(CorePermissions::MEMBERS_INVITE);
    }

    public function update(User $user, Invitation $invitation): bool
    {
        return $user->can(CorePermissions::MEMBERS_INVITE);
    }

    public function delete(User $user, Invitation $invitation): bool
    {
        return $user->can(CorePermissions::MEMBERS_INVITE);
    }
}
