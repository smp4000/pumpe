<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Employees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('last_name')
                    ->label(__('core.fields.last_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('first_name')
                    ->label(__('core.fields.first_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('personnel_number')
                    ->label(__('core.fields.personnel_number'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('station.name')
                    ->label(__('core.fields.station'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label(__('core.fields.status'))
                    ->badge()
                    ->sortable(),
            ])
            ->defaultSort('last_name')
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
