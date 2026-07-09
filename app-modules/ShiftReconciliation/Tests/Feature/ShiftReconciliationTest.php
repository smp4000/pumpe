<?php

declare(strict_types=1);

use App\Actions\CreateOrganization;
use App\Actions\LicenseModule;
use App\Enums\LicenseStatus;
use App\Models\Module;
use App\Models\User;
use App\Tenancy\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Modules\ShiftReconciliation\Actions\ApproveShift;
use Modules\ShiftReconciliation\Actions\CancelShift;
use Modules\ShiftReconciliation\Actions\SubmitShift;
use Modules\ShiftReconciliation\Enums\PaymentMethod;
use Modules\ShiftReconciliation\Enums\ShiftStatus;
use Modules\ShiftReconciliation\Events\ShiftApproved;
use Modules\ShiftReconciliation\Exceptions\ShiftImmutableException;
use Modules\ShiftReconciliation\Filament\Resources\Shifts\ShiftResource;
use Modules\ShiftReconciliation\Models\Shift;
use Modules\ShiftReconciliation\Models\ShiftEntry;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Artisan::call('modules:sync');

    $this->owner = User::factory()->create();
    $this->organization = app(CreateOrganization::class)->execute(
        $this->owner,
        ['name' => 'Betrieb A'],
        'Station Nord',
    );

    app(LicenseModule::class)->execute(
        $this->organization,
        Module::query()->where('code', 'shift-reconciliation')->firstOrFail(),
        LicenseStatus::Active,
    );

    app(CurrentTenant::class)->set($this->organization);

    $this->station = $this->organization->stations()->firstOrFail();
});

function makeShift(): Shift
{
    return Shift::factory()->create([
        'organization_id' => test()->organization->getKey(),
        'station_id' => test()->station->getKey(),
    ]);
}

it('berechnet Soll/Ist-Summen und Differenz aus den Positionen', function (): void {
    $shift = makeShift();

    ShiftEntry::factory()->create([
        'shift_id' => $shift->getKey(),
        'payment_method' => PaymentMethod::Cash,
        'expected_amount_cents' => 50_000,
        'counted_amount_cents' => 49_750,
    ]);

    ShiftEntry::factory()->create([
        'shift_id' => $shift->getKey(),
        'payment_method' => PaymentMethod::DebitCard,
        'expected_amount_cents' => 120_000,
        'counted_amount_cents' => 120_000,
    ]);

    $shift->recalculateTotals();

    expect($shift->expected_total_cents)->toBe(170_000)
        ->and($shift->counted_total_cents)->toBe(169_750)
        ->and($shift->difference_cents)->toBe(-250);
});

it('durchläuft den Statusfluss offen → eingereicht → freigegeben', function (): void {
    Event::fake([ShiftApproved::class]);

    $shift = makeShift();

    app(SubmitShift::class)->execute($shift, $this->owner);

    expect($shift->status)->toBe(ShiftStatus::Submitted)
        ->and($shift->submitted_by)->toBe($this->owner->getKey());

    app(ApproveShift::class)->execute($shift, $this->owner);

    expect($shift->status)->toBe(ShiftStatus::Approved);

    Event::assertDispatched(ShiftApproved::class);
});

it('macht freigegebene Abrechnungen unveränderlich (GoBD)', function (): void {
    $shift = makeShift();

    $entry = ShiftEntry::factory()->create(['shift_id' => $shift->getKey()]);

    app(SubmitShift::class)->execute($shift, $this->owner);
    app(ApproveShift::class)->execute($shift, $this->owner);

    // Kopfdaten unveränderlich
    expect(fn () => $shift->update(['notes' => 'Nachträglich geändert']))
        ->toThrow(ShiftImmutableException::class);

    // Positionen unveränderlich
    expect(fn () => $entry->fresh()->update(['counted_amount_cents' => 1]))
        ->toThrow(ShiftImmutableException::class);

    // Löschen verboten
    expect(fn () => $shift->fresh()->delete())
        ->toThrow(ShiftImmutableException::class);
});

it('erlaubt Storno nur für freigegebene Abrechnungen und verlangt eine Begründung', function (): void {
    $shift = makeShift();

    // Offene Abrechnung kann nicht storniert werden
    expect(fn () => app(CancelShift::class)->execute($shift, $this->owner, 'Fehler'))
        ->toThrow(InvalidArgumentException::class);

    app(SubmitShift::class)->execute($shift, $this->owner);
    app(ApproveShift::class)->execute($shift, $this->owner);

    app(CancelShift::class)->execute($shift, $this->owner, 'Zählfehler Bargeld');

    expect($shift->status)->toBe(ShiftStatus::Cancelled)
        ->and($shift->cancel_reason)->toBe('Zählfehler Bargeld');

    // Stornierte Abrechnung ist ebenfalls unveränderlich
    expect(fn () => $shift->fresh()->update(['notes' => 'x']))
        ->toThrow(ShiftImmutableException::class);
});

it('erlaubt das Löschen nur im Entwurfsstatus', function (): void {
    $shift = makeShift();

    app(SubmitShift::class)->execute($shift, $this->owner);

    expect(fn () => $shift->fresh()->delete())
        ->toThrow(ShiftImmutableException::class);

    $draft = makeShift();

    expect($draft->delete())->toBeTrue();
});

it('trennt Abrechnungen zwischen Organizations', function (): void {
    $shiftA = makeShift();

    $otherOwner = User::factory()->create();
    $otherOrganization = app(CreateOrganization::class)->execute($otherOwner, ['name' => 'Betrieb B'], 'Station Süd');

    Shift::factory()->create([
        'organization_id' => $otherOrganization->getKey(),
        'station_id' => $otherOrganization->stations()->withoutGlobalScopes()->firstOrFail()->getKey(),
    ]);

    app(CurrentTenant::class)->set($this->organization);

    expect(Shift::query()->pluck('id')->all())->toBe([$shiftA->id]);
});

it('rendert Liste und Formular mit Lizenz und blockiert ohne Lizenz', function (): void {
    $this->actingAs($this->owner);

    $this->get(ShiftResource::getUrl('index', tenant: $this->organization))->assertOk();
    $this->get(ShiftResource::getUrl('create', tenant: $this->organization))->assertOk();

    // Lizenz entziehen → Modul verschwindet
    app(LicenseModule::class)->execute(
        $this->organization,
        Module::query()->where('code', 'shift-reconciliation')->firstOrFail(),
        LicenseStatus::Expired,
    );

    $this->get(ShiftResource::getUrl('index', tenant: $this->organization))->assertForbidden();
});
