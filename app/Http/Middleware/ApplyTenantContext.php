<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Organization;
use App\Tenancy\CurrentTenant;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Überträgt den von Filament aufgelösten Tenant in den anwendungsweiten
 * CurrentTenant-Kontext (TenantScope, Rollen-Team-Id). Wird als persistente
 * Tenant-Middleware des App-Panels registriert.
 */
final class ApplyTenantContext
{
    public function __construct(private readonly CurrentTenant $currentTenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Filament::getTenant();

        if ($tenant instanceof Organization) {
            // Gesperrte Organizations haben keinen Zugriff auf das Panel
            abort_if($tenant->isSuspended(), 403, __('core.tenancy.organization_suspended'));

            $this->currentTenant->set($tenant);
        }

        return $next($request);
    }
}
