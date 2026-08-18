<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Enums\PolicyStatus;
use App\Enums\PolicyType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PoliciesRelationManager extends RelationManager
{
    protected static string $relationship = 'policies';

    protected static ?string $title = 'وثائق التأمين';

    protected static ?string $modelLabel = 'وثيقة';

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
                Forms\Components\Select::make('type')
                    ->label('نوع التأمين')
                    ->options(PolicyType::class)
                    ->default(PolicyType::Motor)
                    ->required(),
                Forms\Components\TextInput::make('policy_number')
                    ->label('رقم الوثيقة')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\DatePicker::make('start_date')
                    ->label('تاريخ البداية')
                    ->required()
                    ->default(now()),
                Forms\Components\DatePicker::make('end_date')
                    ->label('تاريخ النهاية')
                    ->required()
                    ->default(now()->addYear()),
                Forms\Components\TextInput::make('premium_amount')
                    ->label('القسط (ج.م)')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('commission_rate')
                    ->label('نسبة العمولة (%)')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options(PolicyStatus::class)
                    ->default(PolicyStatus::Active)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('policy_number')
            ->columns([
                Tables\Columns\TextColumn::make('policy_number')
                    ->label('رقم الوثيقة')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('insuranceCompany.name')
                    ->label('شركة التأمين'),
                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('تاريخ الانتهاء')
                    ->date('Y-m-d'),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
                Tables\Columns\TextColumn::make('premium_amount')
                    ->label('القسط')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' ج.م'),
            ])
            ->defaultSort('end_date')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة وثيقة'),
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
