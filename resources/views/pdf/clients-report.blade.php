<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<title>{{ __('كل العملاء') }}</title>
<style>
    body { font-family: 'DejaVu Sans', sans-serif; direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; color: #10202B; font-size: 12px; }
    .header { border-bottom: 2px solid #3C9284; padding-bottom: 10px; margin-bottom: 18px; }
    .header .brand { font-size: 18px; font-weight: bold; color: #3C9284; }
    .header .meta { font-size: 10px; color: #7C8A92; margin-top: 4px; }
    h1 { font-size: 16px; color: #10202B; margin: 0 0 14px 0; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th { background: #E7ECE6; color: #45525C; font-size: 10px; padding: 7px 6px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; border-bottom: 1px solid #DAE0D8; }
    td { padding: 7px 6px; font-size: 10.5px; border-bottom: 1px solid #DAE0D8; }
    .footer { position: fixed; bottom: -20px; left: 0; right: 0; font-size: 9px; color: #7C8A92; text-align: center; }
</style>
</head>
<body>
    <div class="header">
        <div class="brand">IGIB</div>
        <div class="meta">{{ __('تاريخ الإصدار') }}: {{ now()->format('Y-m-d H:i') }} — {{ __('عدد العملاء') }}: {{ $clients->count() }}</div>
    </div>

    <h1>{{ __('كل العملاء') }}</h1>

    <table>
        <thead>
            <tr>
                <th>{{ __('الاسم') }}</th>
                <th>{{ __('النوع') }}</th>
                <th>{{ __('التليفون') }}</th>
                <th>{{ __('البريد الإلكتروني') }}</th>
                <th>{{ __('المرحلة') }}</th>
                <th>{{ __('الموظف المسؤول') }}</th>
                <th>{{ __('عدد الوثائق') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->type?->getLabel() }}</td>
                    <td>{{ $client->phone }}</td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->pipeline_stage?->getLabel() }}</td>
                    <td>{{ $client->assignedUser?->name ?? '—' }}</td>
                    <td>{{ $client->policies_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">{{ __('تقرير آلي من نظام إدارة الوساطة التأمينية') }} — IGIB للتأمين</div>
</body>
</html>
