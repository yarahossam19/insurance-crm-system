<?php

namespace App\Filament\Widgets;

use App\Enums\PolicyType;
use App\Models\Policy;
use Filament\Widgets\ChartWidget;

class PolicyTypeChart extends ChartWidget
{
    protected static ?string $heading = 'توزيع الوثائق حسب النوع';

    protected static bool $isLazy = false;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $counts = Policy::selectRaw('type, count(*) as aggregate')
            ->groupBy('type')
            ->pluck('aggregate', 'type');

        $labels = [];
        $values = [];

        foreach (PolicyType::cases() as $case) {
            if (($counts[$case->value] ?? 0) > 0) {
                $labels[] = $case->getLabel();
                $values[] = $counts[$case->value];
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => ['#3C9284', '#57C0AE', '#D9AE68', '#8CA096', '#5B3F14', '#123F39', '#A8792E'],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public function getHeading(): string
    {
        return __('توزيع الوثائق حسب النوع');
    }
}
