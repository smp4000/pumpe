<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Formatierung von Integer-Cents für die Anzeige (ADR-0006).
 */
final class Money
{
    public static function format(int $cents): string
    {
        return number_format($cents / 100, 2, ',', '.').' €';
    }
}
