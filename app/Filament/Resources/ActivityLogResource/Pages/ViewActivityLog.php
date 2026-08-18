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
                Section::make('تفاصيل العملية')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('الوقت')
                            ->dateTime('Y-m-d H:i:s'),
                        TextEntry::make('causer.name')
                            ->label('بواسطة')
                            ->default('النظام'),
                        TextEntry::make('event')
                            ->label('العملية')
                            ->formatStateUsing(fn (?string $state) => match ($state) {
                                'created' => 'إضافة',
                                'updated' => 'تعديل',
                                'deleted' => 'حذف',
                                default => $state ?? '—',
                            })
                            ->badge(),
                        TextEntry::make('subject_type')
                            ->label('النوع')
                            ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—'),
                        TextEntry::make('description')
                            ->label('الوصف')
                            ->columnSpanFull(),
                    ]),
                Section::make('القيم قبل وبعد التعديل')
                    ->schema([
                        KeyValueEntry::make('properties.old')
                            ->label('قبل')
                            ->visible(fn ($record) => filled($record->properties['old'] ?? null)),
                        KeyValueEntry::make('properties.attributes')
                            ->label('بعد')
                            ->visible(fn ($record) => filled($record->properties['attributes'] ?? null)),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => filled($record->properties)),
            ]);
    }
}
