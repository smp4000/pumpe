<?php

declare(strict_types=1);

namespace App\Providers;

use App\Modules\ModuleManager;
use Illuminate\Support\ServiceProvider;

/**
 * Registriert die ServiceProvider aller Module dynamisch anhand der
 * module.json-Manifeste. Module sind dadurch installierbar, ohne
 * bootstrap/providers.php anzufassen.
 */
class ModulesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleManager::class);

        foreach ($this->app->make(ModuleManager::class)->serviceProviders() as $provider) {
            $this->app->register($provider);
        }
    }
}
