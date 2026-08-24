<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PolicyType: string implements HasLabel
{
    case Motor = 'motor';
    case Health = 'health';
    case Life = 'life';
    case Property = 'property';
    case Marine = 'marine';
    case Travel = 'travel';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Motor => __('تأمين سيارات'),
            self::Health => __('تأمين صحي'),
            self::Life => __('تأمين حياة'),
            self::Property => __('تأمين ممتلكات'),
            self::Marine => __('تأمين بحري'),
            self::Travel => __('تأمين سفر'),
            self::Other => __('أخرى'),
        };
    }
}
