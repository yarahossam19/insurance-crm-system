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
            ExportColumn::make('policy_number')->label(__('رقم الوثيقة')),
            ExportColumn::make('client.name')->label(__('العميل')),
            ExportColumn::make('insuranceCompany.name')->label(__('شركة التأمين')),
            ExportColumn::make('type')->label(__('النوع')),
            ExportColumn::make('start_date')->label(__('تاريخ البداية')),
            ExportColumn::make('end_date')->label(__('تاريخ النهاية')),
            ExportColumn::make('premium_amount')->label(__('القسط')),
            ExportColumn::make('commission_rate')->label(__('نسبة العمولة')),
            ExportColumn::make('commission_amount')->label(__('قيمة العمولة')),
            ExportColumn::make('employee_commission_amount')->label(__('عمولة الموظف')),
            ExportColumn::make('net_office_commission')->label(__('صافي عمولة المكتب')),
            ExportColumn::make('status')->label(__('الحالة')),
            ExportColumn::make('responsibleUser.name')->label(__('الموظف المسؤول')),
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
