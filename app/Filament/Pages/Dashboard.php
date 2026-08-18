<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardStats;
use App\Filament\Widgets\RenewalStatsOverview;
use App\Filament\Widgets\UpcomingRenewalsTable;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            DashboardStats::class,
            RenewalStatsOverview::class,
            UpcomingRenewalsTable::class,
        ];
    }
}
