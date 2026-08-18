<?php

namespace App\Filament\Widgets;

use App\Models\Policy;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RenewalStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $tiers = [
            ['label' => 'خلال 90 يوم', 'from' => 61, 'to' => 90, 'color' => 'gray'],
            ['label' => 'خلال 60 يوم', 'from' => 31, 'to' => 60, 'color' => 'info'],
            ['label' => 'خلال 30 يوم', 'from' => 16, 'to' => 30, 'color' => 'warning'],
            ['label' => 'خلال 15 يوم', 'from' => 8, 'to' => 15, 'color' => 'warning'],
            ['label' => 'خلال 7 أيام', 'from' => 0, 'to' => 7, 'color' => 'danger'],
        ];

        $stats = array_map(
            fn (array $tier) => Stat::make($tier['label'], Policy::expiringWithin($tier['from'], $tier['to'])->count())
                ->description('وثيقة محتاجة متابعة تجديد')
                ->color($tier['color']),
            $tiers
        );

        $stats[] = Stat::make('منتهية', Policy::expired()->count())
            ->description('وثيقة منتهية بدون تجديد')
            ->color('danger');

        return $stats;
    }
}
