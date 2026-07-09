<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Filament\Resources\Shifts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\ShiftReconciliation\Filament\Resources\Shifts\ShiftResource;
use Modules\ShiftReconciliation\Models\Shift;

class CreateShift extends CreateRecord
{
    protected static string $resource = ShiftResource::class;

    protected function afterCreate(): void
    {
        /** @var Shift $shift */
        $shift = $this->getRecord();

        $shift->recalculateTotals();
    }
}
