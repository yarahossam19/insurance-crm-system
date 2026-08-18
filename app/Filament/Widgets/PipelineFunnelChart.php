<?php

namespace App\Filament\Widgets;

use App\Enums\PipelineStage;
use App\Models\Client;
use Filament\Widgets\ChartWidget;

class PipelineFunnelChart extends ChartWidget
{
    protected static ?string $heading = 'توزيع العملاء على مسار المتابعة';

    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $counts = Client::selectRaw('pipeline_stage, count(*) as aggregate')
            ->groupBy('pipeline_stage')
            ->pluck('aggregate', 'pipeline_stage');

        $labels = [];
        $values = [];

        foreach (PipelineStage::cases() as $stage) {
            $labels[] = $stage->getLabel();
            $values[] = $counts[$stage->value] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'عدد العملاء',
                    'data' => $values,
                    'backgroundColor' => '#3C9284',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'scales' => [
                'x' => ['ticks' => ['stepSize' => 1]],
            ],
        ];
    }
}
