<?php

namespace App\Filament\Widgets;

use App\Models\InsuranceCompany;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CompanyPerformanceTable extends BaseWidget
{
    protected static ?string $heading = 'أداء شركات التأمين';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                InsuranceCompany::query()
                    ->withCount('policies')
                    ->withSum('policies as premiums_sum', 'premium_amount')
                    ->withSum('policies as commissions_sum', 'net_office_commission')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('شركة التأمين'))
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('policies_count')
                    ->label(__('عدد الوثائق'))
                    ->badge(),
                Tables\Columns\TextColumn::make('premiums_sum')
                    ->label(__('إجمالي الأقساط'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix(__(' ج.م'))
                    ->default(0),
                Tables\Columns\TextColumn::make('commissions_sum')
                    ->label(__('إجمالي العمولات'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix(__(' ج.م'))
                    ->default(0)
                    ->weight('bold')
                    ->color('primary'),
            ])
            ->defaultSort('commissions_sum', 'desc')
            ->paginated(false);
    }

    protected function getTableHeading(): string
    {
        return __('أداء شركات التأمين');
    }
}
