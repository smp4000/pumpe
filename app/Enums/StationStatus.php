<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StationStatus: string implements HasColor, HasLabel
{
    case Active = 'active';

    // Standort vorübergehend oder dauerhaft außer Betrieb (Umbau, Schließung)
    case Inactive = 'inactive';

    public function getLabel(): string
    {
        return __('core.station_status.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'gray',
        };
    }
}
