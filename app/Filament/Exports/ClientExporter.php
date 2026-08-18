<?php

namespace App\Filament\Exports;

use App\Models\Client;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ClientExporter extends Exporter
{
    protected static ?string $model = Client::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('الاسم'),
            ExportColumn::make('type')->label('النوع'),
            ExportColumn::make('phone')->label('التليفون'),
            ExportColumn::make('email')->label('البريد الإلكتروني'),
            ExportColumn::make('national_id')->label('الرقم القومي'),
            ExportColumn::make('commercial_register')->label('السجل التجاري'),
            ExportColumn::make('pipeline_stage')->label('مرحلة المتابعة'),
            ExportColumn::make('assignedUser.name')->label('الموظف المسؤول'),
            ExportColumn::make('address')->label('العنوان'),
            ExportColumn::make('created_at')->label('تاريخ الإضافة'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'تم تصدير '.number_format($export->successful_rows).' سجل بنجاح.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' فشل تصدير '.number_format($failedRowsCount).' سجل.';
        }

        return $body;
    }
}
