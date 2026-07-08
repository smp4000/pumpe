<?php

declare(strict_types=1);

namespace App\Tenancy;

use App\Models\Organization;
use Spatie\Permission\PermissionRegistrar;

/**
 * Hält die aktuelle Organization (Tenant) des Requests bzw. Jobs.
 *
 * Wird als scoped Singleton registriert und ist damit pro Request/Job
 * isoliert. Der TenantScope liest diesen Kontext für das automatische
 * Filtern aller mandantenbezogenen Queries (fail-closed, siehe ADR-0001).
 */
final class CurrentTenant
{
    private ?Organization $organization = null;

    /**
     * Erlaubt Queries ohne Tenant-Filter — ausschließlich für das
     * Betreiber-Panel und explizit markierte System-Kontexte.
     */
    private bool $allowUnscopedQueries = false;

    public function set(Organization $organization): void
    {
        $this->organization = $organization;

        // Rollen/Rechte (spatie/laravel-permission, Teams-Feature) an den
        // Tenant koppeln, damit Rollenzuweisungen tenant-spezifisch greifen.
        app(PermissionRegistrar::class)->setPermissionsTeamId($organization->getKey());
    }

    public function forget(): void
    {
        $this->organization = null;

        app(PermissionRegistrar::class)->setPermissionsTeamId(null);
    }

    public function get(): ?Organization
    {
        return $this->organization;
    }

    public function id(): ?string
    {
        return $this->organization?->getKey();
    }

    public function check(): bool
    {
        return $this->organization !== null;
    }

    public function allowUnscopedQueries(): void
    {
        $this->allowUnscopedQueries = true;
    }

    public function unscopedQueriesAllowed(): bool
    {
        return $this->allowUnscopedQueries;
    }

    /**
     * Führt einen Codeblock ohne Tenant-Filter aus (System-Kontext),
     * z. B. für Betreiber-Auswertungen oder Wartungs-Jobs.
     *
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    public function bypass(callable $callback): mixed
    {
        $previous = $this->allowUnscopedQueries;
        $this->allowUnscopedQueries = true;

        try {
            return $callback();
        } finally {
            $this->allowUnscopedQueries = $previous;
        }
    }
}
