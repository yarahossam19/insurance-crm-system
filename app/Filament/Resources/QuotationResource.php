<?php

namespace App\Filament\Resources;

use App\Enums\PolicyType;
use App\Enums\QuotationStatus;
use App\Filament\Resources\QuotationResource\Pages;
use App\Models\Quotation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $navigationGroup = 'العملاء والمبيعات';

    protected static ?string $navigationLabel = 'عروض الأسعار';

    protected static ?string $modelLabel = 'عرض سعر';

    protected static ?string $pluralModelLabel = 'عروض الأسعار';

    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات العرض')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('العميل')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
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
                            ->label('حالة العرض')
                            ->options(QuotationStatus::class)
                            ->default(QuotationStatus::Sent)
                            ->required(),
                    ]),
                Forms\Components\Section::make('المستندات والملاحظات')
                    ->schema([
                        Forms\Components\FileUpload::make('documents')
                            ->label('نسخة العرض (PDF)')
                            ->multiple()
                            ->directory('quotations')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('insuranceCompany.name')
                    ->label('شركة التأمين')
                    ->searchable(),
                Tables\Columns\TextColumn::make('coverage_type')
                    ->label('نوع التغطية')
                    ->badge(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('القيمة')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' ج.م')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(QuotationStatus::class),
                Tables\Filters\SelectFilter::make('client_id')
                    ->label('العميل')
                    ->relationship('client', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'view' => Pages\ViewQuotation::route('/{record}'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
        ];
    }
}
