<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\CreateOrganization;
use App\Actions\LicenseModule;
use App\Enums\LicenseStatus;
use App\Models\Employee;
use App\Models\Module;
use App\Models\User;
use App\Tenancy\CurrentTenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

/**
 * Entwicklungs-Seed: Plattform-Admin, Demo-Organization mit Inhaber,
 * Station und Mitarbeitern.
 *
 * Zugangsdaten (nur lokal):
 * - Betreiber:  admin@pumpe.test / password  → /admin
 * - Inhaber:    demo@pumpe.test  / password  → /app
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Module aus den Manifesten in die Datenbank übernehmen
        Artisan::call('modules:sync');

        User::factory()->create([
            'name' => 'Plattform-Admin',
            'email' => 'admin@pumpe.test',
            'is_platform_admin' => true,
        ]);

        $owner = User::factory()->create([
            'name' => 'Max Mustermann',
            'email' => 'demo@pumpe.test',
        ]);

        $organization = app(CreateOrganization::class)->execute(
            owner: $owner,
            attributes: [
                'name' => 'Muster Tankstellen GmbH',
                'legal_name' => 'Muster Tankstellen GmbH',
                'city' => 'Musterstadt',
                'country_code' => 'DE',
            ],
            stationName: 'Station Musterstadt',
        );

        $tenant = app(CurrentTenant::class);
        $tenant->set($organization);

        $station = $organization->stations()->first();

        Employee::factory()
            ->count(5)
            ->create(['station_id' => $station?->getKey()]);

        // Demo-Betrieb erhält das Referenzmodul als aktive Lizenz
        $playground = Module::query()->where('code', 'playground')->first();

        if ($playground !== null) {
            app(LicenseModule::class)->execute($organization, $playground, LicenseStatus::Active);
        }

        $tenant->forget();
    }
}
