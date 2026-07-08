<?php

declare(strict_types=1);

namespace App\Actions;

use App\Authorization\CorePermissions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Stellt sicher, dass alle registrierten Berechtigungen in der Datenbank
 * existieren. Berechtigungen sind systemweit (nicht tenant-spezifisch);
 * Fachmodule erweitern die Liste später über ihre ServiceProvider.
 */
final readonly class SyncPermissions
{
    public function execute(): void
    {
        foreach (CorePermissions::all() as $permission) {
            Permission::findOrCreate($permission);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
