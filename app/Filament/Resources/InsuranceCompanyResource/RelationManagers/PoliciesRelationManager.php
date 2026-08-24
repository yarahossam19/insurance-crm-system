<?php

namespace App\Filament\Resources\InsuranceCompanyResource\RelationManagers;

use App\Models\Policy;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                    ->label(__('رقم الوثيقة'))
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('العميل'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('النوع'))
                    ->badge(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('تاريخ الانتهاء'))
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->badge(),
                Tables\Columns\TextColumn::make('premium_amount')
                    ->label(__('القسط'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix(__(' ج.م')),
                Tables\Columns\TextColumn::make('net_office_commission')
                    ->label(__('صافي العمولة'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix(__(' ج.م'))
                    ->weight('bold')
                    ->color('primary'),
            ])
            ->defaultSort('end_date')
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('عرض'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (Policy $record) => route('filament.admin.resources.policies.view', $record)),
            ])
            ->bulkActions([]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('وثائق هذه الشركة');
    }

    protected static function getModelLabel(): ?string
    {
        return __('وثيقة');
    }
}
