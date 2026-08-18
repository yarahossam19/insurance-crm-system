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
                Forms\Components\Section::make('بيانات الوثيقة')
                    ->columns(3)
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
                            ->after('start_date')
                            ->default(now()->addYear()),
                    ]),

                Forms\Components\Section::make('العمولة والتحصيل')
                    ->description('يُحسب صافي عمولة المكتب تلقائيًا: القسط × نسبة العمولة − عمولة الموظف')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('premium_amount')
                            ->label('القسط (ج.م)')
                            ->numeric()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateCommissions($get, $set)),
                        Forms\Components\TextInput::make('commission_rate')
                            ->label('نسبة العمولة (%)')
                            ->numeric()
                            ->suffix('%')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateCommissions($get, $set)),
                        Forms\Components\TextInput::make('employee_commission_amount')
                            ->label('عمولة الموظف (ج.م)')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculateCommissions($get, $set)),
                        Forms\Components\TextInput::make('commission_amount')
                            ->label('قيمة العمولة (ج.م)')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('net_office_commission')
                            ->label('صافي عمولة المكتب (ج.م)')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated(),
                    ]),

                Forms\Components\Section::make('الحالة والمتابعة')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('حالة الوثيقة')
                            ->options(PolicyStatus::class)
                            ->default(PolicyStatus::Active)
                            ->required(),
                        Forms\Components\Select::make('responsible_user_id')
                            ->label('الموظف المسؤول')
                            ->relationship('responsibleUser', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

                Forms\Components\Section::make('المستندات والملاحظات')
                    ->schema([
                        Forms\Components\FileUpload::make('documents')
                            ->label('مستندات الوثيقة')
                            ->multiple()
                            ->directory('policies')
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
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('تصدير')
                    ->exporter(PolicyExporter::class),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('policy_number')
                    ->label('رقم الوثيقة')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('insuranceCompany.name')
                    ->label('شركة التأمين')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('تاريخ الانتهاء')
                    ->date('Y-m-d')
                    ->sortable()
                    ->description(fn (Policy $record) => $record->status !== PolicyStatus::Cancelled
                        ? ($record->days_to_expiry >= 0
                            ? "متبقي {$record->days_to_expiry} يوم"
                            : 'منتهية منذ '.abs($record->days_to_expiry).' يوم')
                        : null)
                    ->color(fn (Policy $record) => match (true) {
                        $record->status === PolicyStatus::Cancelled => 'gray',
                        $record->days_to_expiry < 0 => 'danger',
                        $record->days_to_expiry <= 15 => 'danger',
                        $record->days_to_expiry <= 30 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
                Tables\Columns\TextColumn::make('premium_amount')
                    ->label('القسط')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' ج.م')
                    ->sortable(),
                Tables\Columns\TextColumn::make('net_office_commission')
                    ->label('صافي العمولة')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' ج.م')
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('responsibleUser.name')
                    ->label('الموظف المسؤول')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(PolicyStatus::class),
                Tables\Filters\SelectFilter::make('insurance_company_id')
                    ->label('شركة التأمين')
                    ->relationship('insuranceCompany', 'name'),
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('تجديدات خلال 30 يوم')
                    ->query(fn (Builder $query): Builder => $query->expiringWithin(0, 30)),
                Tables\Filters\Filter::make('expired')
                    ->label('منتهية')
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
}
