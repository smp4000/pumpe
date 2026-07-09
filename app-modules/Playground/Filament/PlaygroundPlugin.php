<?php

declare(strict_types=1);

namespace Modules\Playground\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Playground\Filament\Resources\Notes\NoteResource;

/**
 * Filament-Plugin des Moduls — wird vom ModuleManager im App-Panel
 * registriert. Die Sichtbarkeit pro Tenant regelt der
 * BelongsToModule-Trait der Resources (Lizenzprüfung).
 */
class PlaygroundPlugin implements Plugin
{
    public function getId(): string
    {
        return 'playground';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            NoteResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
