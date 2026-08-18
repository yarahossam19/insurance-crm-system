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
                    ->label('شركة التأمين')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('policies_count')
                    ->label('عدد الوثائق')
                    ->badge(),
                Tables\Columns\TextColumn::make('premiums_sum')
                    ->label('إجمالي الأقساط')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' ج.م')
                    ->default(0),
                Tables\Columns\TextColumn::make('commissions_sum')
                    ->label('إجمالي العمولات')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' ج.م')
                    ->default(0)
                    ->weight('bold')
                    ->color('primary'),
            ])
            ->defaultSort('commissions_sum', 'desc')
            ->paginated(false);
    }
}
