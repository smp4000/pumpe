<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ShiftStatus: string implements HasColor, HasLabel
{
    // In Erfassung — frei editierbar
    case Open = 'open';

    // Vom Mitarbeiter eingereicht — wartet auf Freigabe
    case Submitted = 'submitted';

    // Freigegeben — unveränderlich (GoBD), nur Storno möglich
    case Approved = 'approved';

    // Storniert — unveränderlich, Ersatz durch neue Abrechnung
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return __('shift-reconciliation::shift-reconciliation.status.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Open => 'gray',
            self::Submitted => 'warning',
            self::Approved => 'success',
            self::Cancelled => 'danger',
        };
    }
}
