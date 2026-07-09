<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organizations;

use App\Enums\OrganizationStatus;
use App\Filament\Resources\Organizations\Pages\EditOrganization;
use App\Filament\Resources\Organizations\Pages\ListOrganizations;
use App\Models\Organization;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Betreiber-Sicht auf alle Mandanten. Anlage neuer Betriebe erfolgt über
 * die Selbstregistrierung im App-Panel — hier wird verwaltet und gesperrt.
 */
class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('core.resources.organization.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('core.resources.organization.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('core.fields.name'))
                ->required()
                ->maxLength(255),

            TextInput::make('legal_name')
                ->label('Firmierung')
                ->maxLength(255),

            TextInput::make('slug')
                ->label('URL-Kürzel')
                ->disabled()
                ->helperText('Wird bei der Registrierung erzeugt und ist Teil aller Links des Betriebs.'),

            TextInput::make('vat_id')
                ->label('USt-IdNr.')
                ->maxLength(20),

            TextInput::make('billing_email')
                ->label('Rechnungs-E-Mail')
                ->email()
                ->maxLength(255),

            TextInput::make('phone')
                ->label(__('core.fields.phone'))
                ->tel()
                ->maxLength(30),

            TextInput::make('street')
                ->label(__('core.fields.street'))
                ->maxLength(255),

            TextInput::make('postal_code')
                ->label(__('core.fields.postal_code'))
                ->maxLength(10),

            TextInput::make('city')
                ->label(__('core.fields.city'))
                ->maxLength(255),

            Select::make('status')
                ->label(__('core.fields.status'))
                ->options(OrganizationStatus::class)
                ->required()
                ->native(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('core.fields.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('URL-Kürzel')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label(__('core.fields.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('stations_count')
                    ->label(__('core.organizations.stations_count'))
                    ->counts('stations'),

                TextColumn::make('users_count')
                    ->label(__('core.organizations.users_count'))
                    ->counts('users'),

                TextColumn::make('created_at')
                    ->label(__('core.fields.created_at'))
                    ->dateTime('d.m.Y', 'Europe/Berlin')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),

                Action::make('suspend')
                    ->label(__('core.organizations.suspend'))
                    ->icon(Heroicon::OutlinedLockClosed)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Organization $record): bool => ! $record->isSuspended())
                    ->action(function (Organization $record): void {
                        $record->update(['status' => OrganizationStatus::Suspended]);

                        Notification::make()
                            ->title(__('core.organizations.suspended'))
                            ->success()
                            ->send();
                    }),

                Action::make('activate')
                    ->label(__('core.organizations.activate'))
                    ->icon(Heroicon::OutlinedLockOpen)
                    ->color('success')
                    ->visible(fn (Organization $record): bool => $record->isSuspended())
                    ->action(function (Organization $record): void {
                        $record->update(['status' => OrganizationStatus::Active]);

                        Notification::make()
                            ->title(__('core.organizations.activated'))
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizations::route('/'),
            'edit' => EditOrganization::route('/{record}/edit'),
        ];
    }
}
