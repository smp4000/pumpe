<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\CreateOrganization;
use App\Models\Employee;
use App\Models\User;
use App\Tenancy\CurrentTenant;
use Illuminate\Database\Seeder;

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

        $tenant->forget();
    }
}
