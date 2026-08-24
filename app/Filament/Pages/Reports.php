<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CollectionsReportTable;
use App\Filament\Widgets\CommissionsReportTable;
use App\Filament\Widgets\CompanyPerformanceTable;
use App\Filament\Widgets\EmployeePerformanceTable;
use App\Models\InsuranceCompany;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Pages\Page;

class Reports extends Page implements HasActions
{
    use InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'الإعدادات';

    protected static ?string $navigationLabel = 'مركز التقارير';

    protected static ?string $title = 'مركز التقارير';

    protected static ?int $navigationSort = 95;

    protected static string $view = 'filament.pages.reports';

    public function getFooterWidgets(): array
    {
        return [
            CompanyPerformanceTable::class,
            EmployeePerformanceTable::class,
            CommissionsReportTable::class,
            CollectionsReportTable::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label(__('تصدير التقرير PDF'))
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    $companies = InsuranceCompany::query()
                        ->withCount('policies')
                        ->withSum('policies as premiums_sum', 'premium_amount')
                        ->withSum('policies as commissions_sum', 'net_office_commission')
                        ->orderByDesc('commissions_sum')
                        ->get();

                    $employees = User::query()
                        ->withCount('assignedClients')
                        ->withCount('responsiblePolicies')
                        ->withSum('responsiblePolicies as commissions_sum', 'net_office_commission')
                        ->orderByDesc('commissions_sum')
                        ->get();

                    return response()->streamDownload(
                        fn () => print (\Pdf::loadView('pdf.performance-report', [
                            'companies' => $companies,
                            'employees' => $employees,
                        ])->output()),
                        __('تقرير-الأداء-').now()->format('Y-m-d').'.pdf'
                    );
                }),
        ];
    }

    /** @return array<int, array{label: string, description: string, icon: string, url: string}> */
    public function getQuickLinks(): array
    {
        return [
            [
                'label' => __('التجديدات القادمة'),
                'description' => __('الوثائق المحتاجة متابعة تجديد خلال 30 يوم'),
                'icon' => 'heroicon-o-arrow-path',
                'url' => route('filament.admin.resources.policies.index', ['tableFilters[expiring_soon][isActive]' => 1]),
            ],
            [
                'label' => __('الوثائق المنتهية'),
                'description' => __('وثائق منتهية بدون تجديد'),
                'icon' => 'heroicon-o-exclamation-triangle',
                'url' => route('filament.admin.resources.policies.index', ['tableFilters[expired][isActive]' => 1]),
            ],
            [
                'label' => __('كل العملاء'),
                'description' => __('قائمة العملاء كاملة (قابلة للتصدير)'),
                'icon' => 'heroicon-o-users',
                'url' => route('filament.admin.resources.clients.index'),
            ],
            [
                'label' => __('كل وثائق التأمين'),
                'description' => __('قائمة الوثائق كاملة (قابلة للتصدير)'),
                'icon' => 'heroicon-o-document-text',
                'url' => route('filament.admin.resources.policies.index'),
            ],
            [
                'label' => __('المطالبات المفتوحة'),
                'description' => __('مطالبات لسه محتاجة متابعة'),
                'icon' => 'heroicon-o-shield-exclamation',
                'url' => route('filament.admin.resources.claims.index'),
            ],
            [
                'label' => __('عروض الأسعار'),
                'description' => __('كل عروض الأسعار المرسلة للعملاء'),
                'icon' => 'heroicon-o-document-currency-dollar',
                'url' => route('filament.admin.resources.quotations.index'),
            ],
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('الإعدادات');
    }

    public static function getNavigationLabel(): string
    {
        return __('مركز التقارير');
    }

    public function getTitle(): string
    {
        return __('مركز التقارير');
    }
}
