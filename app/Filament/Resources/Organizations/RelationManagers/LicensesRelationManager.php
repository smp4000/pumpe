<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organizations\RelationManagers;

use App\Actions\LicenseModule;
use App\Enums\LicenseStatus;
use App\Models\Module;
use App\Models\ModuleLicense;
use App\Models\Organization;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Lizenzverwaltung eines Betriebs im Betreiber-Panel: Module buchen,
 * Status und Fristen ändern, Lizenzen entfernen.
 */
class LicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'moduleLicenses';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('core.licenses.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('module_id')
                ->label(__('core.licenses.module'))
                ->options(fn (): array => Module::query()
                    ->where('is_core', false)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->required()
                ->native(false)
                ->disabledOn('edit'),

            Select::make('status')
                ->label(__('core.fields.status'))
                ->options(LicenseStatus::class)
                ->default(LicenseStatus::Trial)
                ->required()
                ->native(false),

            DateTimePicker::make('trial_ends_at')
                ->label(__('core.licenses.trial_ends_at'))
                ->seconds(false),

            DateTimePicker::make('expires_at')
                ->label(__('core.licenses.expires_at'))
                ->seconds(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('module.name')
                    ->label(__('core.licenses.module')),

                TextColumn::make('status')
                    ->label(__('core.fields.status'))
                    ->badge(),

                TextColumn::make('trial_ends_at')
                    ->label(__('core.licenses.trial_ends_at'))
                    ->dateTime('d.m.Y H:i', 'Europe/Berlin')
                    ->placeholder('—'),

                TextColumn::make('expires_at')
                    ->label(__('core.licenses.expires_at'))
                    ->dateTime('d.m.Y H:i', 'Europe/Berlin')
                    ->placeholder('—'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('core.licenses.add'))
                    ->using(fn (array $data): ModuleLicense => $this->license($data)),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make()
                    ->using(fn (ModuleLicense $record, array $data): ModuleLicense => $this->license([
                        ...$data,
                        'module_id' => $record->module_id,
                    ])),

                DeleteAction::make(),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function license(array $data): ModuleLicense
    {
        /** @var Organization $organization */
        $organization = $this->getOwnerRecord();

        return app(LicenseModule::class)->execute(
            organization: $organization,
            module: Module::query()->findOrFail($data['module_id']),
            status: $data['status'] instanceof LicenseStatus
                ? $data['status']
                : LicenseStatus::from($data['status']),
            trialEndsAt: filled($data['trial_ends_at'] ?? null) ? \Illuminate\Support\Carbon::parse($data['trial_ends_at']) : null,
            expiresAt: filled($data['expires_at'] ?? null) ? \Illuminate\Support\Carbon::parse($data['expires_at']) : null,
        );
    }
}
