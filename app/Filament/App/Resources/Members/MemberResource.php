<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Members;

use App\Authorization\CorePermissions;
use App\Filament\App\Resources\Members\Pages\ListMembers;
use App\Models\User;
use App\Support\RoleOptions;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Mitgliederverwaltung: alle Login-Konten mit Zugang zur aktuellen
 * Organization. Users tragen keinen TenantScope — das Scoping erfolgt
 * hier explizit über die Mitgliedschafts-Relation.
 */
class MemberResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'members';

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function getModelLabel(): string
    {
        return __('core.resources.member.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('core.resources.member.plural');
    }

    public static function getNavigationGroup(): string
    {
        return __('core.nav.team');
    }

    public static function canCreate(): bool
    {
        // Neue Mitglieder kommen ausschließlich über Einladungen
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas(
            'organizations',
            fn (Builder $query) => $query->whereKey(Filament::getTenant()?->getKey()),
        );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('core.fields.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('core.fields.email'))
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->label(__('core.fields.roles'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => RoleOptions::label($state)),
            ])
            ->defaultSort('name')
            ->recordActions([
                Action::make('editRoles')
                    ->label(__('core.members.edit_roles'))
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->visible(fn (User $record): bool => auth()->user()?->can(CorePermissions::MEMBERS_UPDATE) === true
                        && ! $record->is(auth()->user()))
                    ->fillForm(fn (User $record): array => [
                        'roles' => $record->roles()->pluck('name')->all(),
                    ])
                    ->schema([
                        Select::make('roles')
                            ->label(__('core.fields.roles'))
                            ->multiple()
                            ->options(RoleOptions::forCurrentTenant())
                            ->required()
                            ->native(false),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->syncRoles($data['roles']);
                    }),

                Action::make('remove')
                    ->label(__('core.members.remove'))
                    ->icon(Heroicon::OutlinedUserMinus)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription(__('core.members.remove_confirm'))
                    ->visible(fn (User $record): bool => auth()->user()?->can(CorePermissions::MEMBERS_REMOVE) === true
                        && ! $record->is(auth()->user()))
                    ->action(function (User $record): void {
                        $organization = Filament::getTenant();

                        if ($organization === null) {
                            return;
                        }

                        // Rollen und Stationszugriffe dieser Organization entfernen,
                        // der Personalstammsatz (Employee) bleibt erhalten.
                        $record->syncRoles([]);
                        $record->stations()->detach(
                            $record->stations()->pluck('stations.id'),
                        );
                        $record->organizations()->detach($organization->getKey());

                        Notification::make()
                            ->title(__('core.members.removed'))
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMembers::route('/'),
        ];
    }
}
