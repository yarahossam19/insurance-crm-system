<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Enums\PolicyStatus;
use App\Enums\PolicyType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                    ->label(__('شركة التأمين'))
                    ->relationship('insuranceCompany', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label(__('نوع التأمين'))
                    ->options(PolicyType::class)
                    ->default(PolicyType::Motor)
                    ->required(),
                Forms\Components\TextInput::make('policy_number')
                    ->label(__('رقم الوثيقة'))
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\DatePicker::make('start_date')
                    ->label(__('تاريخ البداية'))
                    ->required()
                    ->default(now()),
                Forms\Components\DatePicker::make('end_date')
                    ->label(__('تاريخ النهاية'))
                    ->required()
                    ->default(now()->addYear()),
                Forms\Components\TextInput::make('premium_amount')
                    ->label(__('القسط (ج.م)'))
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('commission_rate')
                    ->label(__('نسبة العمولة (%)'))
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label(__('الحالة'))
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
                    ->label(__('رقم الوثيقة'))
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('insuranceCompany.name')
                    ->label(__('شركة التأمين')),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('النوع'))
                    ->badge(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('تاريخ الانتهاء'))
                    ->date('Y-m-d'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->badge(),
                Tables\Columns\TextColumn::make('premium_amount')
                    ->label(__('القسط'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix(__(' ج.م')),
            ])
            ->defaultSort('end_date')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('إضافة وثيقة')),
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
        return __('وثائق التأمين');
    }

    protected static function getModelLabel(): ?string
    {
        return __('وثيقة');
    }
}
