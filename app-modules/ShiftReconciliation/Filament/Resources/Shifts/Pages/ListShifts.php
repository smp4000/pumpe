<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Filament\Resources\Shifts\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ShiftReconciliation\Filament\Resources\Shifts\ShiftResource;

class ListShifts extends ListRecords
{
    protected static string $resource = ShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
