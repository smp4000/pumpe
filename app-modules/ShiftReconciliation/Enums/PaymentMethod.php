<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case Cash = 'cash';

    case DebitCard = 'debit_card';

    case CreditCard = 'credit_card';

    case Voucher = 'voucher';

    case Other = 'other';

    public function getLabel(): string
    {
        return __('shift-reconciliation::shift-reconciliation.payment_method.'.$this->value);
    }
}
