<?php

namespace App\Filament\Widgets;

use App\Enums\PolicyStatus;
use App\Models\Client;
use App\Models\Collection as CollectionModel;
use App\Models\Policy;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class DashboardStats extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    /**
     * Daily counts/sums for the last 7 days, oldest first — used both for the
     * sparkline on each stat card and for the growth-vs-last-week description.
     *
     * @return array{0: array<int, float>, 1: float}
     */
    protected function dailySeries(\Closure $queryForDay): array
    {
        $days = collect(range(6, 0))->map(fn (int $i) => now()->subDays($i)->startOfDay());

        $series = $days->map(fn (Carbon $day) => (float) $queryForDay($day));

        $thisWeek = $series->sum();
        $lastWeekStart = now()->subDays(13)->startOfDay();
        $lastWeekEnd = now()->subDays(7)->endOfDay();
        $lastWeek = (float) $queryForDay(null, [$lastWeekStart, $lastWeekEnd]);

        $change = $lastWeek > 0 ? round((($thisWeek - $lastWeek) / $lastWeek) * 100) : ($thisWeek > 0 ? 100 : 0);

        return [$series->toArray(), $change];
    }

    protected function getStats(): array
    {
        $activePolicies = Policy::where('status', PolicyStatus::Active->value);
        $totalPremiums = (clone $activePolicies)->sum('premium_amount');
        $totalCommissions = (clone $activePolicies)->sum('net_office_commission');
        $collectedThisMonth = CollectionModel::whereMonth('collected_at', now()->month)
            ->whereYear('collected_at', now()->year)
            ->sum('amount');

        [$clientsSeries, $clientsChange] = $this->dailySeries(fn (?Carbon $day, ?array $range = null) => $range
            ? Client::whereBetween('created_at', $range)->count()
            : Client::whereDate('created_at', $day)->count());

        [$policiesSeries, $policiesChange] = $this->dailySeries(fn (?Carbon $day, ?array $range = null) => $range
            ? Policy::whereBetween('created_at', $range)->count()
            : Policy::whereDate('created_at', $day)->count());

        [$premiumsSeries] = $this->dailySeries(fn (?Carbon $day, ?array $range = null) => $range
            ? Policy::whereBetween('created_at', $range)->sum('premium_amount')
            : Policy::whereDate('created_at', $day)->sum('premium_amount'));

        [$commissionsSeries] = $this->dailySeries(fn (?Carbon $day, ?array $range = null) => $range
            ? Policy::whereBetween('created_at', $range)->sum('net_office_commission')
            : Policy::whereDate('created_at', $day)->sum('net_office_commission'));

        [$collectionsSeries, $collectionsChange] = $this->dailySeries(fn (?Carbon $day, ?array $range = null) => $range
            ? CollectionModel::whereBetween('collected_at', $range)->sum('amount')
            : CollectionModel::whereDate('collected_at', $day)->sum('amount'));

        return [
            Stat::make('إجمالي العملاء', Client::count())
                ->description(($clientsChange >= 0 ? '+' : '').$clientsChange.'% خلال أسبوع')
                ->descriptionIcon($clientsChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($clientsSeries)
                ->icon('heroicon-o-users')
                ->color('primary'),
            Stat::make('الوثائق السارية', (clone $activePolicies)->count())
                ->description(($policiesChange >= 0 ? '+' : '').$policiesChange.'% خلال أسبوع')
                ->descriptionIcon($policiesChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($policiesSeries)
                ->icon('heroicon-o-document-check')
                ->color('success'),
            Stat::make('إجمالي الأقساط السارية', number_format((float) $totalPremiums).' ج.م')
                ->description('اتجاه آخر 7 أيام')
                ->chart($premiumsSeries)
                ->icon('heroicon-o-banknotes')
                ->color('gray'),
            Stat::make('إجمالي العمولات السارية', number_format((float) $totalCommissions).' ج.م')
                ->description('اتجاه آخر 7 أيام')
                ->chart($commissionsSeries)
                ->icon('heroicon-o-currency-dollar')
                ->color('warning'),
            Stat::make('محصّل هذا الشهر', number_format((float) $collectedThisMonth).' ج.م')
                ->description(($collectionsChange >= 0 ? '+' : '').$collectionsChange.'% خلال أسبوع')
                ->descriptionIcon($collectionsChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($collectionsSeries)
                ->icon('heroicon-o-arrow-trending-up')
                ->color('success'),
        ];
    }
}
