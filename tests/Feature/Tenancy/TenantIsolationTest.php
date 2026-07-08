<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Organization;
use App\Models\Station;
use App\Tenancy\CurrentTenant;
use App\Tenancy\Exceptions\MissingTenantContextException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
 * Diese Tests beweisen die Tenant-Isolation (ADR-0001): Daten einer
 * Organization sind für andere Organizations unter keinen Umständen
 * sichtbar, und ohne Tenant-Kontext schlagen Zugriffe fehl (fail-closed).
 */

it('filtert Stationen auf die aktuelle Organization', function (): void {
    $organizationA = Organization::factory()->create();
    $organizationB = Organization::factory()->create();

    $stationA = Station::factory()->for($organizationA)->create();
    $stationB = Station::factory()->for($organizationB)->create();

    app(CurrentTenant::class)->set($organizationA);

    expect(Station::query()->pluck('id')->all())->toBe([$stationA->id]);

    app(CurrentTenant::class)->set($organizationB);

    expect(Station::query()->pluck('id')->all())->toBe([$stationB->id]);
});

it('filtert Mitarbeiter auf die aktuelle Organization', function (): void {
    $organizationA = Organization::factory()->create();
    $organizationB = Organization::factory()->create();

    $employeeA = Employee::factory()->for($organizationA)->create();
    Employee::factory()->for($organizationB)->create();

    app(CurrentTenant::class)->set($organizationA);

    expect(Employee::query()->pluck('id')->all())->toBe([$employeeA->id]);
});

it('wirft ohne Tenant-Kontext beim Lesen eine Exception (fail-closed)', function (): void {
    Station::factory()->create();

    expect(fn () => Station::all())->toThrow(MissingTenantContextException::class);
});

it('wirft ohne Tenant-Kontext beim Erstellen ohne organization_id eine Exception', function (): void {
    expect(fn () => Station::create(['name' => 'Station ohne Tenant']))
        ->toThrow(MissingTenantContextException::class);
});

it('setzt organization_id beim Erstellen automatisch aus dem Kontext', function (): void {
    $organization = Organization::factory()->create();

    app(CurrentTenant::class)->set($organization);

    $station = Station::create(['name' => 'Automatisch zugeordnet']);

    expect($station->organization_id)->toBe($organization->id);
});

it('erlaubt tenant-übergreifende Zugriffe nur über withoutTenancy oder bypass', function (): void {
    Station::factory()->create();
    Station::factory()->create();

    // Expliziter Ausstieg über withoutTenancy()
    expect(Station::withoutTenancy()->count())->toBe(2);

    // Expliziter System-Kontext über bypass()
    $count = app(CurrentTenant::class)->bypass(fn (): int => Station::count());

    expect($count)->toBe(2);
});

it('scoped Queries bleiben auch innerhalb von bypass auf den gesetzten Tenant beschränkt', function (): void {
    $organizationA = Organization::factory()->create();
    Station::factory()->for($organizationA)->create();
    Station::factory()->create();

    app(CurrentTenant::class)->set($organizationA);

    // Ein gesetzter Tenant hat Vorrang vor bypass — kein versehentliches Entsperren
    $count = app(CurrentTenant::class)->bypass(fn (): int => Station::count());

    expect($count)->toBe(1);
});
