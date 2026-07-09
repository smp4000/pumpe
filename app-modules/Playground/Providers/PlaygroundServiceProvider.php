<?php

declare(strict_types=1);

namespace Modules\Playground\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Playground\Models\Note;
use Modules\Playground\Policies\NotePolicy;

/**
 * Einstiegspunkt des Moduls — wird vom ModulesServiceProvider anhand
 * des module.json-Manifests automatisch registriert.
 */
class PlaygroundServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'playground');

        // Policies von Modul-Models werden explizit registriert
        // (Auto-Discovery gilt nur für App\Models ↔ App\Policies)
        Gate::policy(Note::class, NotePolicy::class);
    }
}
