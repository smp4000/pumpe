<?php

declare(strict_types=1);

namespace App\Authorization;

/**
 * Rollenvorlagen, die beim Anlegen einer Organization kopiert werden.
 *
 * Rollennamen sind stabile englische Identifier; die deutsche Anzeige
 * erfolgt über lang/de/core.php (roles.*). Tenants können ihre Rollen
 * später frei anpassen — die Vorlagen sind nur der Startzustand.
 */
final class RoleTemplates
{
    public const OWNER = 'owner';

    public const STATION_MANAGER = 'station_manager';

    public const EMPLOYEE = 'employee';

    public const ACCOUNTING = 'accounting';

    /**
     * @return array<string, list<string>> Rollenname => Berechtigungen
     */
    public static function all(): array
    {
        return [
            self::OWNER => CorePermissions::all(),

            self::STATION_MANAGER => [
                CorePermissions::STATIONS_VIEW,
                CorePermissions::EMPLOYEES_VIEW,
                CorePermissions::EMPLOYEES_CREATE,
                CorePermissions::EMPLOYEES_UPDATE,
                CorePermissions::MEMBERS_VIEW,
            ],

            self::EMPLOYEE => [
                CorePermissions::STATIONS_VIEW,
            ],

            self::ACCOUNTING => [
                CorePermissions::STATIONS_VIEW,
                CorePermissions::EMPLOYEES_VIEW,
                CorePermissions::MEMBERS_VIEW,
            ],
        ];
    }
}
