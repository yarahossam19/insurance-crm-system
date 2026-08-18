<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ClaimStatus: string implements HasColor, HasLabel
{
    case Reported = 'reported';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Settled = 'settled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Reported => 'مُبلّغ عنها',
            self::UnderReview => 'قيد المراجعة',
            self::Approved => 'موافَق عليها',
            self::Rejected => 'مرفوضة',
            self::Settled => 'مسوّاة',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Reported => 'gray',
            self::UnderReview => 'warning',
            self::Approved => 'info',
            self::Rejected => 'danger',
            self::Settled => 'success',
        };
    }
}
