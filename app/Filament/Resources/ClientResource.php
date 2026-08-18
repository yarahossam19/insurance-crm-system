<?php

namespace App\Filament\Resources;

use App\Enums\ClientType;
use App\Enums\PipelineStage;
use App\Filament\Exports\ClientExporter;
use App\Filament\Imports\ClientImporter;
use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'العملاء والمبيعات';

    protected static ?string $navigationLabel = 'العملاء';

    protected static ?string $modelLabel = 'عميل';

    protected static ?string $pluralModelLabel = 'العملاء';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('نوع العميل والبيانات الأساسية')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Radio::make('type')
                            ->label('نوع العميل')
                            ->options(ClientType::class)
                            ->default(ClientType::Individual)
                            ->inline()
                            ->live()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->label(fn (Forms\Get $get) => $get('type') === ClientType::Company->value ? 'اسم الشركة' : 'الاسم بالكامل')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم التليفون')
                            ->tel(),
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email(),
                        Forms\Components\TextInput::make('national_id')
                            ->label('الرقم القومي')
                            ->visible(fn (Forms\Get $get) => $get('type') === ClientType::Individual->value),
                        Forms\Components\TextInput::make('commercial_register')
                            ->label('السجل التجاري')
                            ->visible(fn (Forms\Get $get) => $get('type') === ClientType::Company->value),
                        Forms\Components\Textarea::make('address')
                            ->label('العنوان')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('المتابعة والمسؤول')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('pipeline_stage')
                            ->label('مرحلة المتابعة (Pipeline)')
                            ->options(PipelineStage::class)
                            ->default(PipelineStage::NewLead)
                            ->required(),
                        Forms\Components\Select::make('assigned_to')
                            ->label('الموظف المسؤول')
                            ->relationship('assignedUser', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

                Forms\Components\Section::make('المستندات والملاحظات')
                    ->schema([
                        Forms\Components\FileUpload::make('documents')
                            ->label('المستندات (PDF / صور)')
                            ->multiple()
                            ->directory('clients')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->reorderable()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات عامة')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->label('استيراد من Excel')
                    ->importer(ClientImporter::class),
                Tables\Actions\ExportAction::make()
                    ->label('تصدير')
                    ->exporter(ClientExporter::class),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Client $record) => $record->phone),
                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
                Tables\Columns\TextColumn::make('pipeline_stage')
                    ->label('المرحلة')
                    ->badge(),
                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('الموظف المسؤول')
                    ->default('—'),
                Tables\Columns\TextColumn::make('policies_count')
                    ->label('عدد الوثائق')
                    ->counts('policies')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('النوع')
                    ->options(ClientType::class),
                Tables\Filters\SelectFilter::make('pipeline_stage')
                    ->label('المرحلة')
                    ->options(PipelineStage::class),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('الموظف المسؤول')
                    ->relationship('assignedUser', 'name'),
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
            RelationManagers\QuotationsRelationManager::class,
            RelationManagers\PoliciesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
