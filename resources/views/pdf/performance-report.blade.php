<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<title>{{ __('ملخص الأداء') }}</title>
<style>
    body { font-family: 'DejaVu Sans', sans-serif; direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; color: #10202B; font-size: 12px; }
    .header { border-bottom: 2px solid #3C9284; padding-bottom: 10px; margin-bottom: 18px; }
    .header .brand { font-size: 18px; font-weight: bold; color: #3C9284; }
    .header .meta { font-size: 10px; color: #7C8A92; margin-top: 4px; }
    h1 { font-size: 16px; color: #10202B; margin: 0 0 14px 0; }
    h2 { font-size: 13px; color: #3C9284; margin: 22px 0 8px 0; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th { background: #E7ECE6; color: #45525C; font-size: 10.5px; padding: 7px 8px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; border-bottom: 1px solid #DAE0D8; }
    td { padding: 7px 8px; font-size: 11px; border-bottom: 1px solid #DAE0D8; }
    .footer { position: fixed; bottom: -20px; left: 0; right: 0; font-size: 9px; color: #7C8A92; text-align: center; }
</style>
</head>
<body>
    <div class="header">
        <div class="brand">IGIB</div>
        <div class="meta">{{ __('تاريخ الإصدار') }}: {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    <h1>{{ __('ملخص الأداء') }}</h1>

    <h2>{{ __('أداء شركات التأمين') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('شركة التأمين') }}</th>
                <th>{{ __('عدد الوثائق') }}</th>
                <th>{{ __('إجمالي الأقساط') }}</th>
                <th>{{ __('إجمالي العمولات') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
                <tr>
                    <td>{{ $company->name }}</td>
                    <td>{{ $company->policies_count }}</td>
                    <td>{{ number_format((float) $company->premiums_sum) }}{{ __(' ج.م') }}</td>
                    <td>{{ number_format((float) $company->commissions_sum) }}{{ __(' ج.م') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>{{ __('أداء الموظفين') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('الموظف') }}</th>
                <th>{{ __('عدد العملاء') }}</th>
                <th>{{ __('عدد الوثائق') }}</th>
                <th>{{ __('إجمالي العمولات المحقّقة') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $employee)
                <tr>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->assigned_clients_count }}</td>
                    <td>{{ $employee->responsible_policies_count }}</td>
                    <td>{{ number_format((float) $employee->commissions_sum) }}{{ __(' ج.م') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">{{ __('تقرير آلي من نظام إدارة الوساطة التأمينية') }} — IGIB للتأمين</div>
</body>
</html>
