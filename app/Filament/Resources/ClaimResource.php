<?php

namespace App\Filament\Resources;

use App\Enums\ClaimStatus;
use App\Filament\Resources\ClaimResource\Pages;
use App\Filament\Resources\ClaimResource\RelationManagers;
use App\Models\Claim;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClaimResource extends Resource
{
    protected static ?string $model = Claim::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'الوثائق والتجديدات';

    protected static ?string $navigationLabel = 'المطالبات';

    protected static ?string $modelLabel = 'مطالبة';

    protected static ?string $pluralModelLabel = 'المطالبات';

    protected static ?int $navigationSort = 25;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('بيانات المطالبة'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label(__('العميل'))
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('policy_id')
                            ->label(__('الوثيقة'))
                            ->relationship(
                                'policy',
                                'policy_number',
                                fn (Forms\Get $get, $query) => $get('client_id')
                                    ? $query->where('client_id', $get('client_id'))
                                    : $query,
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('claim_number')
                            ->label(__('رقم المطالبة'))
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('claim_type')
                            ->label(__('نوع المطالبة')),
                        Forms\Components\TextInput::make('claimed_amount')
                            ->label(__('المبلغ المطالَب به (ج.م)'))
                            ->numeric(),
                        Forms\Components\Select::make('status')
                            ->label(__('الحالة'))
                            ->options(ClaimStatus::class)
                            ->default(ClaimStatus::Reported)
                            ->required(),
                        Forms\Components\Select::make('responsible_user_id')
                            ->label(__('الموظف المسؤول'))
                            ->relationship('responsibleUser', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('وصف المطالبة'))
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make(__('المستندات والملاحظات'))
                    ->schema([
                        Forms\Components\FileUpload::make('documents')
                            ->label(__('مستندات المطالبة'))
                            ->multiple()
                            ->directory('claims')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
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
                Tables\Columns\TextColumn::make('claim_number')
                    ->label(__('رقم المطالبة'))
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('العميل'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('policy.policy_number')
                    ->label(__('الوثيقة'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('policy.insuranceCompany.name')
                    ->label(__('شركة التأمين')),
                Tables\Columns\TextColumn::make('claim_type')
                    ->label(__('النوع'))
                    ->default('—'),
                Tables\Columns\TextColumn::make('claimed_amount')
                    ->label(__('المبلغ'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix(__(' ج.م'))
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->badge(),
                Tables\Columns\TextColumn::make('responsibleUser.name')
                    ->label(__('الموظف المسؤول'))
                    ->default('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('تاريخ التسجيل'))
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('الحالة'))
                    ->options(ClaimStatus::class),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\FollowUpsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClaims::route('/'),
            'create' => Pages\CreateClaim::route('/create'),
            'view' => Pages\ViewClaim::route('/{record}'),
            'edit' => Pages\EditClaim::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('الوثائق والتجديدات');
    }

    public static function getNavigationLabel(): string
    {
        return __('المطالبات');
    }

    public static function getModelLabel(): string
    {
        return __('مطالبة');
    }

    public static function getPluralModelLabel(): string
    {
        return __('المطالبات');
    }
}
