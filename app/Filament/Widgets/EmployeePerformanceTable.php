<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class EmployeePerformanceTable extends BaseWidget
{
    protected static ?string $heading = 'أداء الموظفين';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->withCount('assignedClients')
                    ->withCount('responsiblePolicies')
                    ->withSum('responsiblePolicies as commissions_sum', 'net_office_commission')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الموظف')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('assigned_clients_count')
                    ->label('عدد العملاء')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('responsible_policies_count')
                    ->label('عدد الوثائق')
                    ->badge(),
                Tables\Columns\TextColumn::make('commissions_sum')
                    ->label('إجمالي العمولات المحقّقة')
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
