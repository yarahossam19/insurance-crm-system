<?php

namespace App\Filament\Widgets;

use App\Enums\PolicyStatus;
use App\Models\Client;
use App\Models\Collection as CollectionModel;
use App\Models\Policy;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activePolicies = Policy::where('status', PolicyStatus::Active->value);
        $totalPremiums = (clone $activePolicies)->sum('premium_amount');
        $totalCommissions = (clone $activePolicies)->sum('net_office_commission');
        $collectedThisMonth = CollectionModel::whereMonth('collected_at', now()->month)
            ->whereYear('collected_at', now()->year)
            ->sum('amount');

        return [
            Stat::make('إجمالي العملاء', Client::count())
                ->icon('heroicon-o-users')
                ->color('primary'),
            Stat::make('الوثائق السارية', (clone $activePolicies)->count())
                ->icon('heroicon-o-document-check')
                ->color('success'),
            Stat::make('إجمالي الأقساط السارية', number_format((float) $totalPremiums).' ج.م')
                ->icon('heroicon-o-banknotes')
                ->color('gray'),
            Stat::make('إجمالي العمولات السارية', number_format((float) $totalCommissions).' ج.م')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning'),
            Stat::make('محصّل هذا الشهر', number_format((float) $collectedThisMonth).' ج.م')
                ->icon('heroicon-o-arrow-trending-up')
                ->color('success'),
        ];
    }
}
