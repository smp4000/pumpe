<?php

declare(strict_types=1);

return [
    App\Providers\AppServiceProvider::class,
    // Registriert die ServiceProvider aller Module (app-modules/*/module.json) —
    // muss vor den Panel-Providern stehen, damit Filament-Plugins verfügbar sind
    App\Providers\ModulesServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\AppPanelProvider::class,
];
