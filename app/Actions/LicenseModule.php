<?php

declare(strict_types=1);

namespace App\Actions;

use App\Authorization\RoleTemplates;
use App\Enums\LicenseStatus;
use App\Models\Module;
use App\Models\ModuleLicense;
use App\Models\Organization;
use App\Modules\ModuleManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Bucht ein Modul für eine Organization (bzw. aktualisiert die Lizenz)
 * und gibt der Inhaber-Rolle die Berechtigungen des Moduls. Wird vom
 * Betreiber-Panel genutzt; später docken Billing-Provider hier an.
 */
final readonly class LicenseModule
{
    public function __construct(
        private ModuleManager $moduleManager,
        private SyncPermissions $syncPermissions,
    ) {}

    public function execute(
        Organization $organization,
        Module $module,
        LicenseStatus $status,
        ?Carbon $trialEndsAt = null,
        ?Carbon $expiresAt = null,
    ): ModuleLicense {
        return DB::transaction(function () use ($organization, $module, $status, $trialEndsAt, $expiresAt): ModuleLicense {
            $license = ModuleLicense::withoutTenancy()->updateOrCreate(
                [
                    'organization_id' => $organization->getKey(),
                    'module_id' => $module->getKey(),
                ],
                [
                    'status' => $status,
                    'trial_ends_at' => $trialEndsAt,
                    'expires_at' => $expiresAt,
                    'activated_at' => $status === LicenseStatus::Active ? now() : null,
                    'cancelled_at' => $status === LicenseStatus::Cancelled ? now() : null,
                ],
            );

            $this->grantModulePermissionsToOwner($organization, $module);

            $this->moduleManager->flushLicenseCache();

            return $license;
        });
    }

    /**
     * Die Inhaber-Rolle der Organization erhält alle Berechtigungen des
     * Moduls. Feinere Verteilung nehmen die Tenants selbst über die
     * Rollenverwaltung vor.
     */
    private function grantModulePermissionsToOwner(Organization $organization, Module $module): void
    {
        $manifest = $this->moduleManager->manifest($module->code);

        if ($manifest === null || $manifest->permissions === []) {
            return;
        }

        $this->syncPermissions->execute();

        /** @var Role|null $ownerRole */
        $ownerRole = Role::query()
            ->where('organization_id', $organization->getKey())
            ->where('name', RoleTemplates::OWNER)
            ->first();

        $ownerRole?->givePermissionTo($manifest->permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
