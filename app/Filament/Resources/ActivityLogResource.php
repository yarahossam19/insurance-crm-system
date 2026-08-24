<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'الإعدادات';

    protected static ?string $navigationLabel = 'سجل النشاط';

    protected static ?string $modelLabel = 'نشاط';

    protected static ?string $pluralModelLabel = 'سجل النشاط';

    protected static ?int $navigationSort = 99;

    protected static ?string $recordTitleAttribute = 'description';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('الوقت'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label(__('بواسطة'))
                    ->default(__('النظام')),
                Tables\Columns\TextColumn::make('event')
                    ->label(__('العملية'))
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'created' => __('إضافة'),
                        'updated' => __('تعديل'),
                        'deleted' => __('حذف'),
                        default => $state ?? '—',
                    }),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label(__('النوع'))
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—'),
                Tables\Columns\TextColumn::make('subject_id')
                    ->label(__('رقم السجل')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('الوصف'))
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label(__('العملية'))
                    ->options([
                        'created' => __('إضافة'),
                        'updated' => __('تعديل'),
                        'deleted' => __('حذف'),
                    ]),
                Tables\Filters\SelectFilter::make('subject_type')
                    ->label(__('النوع'))
                    ->options([
                        \App\Models\Client::class => __('عميل'),
                        \App\Models\Policy::class => __('وثيقة تأمين'),
                        \App\Models\Quotation::class => __('عرض سعر'),
                        \App\Models\Claim::class => __('مطالبة'),
                        \App\Models\InsuranceCompany::class => __('شركة تأمين'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('الإعدادات');
    }

    public static function getNavigationLabel(): string
    {
        return __('سجل النشاط');
    }

    public static function getModelLabel(): string
    {
        return __('نشاط');
    }

    public static function getPluralModelLabel(): string
    {
        return __('سجل النشاط');
    }
}
