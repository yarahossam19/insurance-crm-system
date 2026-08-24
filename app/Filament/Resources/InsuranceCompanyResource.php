<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InsuranceCompanyResource\Pages;
use App\Filament\Resources\InsuranceCompanyResource\RelationManagers;
use App\Models\InsuranceCompany;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InsuranceCompanyResource extends Resource
{
    protected static ?string $model = InsuranceCompany::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'الإعدادات';

    protected static ?string $navigationLabel = 'شركات التأمين';

    protected static ?string $modelLabel = 'شركة تأمين';

    protected static ?string $pluralModelLabel = 'شركات التأمين';

    protected static ?int $navigationSort = 90;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('بيانات الشركة'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('اسم الشركة'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('contact_person')
                            ->label(__('مسؤول التواصل')),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('التليفون'))
                            ->tel(),
                        Forms\Components\TextInput::make('email')
                            ->label(__('البريد الإلكتروني'))
                            ->email(),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('نشطة'))
                            ->default(true),
                        Forms\Components\Textarea::make('address')
                            ->label(__('العنوان'))
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('ملاحظات'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('اسم الشركة'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('contact_person')
                    ->label(__('مسؤول التواصل'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('التليفون'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('البريد الإلكتروني'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('policies_count')
                    ->label(__('عدد الوثائق'))
                    ->counts('policies')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('نشطة'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('تاريخ الإضافة'))
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('الحالة'))
                    ->placeholder(__('الكل'))
                    ->trueLabel(__('نشطة'))
                    ->falseLabel(__('غير نشطة')),
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
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PoliciesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInsuranceCompanies::route('/'),
            'create' => Pages\CreateInsuranceCompany::route('/create'),
            'view' => Pages\ViewInsuranceCompany::route('/{record}'),
            'edit' => Pages\EditInsuranceCompany::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('الإعدادات');
    }

    public static function getNavigationLabel(): string
    {
        return __('شركات التأمين');
    }

    public static function getModelLabel(): string
    {
        return __('شركة تأمين');
    }

    public static function getPluralModelLabel(): string
    {
        return __('شركات التأمين');
    }
}
