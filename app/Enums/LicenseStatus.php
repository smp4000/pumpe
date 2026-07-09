<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LicenseStatus: string implements HasColor, HasLabel
{
    // Testphase — aktiv bis trial_ends_at
    case Trial = 'trial';

    // Gebucht und bezahlt — aktiv bis expires_at (null = unbefristet)
    case Active = 'active';

    // Gekündigt — bleibt bis expires_at nutzbar
    case Cancelled = 'cancelled';

    // Beendet — kein Zugriff mehr
    case Expired = 'expired';

    public function getLabel(): string
    {
        return __('core.license_status.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Trial => 'info',
            self::Active => 'success',
            self::Cancelled => 'warning',
            self::Expired => 'gray',
        };
    }
}
