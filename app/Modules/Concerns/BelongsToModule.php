<?php

declare(strict_types=1);

namespace App\Modules\Concerns;

use App\Modules\ModuleManager;

/**
 * Für Filament-Resources von Modulen: blendet Navigation und Seiten aus,
 * wenn die aktuelle Organization keine nutzbare Lizenz besitzt (erste
 * Ebene der Lizenzprüfung neben Middleware und Policies).
 *
 * Verwendung: Trait einbinden und getModuleCode() implementieren.
 */
trait BelongsToModule
{
    abstract public static function getModuleCode(): string;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isActiveForCurrentTenant(static::getModuleCode())
            && parent::canAccess();
    }
}
