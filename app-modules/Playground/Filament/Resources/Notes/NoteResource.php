<?php

declare(strict_types=1);

namespace Modules\Playground\Filament\Resources\Notes;

use App\Modules\Concerns\BelongsToModule;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Playground\Filament\Resources\Notes\Pages\ManageNotes;
use Modules\Playground\Models\Note;

/**
 * Beispiel-Resource des Referenzmoduls. Der BelongsToModule-Trait blendet
 * Navigation und Seiten ohne nutzbare Lizenz aus; die NotePolicy prüft
 * zusätzlich die Berechtigungen.
 */
class NoteResource extends Resource
{
    use BelongsToModule;

    protected static ?string $model = Note::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getModuleCode(): string
    {
        return 'playground';
    }

    public static function getModelLabel(): string
    {
        return __('playground::playground.notes.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('playground::playground.notes.plural');
    }

    public static function getNavigationGroup(): string
    {
        return __('playground::playground.nav_group');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label(__('playground::playground.notes.title'))
                ->required()
                ->maxLength(255),

            Select::make('station_id')
                ->label(__('core.fields.station'))
                ->relationship('station', 'name')
                ->native(false)
                ->preload(),

            Textarea::make('body')
                ->label(__('playground::playground.notes.body'))
                ->rows(4)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('playground::playground.notes.title'))
                    ->searchable(),

                TextColumn::make('station.name')
                    ->label(__('core.fields.station'))
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label(__('core.fields.created_at'))
                    ->dateTime('d.m.Y H:i', 'Europe/Berlin')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageNotes::route('/'),
        ];
    }
}
