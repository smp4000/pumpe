<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Exceptions;

use Modules\ShiftReconciliation\Models\Shift;
use RuntimeException;

/**
 * Verletzung des GoBD-Unveränderlichkeits-Schutzes einer Abrechnung.
 */
final class ShiftImmutableException extends RuntimeException
{
    public static function approved(Shift $shift): self
    {
        return new self("Shift [{$shift->getKey()}] is approved and immutable — cancel it and create a replacement instead.");
    }

    public static function cancelled(Shift $shift): self
    {
        return new self("Shift [{$shift->getKey()}] is cancelled and immutable.");
    }

    public static function notDeletable(Shift $shift): self
    {
        return new self("Shift [{$shift->getKey()}] can only be deleted while open.");
    }

    public static function entries(Shift $shift): self
    {
        return new self("Entries of shift [{$shift->getKey()}] can only be modified while the shift is open.");
    }
}
