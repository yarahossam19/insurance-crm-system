<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardStats;
use App\Filament\Widgets\EmployeePerformanceTable;
use App\Filament\Widgets\PendingFollowUpsTable;
use App\Filament\Widgets\PipelineFunnelChart;
use App\Filament\Widgets\PolicyTypeChart;
use App\Filament\Widgets\RenewalStatsOverview;
use App\Filament\Widgets\RevenueTrendChart;
use App\Filament\Widgets\UpcomingRenewalsTable;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int|array
    {
        return 2;
    }

    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            DashboardStats::class,
            RevenueTrendChart::class,
            PolicyTypeChart::class,
            PipelineFunnelChart::class,
            RenewalStatsOverview::class,
            UpcomingRenewalsTable::class,
            PendingFollowUpsTable::class,
            EmployeePerformanceTable::class,
        ];
    }
}
