<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EmployeeStatus: string implements HasColor, HasLabel
{
    case Active = 'active';

    // Ruhendes Arbeitsverhältnis (Elternzeit, Langzeiterkrankung …)
    case Inactive = 'inactive';

    case Terminated = 'terminated';

    public function getLabel(): string
    {
        return __('core.employee_status.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'warning',
            self::Terminated => 'gray',
        };
    }
}
