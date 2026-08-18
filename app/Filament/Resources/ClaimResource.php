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
                Forms\Components\Section::make('بيانات المطالبة')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('العميل')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('policy_id')
                            ->label('الوثيقة')
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
                            ->label('رقم المطالبة')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('claim_type')
                            ->label('نوع المطالبة'),
                        Forms\Components\TextInput::make('claimed_amount')
                            ->label('المبلغ المطالَب به (ج.م)')
                            ->numeric(),
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options(ClaimStatus::class)
                            ->default(ClaimStatus::Reported)
                            ->required(),
                        Forms\Components\Select::make('responsible_user_id')
                            ->label('الموظف المسؤول')
                            ->relationship('responsibleUser', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Textarea::make('description')
                            ->label('وصف المطالبة')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('المستندات والملاحظات')
                    ->schema([
                        Forms\Components\FileUpload::make('documents')
                            ->label('مستندات المطالبة')
                            ->multiple()
                            ->directory('claims')
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
                Tables\Columns\TextColumn::make('claim_number')
                    ->label('رقم المطالبة')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('العميل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('policy.policy_number')
                    ->label('الوثيقة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('policy.insuranceCompany.name')
                    ->label('شركة التأمين'),
                Tables\Columns\TextColumn::make('claim_type')
                    ->label('النوع')
                    ->default('—'),
                Tables\Columns\TextColumn::make('claimed_amount')
                    ->label('المبلغ')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' ج.م')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
                Tables\Columns\TextColumn::make('responsibleUser.name')
                    ->label('الموظف المسؤول')
                    ->default('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
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
}
