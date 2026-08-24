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
                Forms\Components\Section::make(__('نوع العميل والبيانات الأساسية'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Radio::make('type')
                            ->label(__('نوع العميل'))
                            ->options(ClientType::class)
                            ->default(ClientType::Individual)
                            ->inline()
                            ->live()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->label(fn (Forms\Get $get) => $get('type') === ClientType::Company->value ? __('اسم الشركة') : __('الاسم بالكامل'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('رقم التليفون'))
                            ->tel(),
                        Forms\Components\TextInput::make('email')
                            ->label(__('البريد الإلكتروني'))
                            ->email(),
                        Forms\Components\TextInput::make('national_id')
                            ->label(__('الرقم القومي'))
                            ->visible(fn (Forms\Get $get) => $get('type') === ClientType::Individual->value),
                        Forms\Components\TextInput::make('commercial_register')
                            ->label(__('السجل التجاري'))
                            ->visible(fn (Forms\Get $get) => $get('type') === ClientType::Company->value),
                        Forms\Components\Textarea::make('address')
                            ->label(__('العنوان'))
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('المتابعة والمسؤول'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('pipeline_stage')
                            ->label(__('مرحلة المتابعة (Pipeline)'))
                            ->options(PipelineStage::class)
                            ->default(PipelineStage::NewLead)
                            ->required(),
                        Forms\Components\Select::make('assigned_to')
                            ->label(__('الموظف المسؤول'))
                            ->relationship('assignedUser', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

                Forms\Components\Section::make(__('المستندات والملاحظات'))
                    ->schema([
                        Forms\Components\FileUpload::make('documents')
                            ->label(__('المستندات (PDF / صور)'))
                            ->multiple()
                            ->directory('clients')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->reorderable()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('ملاحظات عامة'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->label(__('استيراد من Excel'))
                    ->importer(ClientImporter::class),
                Tables\Actions\ExportAction::make()
                    ->label(__('تصدير'))
                    ->exporter(ClientExporter::class),
                Tables\Actions\Action::make('exportPdf')
                    ->label(__('تصدير PDF'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function ($livewire) {
                        $clients = $livewire->getFilteredTableQuery()
                            ->withCount('policies')
                            ->with('assignedUser')
                            ->get();

                        return response()->streamDownload(
                            fn () => print (\Pdf::loadView('pdf.clients-report', ['clients' => $clients])->output()),
                            __('تقرير-العملاء-').now()->format('Y-m-d').'.pdf'
                        );
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('الاسم'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Client $record) => $record->phone),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('النوع'))
                    ->badge(),
                Tables\Columns\TextColumn::make('pipeline_stage')
                    ->label(__('المرحلة'))
                    ->badge(),
                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label(__('الموظف المسؤول'))
                    ->default('—'),
                Tables\Columns\TextColumn::make('policies_count')
                    ->label(__('عدد الوثائق'))
                    ->counts('policies')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('البريد الإلكتروني'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('تاريخ الإضافة'))
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('النوع'))
                    ->options(ClientType::class),
                Tables\Filters\SelectFilter::make('pipeline_stage')
                    ->label(__('المرحلة'))
                    ->options(PipelineStage::class),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label(__('الموظف المسؤول'))
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

    public static function getNavigationGroup(): ?string
    {
        return __('العملاء والمبيعات');
    }

    public static function getNavigationLabel(): string
    {
        return __('العملاء');
    }

    public static function getModelLabel(): string
    {
        return __('عميل');
    }

    public static function getPluralModelLabel(): string
    {
        return __('العملاء');
    }
}
