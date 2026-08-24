<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Enums\PolicyType;
use App\Enums\QuotationStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                    ->label(__('شركة التأمين'))
                    ->relationship('insuranceCompany', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('coverage_type')
                    ->label(__('نوع التغطية'))
                    ->options(PolicyType::class)
                    ->default(PolicyType::Motor)
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label(__('قيمة العرض (ج.م)'))
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label(__('الحالة'))
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
                    ->label(__('شركة التأمين'))
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('coverage_type')
                    ->label(__('نوع التغطية'))
                    ->badge(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('القيمة'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix(__(' ج.م'))
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('تاريخ الإرسال'))
                    ->dateTime('Y-m-d'),
            ])
            ->defaultSort('amount')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('إضافة عرض سعر')),
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

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('عروض الأسعار (مقارنة)');
    }

    protected static function getModelLabel(): ?string
    {
        return __('عرض سعر');
    }
}
