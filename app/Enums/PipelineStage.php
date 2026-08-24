<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PipelineStage: string implements HasColor, HasIcon, HasLabel
{
    case NewLead = 'new_lead';
    case Contacted = 'contacted';
    case Quotation = 'quotation';
    case FollowUp = 'follow_up';
    case Approved = 'approved';
    case Issued = 'issued';
    case Renewed = 'renewed';
    case Lost = 'lost';

    public function getLabel(): string
    {
        return match ($this) {
            self::NewLead => __('عميل محتمل جديد'),
            self::Contacted => __('تم التواصل'),
            self::Quotation => __('عرض سعر'),
            self::FollowUp => __('متابعة'),
            self::Approved => __('موافقة'),
            self::Issued => __('تم الإصدار'),
            self::Renewed => __('تم التجديد'),
            self::Lost => __('فاقد'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NewLead => 'gray',
            self::Contacted => 'info',
            self::Quotation => 'warning',
            self::FollowUp => 'warning',
            self::Approved => 'success',
            self::Issued => 'success',
            self::Renewed => 'primary',
            self::Lost => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::NewLead => 'heroicon-o-sparkles',
            self::Contacted => 'heroicon-o-phone',
            self::Quotation => 'heroicon-o-document-currency-dollar',
            self::FollowUp => 'heroicon-o-clock',
            self::Approved => 'heroicon-o-hand-thumb-up',
            self::Issued => 'heroicon-o-document-check',
            self::Renewed => 'heroicon-o-arrow-path',
            self::Lost => 'heroicon-o-x-circle',
        };
    }
}
