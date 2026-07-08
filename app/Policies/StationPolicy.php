<?php

declare(strict_types=1);

namespace App\Policies;

use App\Authorization\CorePermissions;
use App\Models\Station;
use App\Models\User;

/**
 * Zugriffsregeln für Stationen. Die Tenant-Trennung übernimmt der
 * TenantScope — hier geht es nur um Berechtigungen innerhalb des Tenants.
 */
class StationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(CorePermissions::STATIONS_VIEW);
    }

    public function view(User $user, Station $station): bool
    {
        return $user->can(CorePermissions::STATIONS_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->can(CorePermissions::STATIONS_CREATE);
    }

    public function update(User $user, Station $station): bool
    {
        return $user->can(CorePermissions::STATIONS_UPDATE);
    }

    public function delete(User $user, Station $station): bool
    {
        return $user->can(CorePermissions::STATIONS_DELETE);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can(CorePermissions::STATIONS_DELETE);
    }

    public function restore(User $user, Station $station): bool
    {
        return $user->can(CorePermissions::STATIONS_DELETE);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can(CorePermissions::STATIONS_DELETE);
    }

    public function forceDelete(User $user, Station $station): bool
    {
        return $user->can(CorePermissions::STATIONS_DELETE);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can(CorePermissions::STATIONS_DELETE);
    }
}
