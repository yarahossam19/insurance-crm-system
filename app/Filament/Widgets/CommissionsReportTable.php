<?php

namespace App\Filament\Widgets;

use App\Models\Policy;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class CommissionsReportTable extends BaseWidget
{
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 20;

    public function table(Table $table): Table
    {
        return $table
            ->query(Policy::query()->with(['client', 'insuranceCompany']))
            ->columns([
                Tables\Columns\TextColumn::make('policy_number')
                    ->label(__('رقم الوثيقة'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('العميل'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('insuranceCompany.name')
                    ->label(__('شركة التأمين')),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('تاريخ البداية'))
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('premium_amount')
                    ->label(__('القسط'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix(__(' ج.م')),
                Tables\Columns\TextColumn::make('commission_rate')
                    ->label(__('نسبة العمولة (%)')),
                Tables\Columns\TextColumn::make('commission_amount')
                    ->label(__('قيمة العمولة'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix(__(' ج.م')),
                Tables\Columns\TextColumn::make('employee_commission_amount')
                    ->label(__('عمولة الموظف'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix(__(' ج.م')),
                Tables\Columns\TextColumn::make('net_office_commission')
                    ->label(__('صافي عمولة المكتب'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix(__(' ج.م'))
                    ->weight('bold')
                    ->color('primary'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label(__('السنة'))
                    ->options($this->yearOptions())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'],
                        fn (Builder $query, $year) => $query->whereYear('start_date', $year)
                    )),
                Tables\Filters\SelectFilter::make('month')
                    ->label(__('الشهر'))
                    ->options($this->monthOptions())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'],
                        fn (Builder $query, $month) => $query->whereMonth('start_date', $month)
                    )),
            ])
            ->defaultSort('start_date', 'desc')
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
        return __('تقرير العمولات');
    }
}
