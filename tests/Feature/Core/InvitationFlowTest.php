<?php

declare(strict_types=1);

use App\Actions\CreateOrganization;
use App\Actions\InviteMember;
use App\Authorization\RoleTemplates;
use App\Models\Employee;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\InvitationNotification;
use App\Tenancy\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('versendet eine Einladung per E-Mail', function (): void {
    Notification::fake();

    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->execute($owner, ['name' => 'Betrieb A']);

    app(CurrentTenant::class)->set($organization);

    $invitation = app(InviteMember::class)->execute(
        email: 'Neu@Example.com',
        role: RoleTemplates::EMPLOYEE,
        invitedBy: $owner,
    );

    // Adresse wird normalisiert, Token und Ablauf automatisch gesetzt
    expect($invitation->email)->toBe('neu@example.com')
        ->and($invitation->organization_id)->toBe($organization->id)
        ->and($invitation->token)->toHaveLength(64)
        ->and($invitation->isPending())->toBeTrue();

    Notification::assertSentOnDemand(InvitationNotification::class);
});

it('ersetzt eine offene Einladung an dieselbe Adresse', function (): void {
    Notification::fake();

    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->execute($owner, ['name' => 'Betrieb A']);

    app(CurrentTenant::class)->set($organization);

    app(InviteMember::class)->execute('neu@example.com', RoleTemplates::EMPLOYEE);
    app(InviteMember::class)->execute('neu@example.com', RoleTemplates::STATION_MANAGER);

    $invitations = Invitation::query()->where('email', 'neu@example.com')->get();

    expect($invitations)->toHaveCount(1)
        ->and($invitations->first()->role)->toBe(RoleTemplates::STATION_MANAGER);
});

it('lässt einen neuen Benutzer die Einladung über die Weboberfläche annehmen', function (): void {
    Notification::fake();

    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->execute($owner, ['name' => 'Betrieb A'], 'Station Nord');

    $tenant = app(CurrentTenant::class);
    $tenant->set($organization);

    $employee = Employee::factory()->for($organization)->create(['user_id' => null]);

    $invitation = app(InviteMember::class)->execute(
        email: 'max@example.com',
        role: RoleTemplates::EMPLOYEE,
        employee: $employee,
        invitedBy: $owner,
    );

    // Gast öffnet den Link ohne Tenant-Kontext
    $tenant->forget();

    $this->get(route('invitations.show', ['token' => $invitation->token]))
        ->assertOk()
        ->assertSee('max@example.com');

    $response = $this->post(route('invitations.store', ['token' => $invitation->token]), [
        'name' => 'Max Beispiel',
        'password' => 'geheimes-passwort-123',
        'password_confirmation' => 'geheimes-passwort-123',
    ]);

    $response->assertRedirect();

    $user = User::query()->where('email', 'max@example.com')->firstOrFail();

    // Mitgliedschaft, Rolle, Employee-Verknüpfung und Annahme-Zeitpunkt
    expect($organization->users()->whereKey($user->getKey())->exists())->toBeTrue();

    $tenant->set($organization);

    expect($user->hasRole(RoleTemplates::EMPLOYEE))->toBeTrue()
        ->and($employee->fresh()->user_id)->toBe($user->id)
        ->and(Invitation::query()->whereKey($invitation->getKey())->first()->accepted_at)->not->toBeNull();
});

it('nimmt die Einladung für einen bereits angemeldeten Benutzer direkt an', function (): void {
    Notification::fake();

    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->execute($owner, ['name' => 'Betrieb A']);

    $existing = User::factory()->create(['email' => 'vorhanden@example.com']);

    app(CurrentTenant::class)->set($organization);

    $invitation = app(InviteMember::class)->execute('vorhanden@example.com', RoleTemplates::ACCOUNTING);

    app(CurrentTenant::class)->forget();

    $this->actingAs($existing)
        ->get(route('invitations.show', ['token' => $invitation->token]))
        ->assertRedirect();

    expect($organization->users()->whereKey($existing->getKey())->exists())->toBeTrue();

    app(CurrentTenant::class)->set($organization);

    expect($existing->fresh()->hasRole(RoleTemplates::ACCOUNTING))->toBeTrue();
});

it('weist abgelaufene Einladungen ab', function (): void {
    Notification::fake();

    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->execute($owner, ['name' => 'Betrieb A']);

    app(CurrentTenant::class)->set($organization);

    $invitation = app(InviteMember::class)->execute('spaet@example.com', RoleTemplates::EMPLOYEE);
    $invitation->forceFill(['expires_at' => now()->subDay()])->save();

    app(CurrentTenant::class)->forget();

    // Anzeige: Hinweis statt Formular
    $this->get(route('invitations.show', ['token' => $invitation->token]))
        ->assertOk()
        ->assertSee(__('core.invitations.expired_title'));

    // Annahme: hart abgelehnt
    $this->post(route('invitations.store', ['token' => $invitation->token]), [
        'name' => 'Zu Spät',
        'password' => 'geheimes-passwort-123',
        'password_confirmation' => 'geheimes-passwort-123',
    ])->assertStatus(410);

    expect(User::query()->where('email', 'spaet@example.com')->exists())->toBeFalse();
});
