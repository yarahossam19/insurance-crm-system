<?php

namespace App\Filament\Resources\InsuranceCompanyResource\Pages;

use App\Filament\Resources\InsuranceCompanyResource;
use App\Models\InsuranceCompany;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewInsuranceCompany extends ViewRecord
{
    protected static string $resource = InsuranceCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('ملخص الأداء'))
                    ->columns(3)
                    ->schema([
                        TextEntry::make('policies_count')
                            ->label(__('عدد الوثائق'))
                            ->state(fn (InsuranceCompany $record) => $record->policies()->count()),
                        TextEntry::make('premiums_total')
                            ->label(__('إجمالي الأقساط'))
                            ->state(fn (InsuranceCompany $record) => number_format((float) $record->policies()->sum('premium_amount')).__(' ج.م')),
                        TextEntry::make('commissions_total')
                            ->label(__('إجمالي العمولات'))
                            ->state(fn (InsuranceCompany $record) => number_format((float) $record->policies()->sum('net_office_commission')).__(' ج.م'))
                            ->weight('bold')
                            ->color('primary'),
                    ]),
                Section::make(__('بيانات الشركة'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')->label(__('اسم الشركة')),
                        TextEntry::make('contact_person')->label(__('مسؤول التواصل'))->placeholder('—'),
                        TextEntry::make('phone')->label(__('التليفون'))->placeholder('—'),
                        TextEntry::make('email')->label(__('البريد الإلكتروني'))->placeholder('—'),
                        TextEntry::make('is_active')->label(__('نشطة'))->badge()
                            ->formatStateUsing(fn (bool $state) => $state ? __('نشطة') : __('غير نشطة'))
                            ->color(fn (bool $state) => $state ? 'success' : 'gray'),
                        TextEntry::make('address')->label(__('العنوان'))->placeholder('—')->columnSpanFull(),
                        TextEntry::make('notes')->label(__('ملاحظات'))->placeholder('—')->columnSpanFull(),
                    ]),
            ]);
    }
}
