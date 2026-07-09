<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Modules\ShiftReconciliation\Models\Shift;

/**
 * Öffentliches Ereignis des Moduls: eine Schichtabrechnung wurde
 * freigegeben. Andere Module (z. B. Buchhaltungsexport) konsumieren
 * dieses Event — nie die Models direkt (Modul-Konvention 1).
 */
final class ShiftApproved
{
    use Dispatchable;

    public function __construct(public readonly Shift $shift) {}
}
