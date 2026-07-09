<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Modules\ModuleManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verweigert den Zugriff auf Modul-Routen, wenn die aktuelle Organization
 * keine nutzbare Lizenz für das Modul besitzt (zweite Ebene der
 * Lizenzprüfung neben Navigation und Policies).
 *
 * Verwendung: ->middleware('module:<module-code>')
 */
final class EnsureModuleIsLicensed
{
    public function __construct(private readonly ModuleManager $moduleManager) {}

    public function handle(Request $request, Closure $next, string $moduleCode): Response
    {
        abort_unless(
            $this->moduleManager->isActiveForCurrentTenant($moduleCode),
            403,
            __('core.modules.not_licensed'),
        );

        return $next($request);
    }
}
