<?php

declare(strict_types=1);

namespace App\Policies;

use App\Authorization\CorePermissions;
use App\Models\Employee;
use App\Models\User;

/**
 * Zugriffsregeln für Personalstammsätze. Die Tenant-Trennung übernimmt der
 * TenantScope — hier geht es nur um Berechtigungen innerhalb des Tenants.
 */
class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(CorePermissions::EMPLOYEES_VIEW);
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->can(CorePermissions::EMPLOYEES_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->can(CorePermissions::EMPLOYEES_CREATE);
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->can(CorePermissions::EMPLOYEES_UPDATE);
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->can(CorePermissions::EMPLOYEES_DELETE);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can(CorePermissions::EMPLOYEES_DELETE);
    }

    public function restore(User $user, Employee $employee): bool
    {
        return $user->can(CorePermissions::EMPLOYEES_DELETE);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can(CorePermissions::EMPLOYEES_DELETE);
    }

    public function forceDelete(User $user, Employee $employee): bool
    {
        return $user->can(CorePermissions::EMPLOYEES_DELETE);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can(CorePermissions::EMPLOYEES_DELETE);
    }
}
