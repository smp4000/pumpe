<?php

declare(strict_types=1);

namespace Modules\Playground\Filament\Resources\Notes\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Modules\Playground\Filament\Resources\Notes\NoteResource;

class ManageNotes extends ManageRecords
{
    protected static string $resource = NoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
