<?php

namespace App\Filament\Resources;

use App\Enums\PolicyStatus;
use App\Enums\PolicyType;
use App\Filament\Exports\PolicyExporter;
use App\Filament\Resources\PolicyResource\Pages;
use App\Filament\Resources\PolicyResource\RelationManagers;
use App\Models\Policy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PolicyResource extends Resource
{
    protected static ?string $model = Policy::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'الوثائق والتجديدات';

    protected static ?string $navigationLabel = 'وثائق التأمين';

    protected static ?string $modelLabel = 'وثيقة تأمين';

    protected static ?string $pluralModelLabel = 'وثائق التأمين';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'policy_number';

    /** Recalculate commission_amount and net_office_commission from the current form state. */
    protected static function recalculateCommissions(Get $get, Set $set): void
    {
        $premium = (float) ($get('premium_amount') ?? 0);
        $rate = (float) ($get('commission_rate') ?? 0);
        $employeeCommission = (float) ($get('employee_commission_amount') ?? 0);

        $commissionAmount = round($premium * $rate / 100, 2);
        $netOffice = round($commissionAmount - $employeeCommission, 2);

        $set('commission_amount', $commissionAmount);
        $set('net_office_commission', $netOffice);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('بيانات الوثيقة'))
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label(__('العميل'))
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
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
                            ->after('start_date')
                            ->default(now()->addYear()),
                    ]),

                Forms\Components\Section::make(__('العمولة والتحصيل'))
                    ->description(__('يُحسب صافي عمولة المكتب تلقائيًا: القسط × نسبة العمولة − عمولة الموظف'))
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('premium_amount')
                            ->label(__('القسط (ج.م)'))
                            ->numeric()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateCommissions($get, $set)),
                        Forms\Components\TextInput::make('commission_rate')
                            ->label(__('نسبة العمولة (%)'))
                            ->numeric()
                            ->suffix('%')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateCommissions($get, $set)),
                        Forms\Components\TextInput::make('employee_commission_amount')
                            ->label(__('عمولة الموظف (ج.م)'))
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateCommissions($get, $set)),
                        Forms\Components\TextInput::make('commission_amount')
                            ->label(__('قيمة العمولة (ج.م)'))
                            ->numeric()
                            ->readOnly()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('net_office_commission')
                            ->label(__('صافي عمولة المكتب (ج.م)'))
                            ->numeric()
                            ->readOnly()
                            ->dehydrated(),
                    ]),

                Forms\Components\Section::make(__('الحالة والمتابعة'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('حالة الوثيقة'))
                            ->options(PolicyStatus::class)
                            ->default(PolicyStatus::Active)
                            ->required(),
                        Forms\Components\Select::make('responsible_user_id')
                            ->label(__('الموظف المسؤول'))
                            ->relationship('responsibleUser', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

                Forms\Components\Section::make(__('المستندات والملاحظات'))
                    ->schema([
                        Forms\Components\FileUpload::make('documents')
                            ->label(__('مستندات الوثيقة'))
                            ->multiple()
                            ->directory('policies')
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
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label(__('تصدير Excel'))
                    ->exporter(PolicyExporter::class),
                Tables\Actions\Action::make('exportPdf')
                    ->label(__('تصدير PDF'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function ($livewire) {
                        $policies = $livewire->getFilteredTableQuery()
                            ->with(['client', 'insuranceCompany'])
                            ->get();

                        return response()->streamDownload(
                            fn () => print (\Pdf::loadView('pdf.policies-report', ['policies' => $policies])->output()),
                            __('تقرير-وثائق-التأمين-').now()->format('Y-m-d').'.pdf'
                        );
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('policy_number')
                    ->label(__('رقم الوثيقة'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('العميل'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('insuranceCompany.name')
                    ->label(__('شركة التأمين'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('النوع'))
                    ->badge(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('تاريخ الانتهاء'))
                    ->date('Y-m-d')
                    ->sortable()
                    ->description(fn (Policy $record) => $record->status !== PolicyStatus::Cancelled
                        ? ($record->days_to_expiry >= 0
                            ? __('متبقي :days يوم', ['days' => $record->days_to_expiry])
                            : __('منتهية منذ ').abs($record->days_to_expiry).__(' يوم'))
                        : null)
                    ->color(fn (Policy $record) => match (true) {
                        $record->status === PolicyStatus::Cancelled => 'gray',
                        $record->days_to_expiry < 0 => 'danger',
                        $record->days_to_expiry <= 15 => 'danger',
                        $record->days_to_expiry <= 30 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->badge(),
                Tables\Columns\TextColumn::make('premium_amount')
                    ->label(__('القسط'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix(__(' ج.م'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('net_office_commission')
                    ->label(__('صافي العمولة'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix(__(' ج.م'))
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('responsibleUser.name')
                    ->label(__('الموظف المسؤول'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('الحالة'))
                    ->options(PolicyStatus::class),
                Tables\Filters\SelectFilter::make('insurance_company_id')
                    ->label(__('شركة التأمين'))
                    ->relationship('insuranceCompany', 'name'),
                Tables\Filters\Filter::make('expiring_soon')
                    ->label(__('تجديدات خلال 30 يوم'))
                    ->query(fn (Builder $query): Builder => $query->expiringWithin(0, 30)),
                Tables\Filters\Filter::make('expired')
                    ->label(__('منتهية'))
                    ->query(fn (Builder $query): Builder => $query->expired()),
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
            ->defaultSort('end_date');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CollectionsRelationManager::class,
            RelationManagers\FollowUpsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPolicies::route('/'),
            'create' => Pages\CreatePolicy::route('/create'),
            'view' => Pages\ViewPolicy::route('/{record}'),
            'edit' => Pages\EditPolicy::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('الوثائق والتجديدات');
    }

    public static function getNavigationLabel(): string
    {
        return __('وثائق التأمين');
    }

    public static function getModelLabel(): string
    {
        return __('وثيقة تأمين');
    }

    public static function getPluralModelLabel(): string
    {
        return __('وثائق التأمين');
    }
}
