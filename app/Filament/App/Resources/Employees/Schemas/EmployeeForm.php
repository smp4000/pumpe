<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Employees\Schemas;

use App\Enums\EmployeeStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label(__('core.fields.first_name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('last_name')
                    ->label(__('core.fields.last_name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('personnel_number')
                    ->label(__('core.fields.personnel_number'))
                    ->maxLength(50),

                Select::make('station_id')
                    ->label(__('core.fields.station'))
                    ->relationship('station', 'name')
                    ->native(false)
                    ->preload(),

                TextInput::make('email')
                    ->label(__('core.fields.email'))
                    ->email()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label(__('core.fields.phone'))
                    ->tel()
                    ->maxLength(30),

                DatePicker::make('birth_date')
                    ->label(__('core.fields.birth_date')),

                DatePicker::make('hired_at')
                    ->label(__('core.fields.hired_at')),

                DatePicker::make('terminated_at')
                    ->label(__('core.fields.terminated_at')),

                Select::make('status')
                    ->label(__('core.fields.status'))
                    ->options(EmployeeStatus::class)
                    ->default(EmployeeStatus::Active)
                    ->required()
                    ->native(false),

                Textarea::make('notes')
                    ->label(__('core.fields.notes'))
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
