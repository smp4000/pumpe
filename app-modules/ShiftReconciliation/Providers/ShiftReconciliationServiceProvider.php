<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\ShiftReconciliation\Models\Shift;
use Modules\ShiftReconciliation\Policies\ShiftPolicy;

class ShiftReconciliationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'shift-reconciliation');

        Gate::policy(Shift::class, ShiftPolicy::class);
    }
}
