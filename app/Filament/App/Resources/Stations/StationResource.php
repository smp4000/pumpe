<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Stations;

use App\Filament\App\Resources\Stations\Pages\CreateStation;
use App\Filament\App\Resources\Stations\Pages\EditStation;
use App\Filament\App\Resources\Stations\Pages\ListStations;
use App\Filament\App\Resources\Stations\Schemas\StationForm;
use App\Filament\App\Resources\Stations\Tables\StationsTable;
use App\Models\Station;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StationResource extends Resource
{
    protected static ?string $model = Station::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('core.resources.station.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('core.resources.station.plural');
    }

    public static function getNavigationGroup(): string
    {
        return __('core.nav.master_data');
    }

    public static function form(Schema $schema): Schema
    {
        return StationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStations::route('/'),
            'create' => CreateStation::route('/create'),
            'edit' => EditStation::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
