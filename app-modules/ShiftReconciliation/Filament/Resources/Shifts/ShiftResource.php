<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Filament\Resources\Shifts;

use App\Filament\Components\MoneyInput;
use App\Models\Employee;
use App\Modules\Concerns\BelongsToModule;
use App\Support\Money;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\ShiftReconciliation\Actions\ApproveShift;
use Modules\ShiftReconciliation\Actions\CancelShift;
use Modules\ShiftReconciliation\Actions\SubmitShift;
use Modules\ShiftReconciliation\Enums\PaymentMethod;
use Modules\ShiftReconciliation\Enums\ShiftStatus;
use Modules\ShiftReconciliation\Filament\Resources\Shifts\Pages\CreateShift;
use Modules\ShiftReconciliation\Filament\Resources\Shifts\Pages\EditShift;
use Modules\ShiftReconciliation\Filament\Resources\Shifts\Pages\ListShifts;
use Modules\ShiftReconciliation\Models\Shift;

class ShiftResource extends Resource
{
    use BelongsToModule;

    protected static ?string $model = Shift::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function getModuleCode(): string
    {
        return 'shift-reconciliation';
    }

    public static function getModelLabel(): string
    {
        return __('shift-reconciliation::shift-reconciliation.shifts.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('shift-reconciliation::shift-reconciliation.shifts.plural');
    }

    public static function getNavigationGroup(): string
    {
        return __('shift-reconciliation::shift-reconciliation.nav_group');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tab::make(__('shift-reconciliation::shift-reconciliation.tabs.shift'))
                        ->icon(Heroicon::OutlinedClock)
                        ->columns(2)
                        ->schema([
                            Select::make('station_id')
                                ->label(__('core.fields.station'))
                                ->relationship('station', 'name')
                                ->required()
                                ->native(false)
                                ->preload(),

                            Select::make('employee_id')
                                ->label(__('shift-reconciliation::shift-reconciliation.fields.employee'))
                                ->options(fn (): array => Employee::query()
                                    ->orderBy('last_name')
                                    ->get()
                                    ->mapWithKeys(fn (Employee $employee): array => [
                                        $employee->getKey() => $employee->full_name,
                                    ])
                                    ->all())
                                ->searchable()
                                ->native(false),

                            DateTimePicker::make('starts_at')
                                ->label(__('shift-reconciliation::shift-reconciliation.fields.starts_at'))
                                ->seconds(false)
                                ->required(),

                            DateTimePicker::make('ends_at')
                                ->label(__('shift-reconciliation::shift-reconciliation.fields.ends_at'))
                                ->seconds(false)
                                ->after('starts_at'),
                        ]),

                    Tab::make(__('shift-reconciliation::shift-reconciliation.tabs.counting'))
                        ->icon(Heroicon::OutlinedCalculator)
                        ->schema([
                            Repeater::make('entries')
                                ->label(__('shift-reconciliation::shift-reconciliation.fields.entries'))
                                ->relationship('entries')
                                ->columns(4)
                                ->defaultItems(1)
                                ->schema([
                                    Select::make('payment_method')
                                        ->label(__('shift-reconciliation::shift-reconciliation.fields.payment_method'))
                                        ->options(PaymentMethod::class)
                                        ->default(PaymentMethod::Cash)
                                        ->required()
                                        ->native(false),

                                    MoneyInput::make('expected_amount_cents')
                                        ->label(__('shift-reconciliation::shift-reconciliation.fields.expected')),

                                    MoneyInput::make('counted_amount_cents')
                                        ->label(__('shift-reconciliation::shift-reconciliation.fields.counted')),

                                    TextInput::make('note')
                                        ->label(__('shift-reconciliation::shift-reconciliation.fields.note'))
                                        ->maxLength(255),
                                ]),
                        ]),

                    Tab::make(__('shift-reconciliation::shift-reconciliation.tabs.closing'))
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->columns(2)
                        ->schema([
                            Textarea::make('notes')
                                ->label(__('shift-reconciliation::shift-reconciliation.fields.notes'))
                                ->rows(3)
                                ->columnSpanFull(),

                            Placeholder::make('difference_display')
                                ->label(__('shift-reconciliation::shift-reconciliation.fields.difference'))
                                ->content(fn (?Shift $record): string => $record === null
                                    ? '—'
                                    : Money::format($record->difference_cents))
                                ->visible(fn (?Shift $record): bool => $record !== null),

                            Placeholder::make('cancel_reason_display')
                                ->label(__('shift-reconciliation::shift-reconciliation.fields.cancel_reason'))
                                ->content(fn (?Shift $record): string => (string) $record?->cancel_reason)
                                ->visible(fn (?Shift $record): bool => filled($record?->cancel_reason)),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('starts_at')
                    ->label(__('shift-reconciliation::shift-reconciliation.fields.starts_at'))
                    ->dateTime('d.m.Y H:i', 'Europe/Berlin')
                    ->sortable(),

                TextColumn::make('station.name')
                    ->label(__('core.fields.station'))
                    ->sortable(),

                TextColumn::make('employee.last_name')
                    ->label(__('shift-reconciliation::shift-reconciliation.fields.employee'))
                    ->formatStateUsing(fn (Shift $record): string => $record->employee->full_name ?? '—')
                    ->placeholder('—'),

                TextColumn::make('difference_cents')
                    ->label(__('shift-reconciliation::shift-reconciliation.fields.difference'))
                    ->formatStateUsing(fn (int $state): string => Money::format($state))
                    ->color(fn (int $state): string => $state === 0 ? 'success' : 'danger')
                    ->alignEnd(),

                TextColumn::make('status')
                    ->label(__('core.fields.status'))
                    ->badge()
                    ->sortable(),
            ])
            ->defaultSort('starts_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('core.fields.status'))
                    ->options(ShiftStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('submit')
                    ->label(__('shift-reconciliation::shift-reconciliation.actions.submit'))
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription(__('shift-reconciliation::shift-reconciliation.actions.submit_confirm'))
                    ->authorize(fn (Shift $record): bool => auth()->user()?->can('submit', $record) === true)
                    ->action(function (Shift $record): void {
                        app(SubmitShift::class)->execute($record, auth()->user());

                        Notification::make()
                            ->title(__('shift-reconciliation::shift-reconciliation.actions.submitted'))
                            ->success()
                            ->send();
                    }),

                Action::make('approve')
                    ->label(__('shift-reconciliation::shift-reconciliation.actions.approve'))
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription(__('shift-reconciliation::shift-reconciliation.actions.approve_confirm'))
                    ->authorize(fn (Shift $record): bool => auth()->user()?->can('approve', $record) === true)
                    ->action(function (Shift $record): void {
                        app(ApproveShift::class)->execute($record, auth()->user());

                        Notification::make()
                            ->title(__('shift-reconciliation::shift-reconciliation.actions.approved'))
                            ->success()
                            ->send();
                    }),

                Action::make('cancel')
                    ->label(__('shift-reconciliation::shift-reconciliation.actions.cancel'))
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription(__('shift-reconciliation::shift-reconciliation.actions.cancel_confirm'))
                    ->schema([
                        Textarea::make('reason')
                            ->label(__('shift-reconciliation::shift-reconciliation.fields.cancel_reason'))
                            ->required()
                            ->rows(2),
                    ])
                    ->authorize(fn (Shift $record): bool => auth()->user()?->can('cancel', $record) === true)
                    ->action(function (Shift $record, array $data): void {
                        app(CancelShift::class)->execute($record, auth()->user(), $data['reason']);

                        Notification::make()
                            ->title(__('shift-reconciliation::shift-reconciliation.actions.cancelled'))
                            ->success()
                            ->send();
                    }),

                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShifts::route('/'),
            'create' => CreateShift::route('/create'),
            'edit' => EditShift::route('/{record}/edit'),
        ];
    }
}
