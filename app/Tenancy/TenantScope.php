<?php

declare(strict_types=1);

namespace App\Tenancy;

use App\Tenancy\Exceptions\MissingTenantContextException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Globaler Scope für mandantenbezogene Models: filtert jede Query auf die
 * aktuelle Organization. Ohne Tenant-Kontext schlägt die Query fehl
 * (fail-closed), außer der Kontext erlaubt ausdrücklich ungefilterte
 * Zugriffe (Betreiber-Panel, System-Jobs).
 */
final class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenant = app(CurrentTenant::class);

        if ($tenant->check()) {
            $builder->where($model->qualifyColumn('organization_id'), $tenant->id());

            return;
        }

        if ($tenant->unscopedQueriesAllowed()) {
            return;
        }

        throw MissingTenantContextException::forModel($model::class);
    }
}
