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
                Section::make('ملخص الأداء')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('policies_count')
                            ->label('عدد الوثائق')
                            ->state(fn (InsuranceCompany $record) => $record->policies()->count()),
                        TextEntry::make('premiums_total')
                            ->label('إجمالي الأقساط')
                            ->state(fn (InsuranceCompany $record) => number_format((float) $record->policies()->sum('premium_amount')).' ج.م'),
                        TextEntry::make('commissions_total')
                            ->label('إجمالي العمولات')
                            ->state(fn (InsuranceCompany $record) => number_format((float) $record->policies()->sum('net_office_commission')).' ج.م')
                            ->weight('bold')
                            ->color('primary'),
                    ]),
                Section::make('بيانات الشركة')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')->label('اسم الشركة'),
                        TextEntry::make('contact_person')->label('مسؤول التواصل')->placeholder('—'),
                        TextEntry::make('phone')->label('التليفون')->placeholder('—'),
                        TextEntry::make('email')->label('البريد الإلكتروني')->placeholder('—'),
                        TextEntry::make('is_active')->label('نشطة')->badge()
                            ->formatStateUsing(fn (bool $state) => $state ? 'نشطة' : 'غير نشطة')
                            ->color(fn (bool $state) => $state ? 'success' : 'gray'),
                        TextEntry::make('address')->label('العنوان')->placeholder('—')->columnSpanFull(),
                        TextEntry::make('notes')->label('ملاحظات')->placeholder('—')->columnSpanFull(),
                    ]),
            ]);
    }
}
