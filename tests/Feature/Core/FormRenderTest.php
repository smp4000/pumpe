<?php

declare(strict_types=1);

use App\Actions\CreateOrganization;
use App\Filament\App\Resources\Employees\EmployeeResource;
use App\Filament\App\Resources\Stations\StationResource;
use App\Filament\Resources\Organizations\OrganizationResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
 * Render-Tests der Formularseiten: bauen die Filament-Schemas (Tab-Layout)
 * über echte HTTP-Requests auf und fangen damit API-Fehler in den
 * Formulardefinitionen ab, die Unit-Tests nicht sehen.
 */

it('rendert die Stations- und Mitarbeiter-Formulare im App-Panel', function (): void {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->execute($owner, ['name' => 'Betrieb A'], 'Station Nord');

    $this->actingAs($owner);

    $this->get(StationResource::getUrl('create', tenant: $organization))
        ->assertOk();

    $this->get(EmployeeResource::getUrl('create', tenant: $organization))
        ->assertOk();
});

it('rendert das Betriebe-Formular im Betreiber-Panel', function (): void {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->execute($owner, ['name' => 'Betrieb A']);

    $admin = User::factory()->create(['is_platform_admin' => true]);

    $this->actingAs($admin);

    $this->get(OrganizationResource::getUrl('edit', ['record' => $organization], panel: 'admin'))
        ->assertOk();
});
