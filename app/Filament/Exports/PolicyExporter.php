<?php

namespace App\Filament\Exports;

use App\Models\Policy;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PolicyExporter extends Exporter
{
    protected static ?string $model = Policy::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('policy_number')->label('رقم الوثيقة'),
            ExportColumn::make('client.name')->label('العميل'),
            ExportColumn::make('insuranceCompany.name')->label('شركة التأمين'),
            ExportColumn::make('type')->label('النوع'),
            ExportColumn::make('start_date')->label('تاريخ البداية'),
            ExportColumn::make('end_date')->label('تاريخ النهاية'),
            ExportColumn::make('premium_amount')->label('القسط'),
            ExportColumn::make('commission_rate')->label('نسبة العمولة'),
            ExportColumn::make('commission_amount')->label('قيمة العمولة'),
            ExportColumn::make('employee_commission_amount')->label('عمولة الموظف'),
            ExportColumn::make('net_office_commission')->label('صافي عمولة المكتب'),
            ExportColumn::make('status')->label('الحالة'),
            ExportColumn::make('responsibleUser.name')->label('الموظف المسؤول'),
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
