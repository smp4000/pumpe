<?php

declare(strict_types=1);

use App\Actions\CreateOrganization;
use App\Actions\LicenseModule;
use App\Authorization\RoleTemplates;
use App\Enums\LicenseStatus;
use App\Http\Middleware\EnsureModuleIsLicensed;
use App\Models\Module;
use App\Models\User;
use App\Modules\ModuleManager;
use App\Tenancy\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Modules\Playground\Models\Note;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Artisan::call('modules:sync');
});

it('synchronisiert das Playground-Modul aus dem Manifest in die Datenbank', function (): void {
    $module = Module::query()->where('code', 'playground')->first();

    expect($module)->not->toBeNull()
        ->and($module->name)->toBe('Spielwiese')
        ->and($module->is_core)->toBeFalse();
});

it('ist ohne Lizenz inaktiv und mit Lizenz aktiv', function (): void {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->execute($owner, ['name' => 'Betrieb A']);
    $module = Module::query()->where('code', 'playground')->firstOrFail();

    $manager = app(ModuleManager::class);

    expect($manager->isActive('playground', $organization))->toBeFalse();

    app(LicenseModule::class)->execute($organization, $module, LicenseStatus::Active);

    expect($manager->isActive('playground', $organization))->toBeTrue();
});

it('behandelt abgelaufene Testphasen als inaktiv', function (): void {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->execute($owner, ['name' => 'Betrieb A']);
    $module = Module::query()->where('code', 'playground')->firstOrFail();

    app(LicenseModule::class)->execute(
        $organization,
        $module,
        LicenseStatus::Trial,
        trialEndsAt: now()->subDay(),
    );

    expect(app(ModuleManager::class)->isActive('playground', $organization))->toBeFalse();
});

it('gibt der Inhaber-Rolle die Modul-Berechtigungen bei Lizenzierung', function (): void {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->execute($owner, ['name' => 'Betrieb A']);
    $module = Module::query()->where('code', 'playground')->firstOrFail();

    app(LicenseModule::class)->execute($organization, $module, LicenseStatus::Active);

    app(CurrentTenant::class)->set($organization);

    expect($owner->fresh()->can('playground.notes.create'))->toBeTrue();
});

it('blockiert Modul-Routen ohne Lizenz über die Middleware', function (): void {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->execute($owner, ['name' => 'Betrieb A']);

    app(CurrentTenant::class)->set($organization);

    $middleware = app(EnsureModuleIsLicensed::class);
    $request = Request::create('/test');

    // Ohne Lizenz: 403
    expect(fn () => $middleware->handle($request, fn () => response('ok'), 'playground'))
        ->toThrow(HttpException::class);

    // Mit Lizenz: Durchlass
    $module = Module::query()->where('code', 'playground')->firstOrFail();
    app(LicenseModule::class)->execute($organization, $module, LicenseStatus::Active);

    $response = $middleware->handle($request, fn () => response('ok'), 'playground');

    expect($response->getContent())->toBe('ok');
});

it('trennt Notizen zwischen Organizations (Tenant-Isolation des Moduls)', function (): void {
    $ownerA = User::factory()->create();
    $ownerB = User::factory()->create();
    $organizationA = app(CreateOrganization::class)->execute($ownerA, ['name' => 'Betrieb A']);
    $organizationB = app(CreateOrganization::class)->execute($ownerB, ['name' => 'Betrieb B']);

    $noteA = Note::factory()->for($organizationA, 'organization')->create();
    Note::factory()->for($organizationB, 'organization')->create();

    app(CurrentTenant::class)->set($organizationA);

    expect(Note::query()->pluck('id')->all())->toBe([$noteA->id]);
});

it('erlaubt dem Inhaber-Rollen-Template keinen Zugriff auf nicht lizenzierte Modul-Rechte', function (): void {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->execute($owner, ['name' => 'Betrieb A']);

    app(CurrentTenant::class)->set($organization);

    // Ohne Lizenzierung wurden die Modul-Rechte nie an die Rolle vergeben
    expect($owner->can('playground.notes.create'))->toBeFalse()
        ->and($owner->hasRole(RoleTemplates::OWNER))->toBeTrue();
});
