<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Invitations;

use App\Filament\App\Resources\Invitations\Pages\ListInvitations;
use App\Models\Invitation;
use App\Support\RoleOptions;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    public static function getModelLabel(): string
    {
        return __('core.resources.invitation.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('core.resources.invitation.plural');
    }

    public static function getNavigationGroup(): string
    {
        return __('core.nav.team');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('email')
                ->label(__('core.fields.email'))
                ->email()
                ->required()
                ->maxLength(255),

            Select::make('role')
                ->label(__('core.fields.role'))
                ->options(RoleOptions::forCurrentTenant())
                ->required()
                ->native(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->label(__('core.fields.email'))
                    ->searchable(),

                TextColumn::make('role')
                    ->label(__('core.fields.role'))
                    ->formatStateUsing(fn (string $state): string => RoleOptions::label($state)),

                TextColumn::make('status')
                    ->label(__('core.fields.status'))
                    ->state(fn (Invitation $record): string => match (true) {
                        $record->accepted_at !== null => 'accepted',
                        $record->isExpired() => 'expired',
                        default => 'pending',
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'expired' => 'gray',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => __('core.invitations.status_'.$state)),

                TextColumn::make('expires_at')
                    ->label(__('core.fields.expires_at'))
                    ->dateTime('d.m.Y H:i', 'Europe/Berlin')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('resend')
                    ->label(__('core.invitations.resend'))
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->visible(fn (Invitation $record): bool => $record->isPending())
                    ->action(function (Invitation $record): void {
                        NotificationFacade::route('mail', $record->email)
                            ->notify(new \App\Notifications\InvitationNotification($record));

                        Notification::make()
                            ->title(__('core.invitations.resent'))
                            ->success()
                            ->send();
                    }),

                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvitations::route('/'),
        ];
    }
}
