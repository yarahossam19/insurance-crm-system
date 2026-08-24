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
            ExportColumn::make('name')->label(__('الاسم')),
            ExportColumn::make('type')->label(__('النوع')),
            ExportColumn::make('phone')->label(__('التليفون')),
            ExportColumn::make('email')->label(__('البريد الإلكتروني')),
            ExportColumn::make('national_id')->label(__('الرقم القومي')),
            ExportColumn::make('commercial_register')->label(__('السجل التجاري')),
            ExportColumn::make('pipeline_stage')->label(__('مرحلة المتابعة')),
            ExportColumn::make('assignedUser.name')->label(__('الموظف المسؤول')),
            ExportColumn::make('address')->label(__('العنوان')),
            ExportColumn::make('created_at')->label(__('تاريخ الإضافة')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('تم تصدير ').number_format($export->successful_rows).__(' سجل بنجاح.');

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= __(' فشل تصدير ').number_format($failedRowsCount).__(' سجل.');
        }

        return $body;
    }
}
