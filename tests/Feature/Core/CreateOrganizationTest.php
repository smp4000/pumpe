<?php

declare(strict_types=1);

use App\Actions\CreateOrganization;
use App\Authorization\CorePermissions;
use App\Authorization\RoleTemplates;
use App\Models\User;
use App\Tenancy\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('legt eine Organization mit Rollen, Inhaber und erster Station an', function (): void {
    $owner = User::factory()->create();

    $organization = app(CreateOrganization::class)->execute(
        owner: $owner,
        attributes: ['name' => 'Test Tankstellen GmbH'],
        stationName: 'Station Nord',
    );

    // Slug wird automatisch aus dem Namen erzeugt
    expect($organization->slug)->toBe('test-tankstellen-gmbh');

    // Der Gründer ist Mitglied
    expect($organization->users()->whereKey($owner->getKey())->exists())->toBeTrue();

    app(CurrentTenant::class)->set($organization);

    // Alle Rollenvorlagen wurden kopiert
    $roleNames = Role::query()
        ->where('organization_id', $organization->getKey())
        ->pluck('name')
        ->sort()
        ->values()
        ->all();

    expect($roleNames)->toBe([
        RoleTemplates::ACCOUNTING,
        RoleTemplates::EMPLOYEE,
        RoleTemplates::OWNER,
        RoleTemplates::STATION_MANAGER,
    ]);

    // Der Gründer hat die Inhaber-Rolle mit allen Core-Berechtigungen
    expect($owner->hasRole(RoleTemplates::OWNER))->toBeTrue()
        ->and($owner->can(CorePermissions::STATIONS_CREATE))->toBeTrue()
        ->and($owner->can(CorePermissions::ROLES_MANAGE))->toBeTrue();

    // Die erste Station wurde angelegt und gehört zur Organization
    expect($organization->stations()->pluck('name')->all())->toBe(['Station Nord']);
});

it('trennt Rollen zwischen Organizations (Teams-Feature)', function (): void {
    $ownerA = User::factory()->create();
    $ownerB = User::factory()->create();

    $organizationA = app(CreateOrganization::class)->execute($ownerA, ['name' => 'Betrieb A']);
    $organizationB = app(CreateOrganization::class)->execute($ownerB, ['name' => 'Betrieb B']);

    // Im Kontext von B hat der Inhaber von A keine Rolle.
    // fresh() ist nötig, weil spatie/laravel-permission die Rollen-Relation
    // auf der Instanz cached — beim Teamwechsel muss neu geladen werden.
    app(CurrentTenant::class)->set($organizationB);

    expect($ownerB->fresh()->hasRole(RoleTemplates::OWNER))->toBeTrue()
        ->and($ownerA->fresh()->hasRole(RoleTemplates::OWNER))->toBeFalse();

    app(CurrentTenant::class)->set($organizationA);

    expect($ownerA->fresh()->hasRole(RoleTemplates::OWNER))->toBeTrue();
});
