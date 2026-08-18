<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum QuotationStatus: string implements HasColor, HasLabel
{
    case Sent = 'sent';
    case UnderReview = 'under_review';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::Sent => 'مرسل',
            self::UnderReview => 'قيد المراجعة',
            self::Accepted => 'مقبول',
            self::Rejected => 'مرفوض',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Sent => 'info',
            self::UnderReview => 'warning',
            self::Accepted => 'success',
            self::Rejected => 'danger',
        };
    }
}
