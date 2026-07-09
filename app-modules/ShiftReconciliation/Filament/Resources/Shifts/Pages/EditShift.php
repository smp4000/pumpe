<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Filament\Resources\Shifts\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\ShiftReconciliation\Filament\Resources\Shifts\ShiftResource;
use Modules\ShiftReconciliation\Models\Shift;

class EditShift extends EditRecord
{
    protected static string $resource = ShiftResource::class;

    protected function afterSave(): void
    {
        /** @var Shift $shift */
        $shift = $this->getRecord();

        $shift->recalculateTotals();
    }
}
