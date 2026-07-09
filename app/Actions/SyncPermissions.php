<?php

declare(strict_types=1);

namespace App\Actions;

use App\Authorization\CorePermissions;
use App\Modules\ModuleManager;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Stellt sicher, dass alle registrierten Berechtigungen in der Datenbank
 * existieren: die Core-Berechtigungen plus alle Berechtigungen, die
 * Module über ihre module.json-Manifeste deklarieren.
 */
final readonly class SyncPermissions
{
    public function __construct(private ModuleManager $moduleManager) {}

    public function execute(): void
    {
        $permissions = [
            ...CorePermissions::all(),
            ...$this->moduleManager->declaredPermissions(),
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
