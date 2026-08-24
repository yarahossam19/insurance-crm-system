<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<title>{{ __('تقرير وثائق التأمين') }}</title>
<style>
    body { font-family: 'DejaVu Sans', sans-serif; direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; color: #10202B; font-size: 12px; }
    .header { border-bottom: 2px solid #3C9284; padding-bottom: 10px; margin-bottom: 18px; }
    .header .brand { font-size: 18px; font-weight: bold; color: #3C9284; }
    .header .meta { font-size: 10px; color: #7C8A92; margin-top: 4px; }
    h1 { font-size: 16px; color: #10202B; margin: 0 0 14px 0; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th { background: #E7ECE6; color: #45525C; font-size: 10px; padding: 7px 6px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; border-bottom: 1px solid #DAE0D8; }
    td { padding: 7px 6px; font-size: 10.5px; border-bottom: 1px solid #DAE0D8; }
    tfoot td { font-weight: bold; background: #F1F3EF; }
    .muted { color: #7C8A92; font-size: 10px; }
    .footer { position: fixed; bottom: -20px; left: 0; right: 0; font-size: 9px; color: #7C8A92; text-align: center; }
</style>
</head>
<body>
    <div class="header">
        <div class="brand">عبدالله الشباسي للتأمين</div>
        <div class="meta">{{ __('تاريخ الإصدار') }}: {{ now()->format('Y-m-d H:i') }} — {{ __('عدد الوثائق') }}: {{ $policies->count() }}</div>
    </div>

    <h1>{{ __('تقرير وثائق التأمين') }}</h1>

    <table>
        <thead>
            <tr>
                <th>{{ __('رقم الوثيقة') }}</th>
                <th>{{ __('العميل') }}</th>
                <th>{{ __('شركة التأمين') }}</th>
                <th>{{ __('النوع') }}</th>
                <th>{{ __('تاريخ الانتهاء') }}</th>
                <th>{{ __('الحالة') }}</th>
                <th>{{ __('القسط') }}</th>
                <th>{{ __('صافي العمولة') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($policies as $policy)
                <tr>
                    <td>{{ $policy->policy_number }}</td>
                    <td>{{ $policy->client?->name }}</td>
                    <td>{{ $policy->insuranceCompany?->name }}</td>
                    <td>{{ $policy->type?->getLabel() }}</td>
                    <td>{{ $policy->end_date->format('Y-m-d') }}</td>
                    <td>{{ $policy->status?->getLabel() }}</td>
                    <td>{{ number_format((float) $policy->premium_amount) }}{{ __(' ج.م') }}</td>
                    <td>{{ number_format((float) $policy->net_office_commission) }}{{ __(' ج.م') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">{{ __('الإجمالي') }}</td>
                <td>{{ number_format((float) $policies->sum('premium_amount')) }}{{ __(' ج.م') }}</td>
                <td>{{ number_format((float) $policies->sum('net_office_commission')) }}{{ __(' ج.م') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">{{ __('تقرير آلي من نظام إدارة الوساطة التأمينية') }} — عبدالله الشباسي للتأمين</div>
</body>
</html>
