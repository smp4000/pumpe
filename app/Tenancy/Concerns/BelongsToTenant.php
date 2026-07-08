<?php

declare(strict_types=1);

namespace App\Tenancy\Concerns;

use App\Models\Organization;
use App\Tenancy\CurrentTenant;
use App\Tenancy\Exceptions\MissingTenantContextException;
use App\Tenancy\TenantScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Macht ein Model mandantenbezogen:
 *
 * - jede Query wird automatisch auf die aktuelle Organization gefiltert,
 * - organization_id wird beim Erstellen automatisch gesetzt,
 * - ohne Tenant-Kontext schlagen Lese- und Schreibzugriffe fehl (fail-closed).
 *
 * Voraussetzung: Die Tabelle besitzt eine organization_id-Spalte.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model): void {
            // Explizit gesetzte organization_id hat Vorrang (z. B. Betreiber-Panel)
            if ($model->getAttribute('organization_id') !== null) {
                return;
            }

            $tenant = app(CurrentTenant::class);

            if (! $tenant->check()) {
                throw MissingTenantContextException::forModel($model::class);
            }

            $model->setAttribute('organization_id', $tenant->id());
        });
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Expliziter Ausstieg aus dem Tenant-Filter — nur für begründete
     * tenant-übergreifende Zugriffe verwenden.
     *
     * @return Builder<static>
     */
    public static function withoutTenancy(): Builder
    {
        return static::query()->withoutGlobalScope(TenantScope::class);
    }
}
