<?php

declare(strict_types=1);

namespace App\Modules;

use App\Models\ModuleLicense;
use App\Models\Organization;
use App\Tenancy\CurrentTenant;
use Filament\Contracts\Plugin;

/**
 * Zentrale Modulverwaltung: liest die module.json-Manifeste unter
 * app-modules/ ein und beantwortet Lizenzfragen zur Laufzeit.
 *
 * Lizenzprüfung erfolgt dreistufig (Navigation, Middleware, Policy) —
 * alle drei Ebenen fragen diesen Manager.
 */
class ModuleManager
{
    /** @var array<string, ModuleManifest>|null */
    private ?array $manifests = null;

    /** @var array<string, bool> Request-lokaler Lizenz-Cache (orgId:code) */
    private array $licenseCache = [];

    public function __construct(private readonly CurrentTenant $currentTenant) {}

    /**
     * @return array<string, ModuleManifest> Modul-Code => Manifest
     */
    public function manifests(): array
    {
        if ($this->manifests !== null) {
            return $this->manifests;
        }

        $this->manifests = [];

        foreach (glob(base_path('app-modules/*/module.json')) ?: [] as $path) {
            $manifest = ModuleManifest::fromFile($path);
            $this->manifests[$manifest->code] = $manifest;
        }

        return $this->manifests;
    }

    public function manifest(string $code): ?ModuleManifest
    {
        return $this->manifests()[$code] ?? null;
    }

    /**
     * ServiceProvider-Klassen aller Module (für die dynamische Registrierung).
     *
     * @return list<class-string>
     */
    public function serviceProviders(): array
    {
        $providers = [];

        foreach ($this->manifests() as $manifest) {
            $class = $manifest->serviceProviderClass();

            if (class_exists($class)) {
                $providers[] = $class;
            }
        }

        return $providers;
    }

    /**
     * Filament-Plugins aller Module (Registrierung im App-Panel; die
     * Sichtbarkeit pro Tenant regeln Resource-Trait und Policies).
     *
     * @return list<Plugin>
     */
    public function filamentPlugins(): array
    {
        $plugins = [];

        foreach ($this->manifests() as $manifest) {
            $class = $manifest->filamentPluginClass();

            if (class_exists($class)) {
                /** @var Plugin $plugin */
                $plugin = new $class;
                $plugins[] = $plugin;
            }
        }

        return $plugins;
    }

    /**
     * Alle Berechtigungen, die Module über ihre Manifeste deklarieren.
     *
     * @return list<string>
     */
    public function declaredPermissions(): array
    {
        $permissions = [];

        foreach ($this->manifests() as $manifest) {
            $permissions = [...$permissions, ...$manifest->permissions];
        }

        return $permissions;
    }

    /**
     * Ist das Modul für die aktuelle Organization nutzbar?
     */
    public function isActiveForCurrentTenant(string $code): bool
    {
        $organization = $this->currentTenant->get();

        if ($organization === null) {
            return false;
        }

        return $this->isActive($code, $organization);
    }

    public function isActive(string $code, Organization $organization): bool
    {
        $manifest = $this->manifest($code);

        if ($manifest === null) {
            return false;
        }

        // Core-Module sind für alle Organizations ohne Lizenz aktiv
        if ($manifest->isCore) {
            return true;
        }

        // Abhängigkeiten müssen ebenfalls aktiv sein
        foreach ($manifest->dependsOn as $dependency) {
            if (! $this->isActive($dependency, $organization)) {
                return false;
            }
        }

        $cacheKey = $organization->getKey().':'.$code;

        return $this->licenseCache[$cacheKey] ??= $this->hasUsableLicense($code, $organization);
    }

    /**
     * Leert den Request-lokalen Lizenz-Cache (nach Lizenzänderungen).
     */
    public function flushLicenseCache(): void
    {
        $this->licenseCache = [];
    }

    private function hasUsableLicense(string $code, Organization $organization): bool
    {
        $license = ModuleLicense::withoutTenancy()
            ->where('organization_id', $organization->getKey())
            ->whereHas('module', fn ($query) => $query->where('code', $code))
            ->first();

        return $license?->isUsable() ?? false;
    }
}
