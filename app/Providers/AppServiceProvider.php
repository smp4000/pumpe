<?php

declare(strict_types=1);

namespace App\Providers;

use App\Tenancy\CurrentTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Anwendungsdienste registrieren.
     */
    public function register(): void
    {
        // Pro Request/Job isolierter Tenant-Kontext (siehe ADR-0001)
        $this->app->scoped(CurrentTenant::class);
    }

    /**
     * Anwendungsdienste initialisieren.
     */
    public function boot(): void
    {
        // Entwicklungsdisziplin: Lazy-Loading, fehlende Attribute und
        // stilles Verwerfen von Attributen gelten außerhalb der Produktion
        // als Fehler statt als Warnung.
        Model::shouldBeStrict(! $this->app->isProduction());

        // Destruktive Artisan-Befehle (migrate:fresh u. ä.) in Produktion sperren
        DB::prohibitDestructiveCommands($this->app->isProduction());
    }
}
