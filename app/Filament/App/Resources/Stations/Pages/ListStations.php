<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Stations\Pages;

use App\Filament\App\Resources\Stations\StationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStations extends ListRecords
{
    protected static string $resource = StationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
