<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Stations\Pages;

use App\Filament\App\Resources\Stations\StationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStation extends CreateRecord
{
    protected static string $resource = StationResource::class;
}
