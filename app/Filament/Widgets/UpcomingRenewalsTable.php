<?php

namespace App\Filament\Widgets;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingRenewalsTable extends BaseWidget
{
    protected static ?string $heading = 'أقرب التجديدات';

    protected static ?int $sort = 6;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Policy::query()
                    ->where('status', '!=', PolicyStatus::Cancelled->value)
                    ->orderBy('end_date')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('policy_number')
                    ->label(__('رقم الوثيقة')),
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('العميل')),
                Tables\Columns\TextColumn::make('insuranceCompany.name')
                    ->label(__('شركة التأمين')),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('تاريخ الانتهاء'))
                    ->date('Y-m-d')
                    ->description(fn (Policy $record) => $record->days_to_expiry >= 0
                        ? __('متبقي :days يوم', ['days' => $record->days_to_expiry])
                        : __('منتهية منذ ').abs($record->days_to_expiry).__(' يوم'))
                    ->color(fn (Policy $record) => match (true) {
                        $record->days_to_expiry < 0 => 'danger',
                        $record->days_to_expiry <= 15 => 'danger',
                        $record->days_to_expiry <= 30 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('responsibleUser.name')
                    ->label(__('الموظف المسؤول'))
                    ->default('—'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('عرض'))
                    ->url(fn (Policy $record) => route('filament.admin.resources.policies.view', $record))
                    ->icon('heroicon-o-eye'),
            ])
            ->paginated(false);
    }

    protected function getTableHeading(): string
    {
        return __('أقرب التجديدات');
    }
}
