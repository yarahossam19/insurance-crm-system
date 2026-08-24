<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewActivityLog extends ViewRecord
{
    protected static string $resource = ActivityLogResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('تفاصيل العملية'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('الوقت'))
                            ->dateTime('Y-m-d H:i:s'),
                        TextEntry::make('causer.name')
                            ->label(__('بواسطة'))
                            ->default(__('النظام')),
                        TextEntry::make('event')
                            ->label(__('العملية'))
                            ->formatStateUsing(fn (?string $state) => match ($state) {
                                'created' => __('إضافة'),
                                'updated' => __('تعديل'),
                                'deleted' => __('حذف'),
                                default => $state ?? '—',
                            })
                            ->badge(),
                        TextEntry::make('subject_type')
                            ->label(__('النوع'))
                            ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—'),
                        TextEntry::make('description')
                            ->label(__('الوصف'))
                            ->columnSpanFull(),
                    ]),
                Section::make(__('القيم قبل وبعد التعديل'))
                    ->schema([
                        KeyValueEntry::make('properties.old')
                            ->label(__('قبل'))
                            ->visible(fn ($record) => filled($record->properties['old'] ?? null)),
                        KeyValueEntry::make('properties.attributes')
                            ->label(__('بعد'))
                            ->visible(fn ($record) => filled($record->properties['attributes'] ?? null)),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => filled($record->properties)),
            ]);
    }
}
