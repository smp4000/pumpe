<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Stations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class StationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('core.fields.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('station_number')
                    ->label(__('core.fields.station_number'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('city')
                    ->label(__('core.fields.city'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('core.fields.status'))
                    ->badge()
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
