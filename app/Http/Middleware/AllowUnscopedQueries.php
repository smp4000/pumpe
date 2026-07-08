<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Tenancy\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Erlaubt tenant-übergreifende Queries — ausschließlich für das
 * Betreiber-Panel (/admin). Fachliche Panels dürfen diese Middleware
 * niemals verwenden (fail-closed, siehe ADR-0001).
 */
final class AllowUnscopedQueries
{
    public function __construct(private readonly CurrentTenant $currentTenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->currentTenant->allowUnscopedQueries();

        return $next($request);
    }
}
