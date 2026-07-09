<?php

declare(strict_types=1);

namespace App\Policies;

use App\Authorization\CorePermissions;
use App\Models\User;

/**
 * Zugriffsregeln für die Mitgliederverwaltung im App-Panel.
 * Plattform-Administratoren übergehen alle Checks via Gate::before.
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(CorePermissions::MEMBERS_VIEW);
    }

    public function view(User $user, User $member): bool
    {
        return $user->can(CorePermissions::MEMBERS_VIEW);
    }

    public function create(User $user): bool
    {
        // Neue Konten entstehen nur über Einladungen
        return false;
    }

    public function update(User $user, User $member): bool
    {
        return $user->can(CorePermissions::MEMBERS_UPDATE);
    }

    public function delete(User $user, User $member): bool
    {
        return $user->can(CorePermissions::MEMBERS_REMOVE) && ! $member->is($user);
    }
}
