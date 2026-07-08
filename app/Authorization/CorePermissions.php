<?php

declare(strict_types=1);

namespace App\Authorization;

/**
 * Zentrale Registry der Core-Berechtigungen.
 *
 * Namensschema: core.<resource>.<ability> — Fachmodule registrieren ihre
 * Berechtigungen später analog als <module-code>.<resource>.<ability>.
 */
final class CorePermissions
{
    public const STATIONS_VIEW = 'core.stations.view';

    public const STATIONS_CREATE = 'core.stations.create';

    public const STATIONS_UPDATE = 'core.stations.update';

    public const STATIONS_DELETE = 'core.stations.delete';

    public const EMPLOYEES_VIEW = 'core.employees.view';

    public const EMPLOYEES_CREATE = 'core.employees.create';

    public const EMPLOYEES_UPDATE = 'core.employees.update';

    public const EMPLOYEES_DELETE = 'core.employees.delete';

    public const MEMBERS_VIEW = 'core.members.view';

    public const MEMBERS_INVITE = 'core.members.invite';

    public const MEMBERS_UPDATE = 'core.members.update';

    public const MEMBERS_REMOVE = 'core.members.remove';

    public const ROLES_MANAGE = 'core.roles.manage';

    public const ORGANIZATION_UPDATE = 'core.organization.update';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::STATIONS_VIEW,
            self::STATIONS_CREATE,
            self::STATIONS_UPDATE,
            self::STATIONS_DELETE,
            self::EMPLOYEES_VIEW,
            self::EMPLOYEES_CREATE,
            self::EMPLOYEES_UPDATE,
            self::EMPLOYEES_DELETE,
            self::MEMBERS_VIEW,
            self::MEMBERS_INVITE,
            self::MEMBERS_UPDATE,
            self::MEMBERS_REMOVE,
            self::ROLES_MANAGE,
            self::ORGANIZATION_UPDATE,
        ];
    }
}
