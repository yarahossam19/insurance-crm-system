<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Enums\PolicyType;
use App\Enums\QuotationStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class QuotationsRelationManager extends RelationManager
{
    protected static string $relationship = 'quotations';

    protected static ?string $title = 'عروض الأسعار (مقارنة)';

    protected static ?string $modelLabel = 'عرض سعر';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('insurance_company_id')
                    ->label('شركة التأمين')
                    ->relationship('insuranceCompany', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('coverage_type')
                    ->label('نوع التغطية')
                    ->options(PolicyType::class)
                    ->default(PolicyType::Motor)
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label('قيمة العرض (ج.م)')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options(QuotationStatus::class)
                    ->default(QuotationStatus::Sent)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('insuranceCompany.name')
                    ->label('شركة التأمين')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('coverage_type')
                    ->label('نوع التغطية')
                    ->badge(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('القيمة')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' ج.م')
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime('Y-m-d'),
            ])
            ->defaultSort('amount')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة عرض سعر'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
