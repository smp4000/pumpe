<?php

declare(strict_types=1);

namespace App\Actions;

use App\Authorization\RoleTemplates;
use App\Models\Organization;
use App\Models\Station;
use App\Models\User;
use App\Tenancy\CurrentTenant;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * Legt eine neue Organization an: Stammdaten, Rollen aus den Vorlagen,
 * Mitgliedschaft und Inhaber-Rolle für den Gründer, optional die erste
 * Station. Wird von der Registrierung (App-Panel) und vom Betreiber-Panel
 * gleichermaßen genutzt.
 */
final readonly class CreateOrganization
{
    public function __construct(
        private CurrentTenant $currentTenant,
        private SyncPermissions $syncPermissions,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes  Stammdaten der Organization
     */
    public function execute(User $owner, array $attributes, ?string $stationName = null): Organization
    {
        return DB::transaction(function () use ($owner, $attributes, $stationName): Organization {
            $previousTenant = $this->currentTenant->get();

            $organization = Organization::create($attributes);
            $organization->users()->attach($owner);

            // Tenant-Kontext setzen, damit Rollen und Station der neuen
            // Organization zugeordnet werden (Teams-Feature, TenantScope).
            $this->currentTenant->set($organization);

            try {
                $this->syncPermissions->execute();

                foreach (RoleTemplates::all() as $roleName => $permissions) {
                    /** @var Role $role */
                    $role = Role::findOrCreate($roleName);
                    $role->syncPermissions($permissions);
                }

                $owner->assignRole(RoleTemplates::OWNER);

                if ($stationName !== null && $stationName !== '') {
                    Station::create(['name' => $stationName]);
                }
            } finally {
                $previousTenant !== null
                    ? $this->currentTenant->set($previousTenant)
                    : $this->currentTenant->forget();
            }

            return $organization;
        });
    }
}
