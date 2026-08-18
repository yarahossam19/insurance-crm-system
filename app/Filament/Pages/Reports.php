<?php

namespace App\Filament\Pages;

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
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('تصدير التقرير PDF')
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
                        'تقرير-الأداء-'.now()->format('Y-m-d').'.pdf'
                    );
                }),
        ];
    }

    /** @return array<int, array{label: string, description: string, icon: string, url: string}> */
    public function getQuickLinks(): array
    {
        return [
            [
                'label' => 'التجديدات القادمة',
                'description' => 'الوثائق المحتاجة متابعة تجديد خلال 30 يوم',
                'icon' => 'heroicon-o-arrow-path',
                'url' => route('filament.admin.resources.policies.index', ['tableFilters[expiring_soon][isActive]' => 1]),
            ],
            [
                'label' => 'الوثائق المنتهية',
                'description' => 'وثائق منتهية بدون تجديد',
                'icon' => 'heroicon-o-exclamation-triangle',
                'url' => route('filament.admin.resources.policies.index', ['tableFilters[expired][isActive]' => 1]),
            ],
            [
                'label' => 'كل العملاء',
                'description' => 'قائمة العملاء كاملة (قابلة للتصدير)',
                'icon' => 'heroicon-o-users',
                'url' => route('filament.admin.resources.clients.index'),
            ],
            [
                'label' => 'كل وثائق التأمين',
                'description' => 'قائمة الوثائق كاملة (قابلة للتصدير)',
                'icon' => 'heroicon-o-document-text',
                'url' => route('filament.admin.resources.policies.index'),
            ],
            [
                'label' => 'المطالبات المفتوحة',
                'description' => 'مطالبات لسه محتاجة متابعة',
                'icon' => 'heroicon-o-shield-exclamation',
                'url' => route('filament.admin.resources.claims.index'),
            ],
            [
                'label' => 'عروض الأسعار',
                'description' => 'كل عروض الأسعار المرسلة للعملاء',
                'icon' => 'heroicon-o-document-currency-dollar',
                'url' => route('filament.admin.resources.quotations.index'),
            ],
        ];
    }
}
