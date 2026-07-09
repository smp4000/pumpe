<?php

declare(strict_types=1);

namespace App\Support;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Lang;
use Spatie\Permission\Models\Role;

/**
 * Rollen-Auswahllisten für Formulare: Rollen des aktuellen Tenants mit
 * deutscher Anzeige der bekannten Vorlagen-Rollen; eigene Rollen der
 * Tenants werden unverändert angezeigt.
 */
final class RoleOptions
{
    /**
     * @return array<string, string> Rollenname => Anzeigename
     */
    public static function forCurrentTenant(): array
    {
        $organizationId = Filament::getTenant()?->getKey();

        if ($organizationId === null) {
            return [];
        }

        return Role::query()
            ->where('organization_id', $organizationId)
            ->orderBy('name')
            ->pluck('name')
            ->mapWithKeys(fn (string $name): array => [$name => self::label($name)])
            ->all();
    }

    public static function label(string $roleName): string
    {
        return Lang::has('core.roles.'.$roleName)
            ? __('core.roles.'.$roleName)
            : $roleName;
    }
}
