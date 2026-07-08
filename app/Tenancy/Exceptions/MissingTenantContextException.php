<?php

declare(strict_types=1);

namespace App\Tenancy\Exceptions;

use RuntimeException;

/**
 * Wird geworfen, wenn ein mandantenbezogenes Model ohne gesetzten
 * Tenant-Kontext gelesen oder geschrieben wird (fail-closed).
 *
 * Legitime tenant-übergreifende Zugriffe müssen explizit über
 * CurrentTenant::bypass() bzw. Model::withoutTenancy() erfolgen.
 */
final class MissingTenantContextException extends RuntimeException
{
    public static function forModel(string $model): self
    {
        return new self(sprintf(
            'Query on tenant-scoped model [%s] without tenant context. '
            .'Set CurrentTenant or use bypass()/withoutTenancy() explicitly.',
            $model,
        ));
    }
}
