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
            self::Motor => 'تأمين سيارات',
            self::Health => 'تأمين صحي',
            self::Life => 'تأمين حياة',
            self::Property => 'تأمين ممتلكات',
            self::Marine => 'تأمين بحري',
            self::Travel => 'تأمين سفر',
            self::Other => 'أخرى',
        };
    }
}
