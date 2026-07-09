<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\ShiftReconciliation\Filament\Resources\Shifts\ShiftResource;

class ShiftReconciliationPlugin implements Plugin
{
    public function getId(): string
    {
        return 'shift-reconciliation';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ShiftResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
