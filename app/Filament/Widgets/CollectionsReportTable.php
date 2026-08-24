<?php

namespace App\Filament\Widgets;

use App\Models\Collection;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class CollectionsReportTable extends BaseWidget
{
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 21;

    public function table(Table $table): Table
    {
        return $table
            ->query(Collection::query()->with(['policy.client', 'collector']))
            ->columns([
                Tables\Columns\TextColumn::make('policy.policy_number')
                    ->label(__('رقم الوثيقة'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('policy.client.name')
                    ->label(__('العميل'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('المبلغ'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix(__(' ج.م'))
                    ->weight('bold')
                    ->color('success'),
                Tables\Columns\TextColumn::make('collected_at')
                    ->label(__('تاريخ التحصيل'))
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('collector.name')
                    ->label(__('بواسطة'))
                    ->default('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label(__('السنة'))
                    ->options($this->yearOptions())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'],
                        fn (Builder $query, $year) => $query->whereYear('collected_at', $year)
                    )),
                Tables\Filters\SelectFilter::make('month')
                    ->label(__('الشهر'))
                    ->options($this->monthOptions())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'],
                        fn (Builder $query, $month) => $query->whereMonth('collected_at', $month)
                    )),
            ])
            ->defaultSort('collected_at', 'desc')
            ->paginated([10, 25, 50]);
    }

    /** @return array<int, string> */
    protected function yearOptions(): array
    {
        $currentYear = (int) now()->format('Y');

        return collect(range($currentYear, $currentYear - 4))
            ->mapWithKeys(fn (int $year) => [$year => (string) $year])
            ->all();
    }

    /** @return array<int, string> */
    protected function monthOptions(): array
    {
        return collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [$month => now()->setMonth($month)->translatedFormat('F')])
            ->all();
    }

    protected function getTableHeading(): string
    {
        return __('تقرير التحصيلات');
    }
}
