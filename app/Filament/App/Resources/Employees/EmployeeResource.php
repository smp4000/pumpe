<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Employees;

use App\Filament\App\Resources\Employees\Pages\CreateEmployee;
use App\Filament\App\Resources\Employees\Pages\EditEmployee;
use App\Filament\App\Resources\Employees\Pages\ListEmployees;
use App\Filament\App\Resources\Employees\Schemas\EmployeeForm;
use App\Filament\App\Resources\Employees\Tables\EmployeesTable;
use App\Models\Employee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'last_name';

    public static function getModelLabel(): string
    {
        return __('core.resources.employee.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('core.resources.employee.plural');
    }

    public static function getNavigationGroup(): string
    {
        return __('core.nav.master_data');
    }

    public static function form(Schema $schema): Schema
    {
        return EmployeeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeesTable::configure($table);
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
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'edit' => EditEmployee::route('/{record}/edit'),
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
