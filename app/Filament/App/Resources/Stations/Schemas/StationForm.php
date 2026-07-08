<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Stations\Schemas;

use App\Enums\StationStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('core.fields.name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('station_number')
                    ->label(__('core.fields.station_number'))
                    ->maxLength(50),

                TextInput::make('street')
                    ->label(__('core.fields.street'))
                    ->maxLength(255),

                TextInput::make('postal_code')
                    ->label(__('core.fields.postal_code'))
                    ->maxLength(10),

                TextInput::make('city')
                    ->label(__('core.fields.city'))
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label(__('core.fields.phone'))
                    ->tel()
                    ->maxLength(30),

                Select::make('status')
                    ->label(__('core.fields.status'))
                    ->options(StationStatus::class)
                    ->default(StationStatus::Active)
                    ->required()
                    ->native(false),
            ]);
    }
}
