<?php

namespace App\Filament\Widgets;

use App\Models\Policy;
use Filament\Widgets\ChartWidget;

class RevenueTrendChart extends ChartWidget
{
    protected static ?string $heading = 'الأقساط والعمولات خلال آخر 6 شهور';

    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn (int $i) => now()->subMonths($i));

        $labels = $months->map(fn ($m) => $m->translatedFormat('M Y'))->toArray();

        $premiums = $months->map(
            fn ($m) => (float) Policy::whereYear('start_date', $m->year)
                ->whereMonth('start_date', $m->month)
                ->sum('premium_amount')
        )->toArray();

        $commissions = $months->map(
            fn ($m) => (float) Policy::whereYear('start_date', $m->year)
                ->whereMonth('start_date', $m->month)
                ->sum('net_office_commission')
        )->toArray();

        return [
            'datasets' => [
                [
                    'label' => __('الأقساط'),
                    'data' => $premiums,
                    'borderColor' => '#57C0AE',
                    'backgroundColor' => 'rgba(87, 192, 174, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => __('صافي العمولات'),
                    'data' => $commissions,
                    'borderColor' => '#D9AE68',
                    'backgroundColor' => 'rgba(217, 174, 104, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getHeading(): string
    {
        return __('الأقساط والعمولات خلال آخر 6 شهور');
    }
}
