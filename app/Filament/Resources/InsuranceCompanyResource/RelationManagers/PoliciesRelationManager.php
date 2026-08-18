<?php

namespace App\Filament\Resources\InsuranceCompanyResource\RelationManagers;

use App\Models\Policy;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PoliciesRelationManager extends RelationManager
{
    protected static string $relationship = 'policies';

    protected static ?string $title = 'وثائق هذه الشركة';

    protected static ?string $modelLabel = 'وثيقة';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('policy_number')
            ->columns([
                Tables\Columns\TextColumn::make('policy_number')
                    ->label('رقم الوثيقة')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('العميل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('تاريخ الانتهاء')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
                Tables\Columns\TextColumn::make('premium_amount')
                    ->label('القسط')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' ج.م'),
                Tables\Columns\TextColumn::make('net_office_commission')
                    ->label('صافي العمولة')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' ج.م')
                    ->weight('bold')
                    ->color('primary'),
            ])
            ->defaultSort('end_date')
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('عرض')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Policy $record) => route('filament.admin.resources.policies.view', $record)),
            ])
            ->bulkActions([]);
    }
}
