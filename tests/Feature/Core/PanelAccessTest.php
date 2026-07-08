<?php

declare(strict_types=1);

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('erlaubt das Betreiber-Panel nur Plattform-Administratoren', function (): void {
    $admin = User::factory()->create(['is_platform_admin' => true]);
    $user = User::factory()->create();

    $adminPanel = Filament::getPanel('admin');
    $appPanel = Filament::getPanel('app');

    expect($admin->canAccessPanel($adminPanel))->toBeTrue()
        ->and($user->canAccessPanel($adminPanel))->toBeFalse()
        ->and($user->canAccessPanel($appPanel))->toBeTrue();
});
