<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Stations\Schemas;

use App\Enums\StationStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class StationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('core.tabs.master_data'))
                            ->icon(Heroicon::OutlinedBuildingStorefront)
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('core.fields.name'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('station_number')
                                    ->label(__('core.fields.station_number'))
                                    ->maxLength(50),

                                Select::make('status')
                                    ->label(__('core.fields.status'))
                                    ->options(StationStatus::class)
                                    ->default(StationStatus::Active)
                                    ->required()
                                    ->native(false),
                            ]),

                        Tab::make(__('core.tabs.address_contact'))
                            ->icon(Heroicon::OutlinedMapPin)
                            ->columns(2)
                            ->schema([
                                TextInput::make('street')
                                    ->label(__('core.fields.street'))
                                    ->maxLength(255)
                                    ->columnSpanFull(),

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
                            ]),
                    ]),
            ]);
    }
}
