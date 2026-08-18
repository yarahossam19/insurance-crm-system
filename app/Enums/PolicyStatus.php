<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PolicyStatus: string implements HasColor, HasLabel
{
    case Active = 'active';
    case PendingRenewal = 'pending_renewal';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Active => 'سارية',
            self::PendingRenewal => 'قيد التجديد',
            self::Expired => 'منتهية',
            self::Cancelled => 'ملغاة',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::PendingRenewal => 'warning',
            self::Expired => 'danger',
            self::Cancelled => 'gray',
        };
    }
}
