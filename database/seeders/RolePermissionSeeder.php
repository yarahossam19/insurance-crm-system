<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Assign a sensible default permission set to each business role.
     * super_admin is untouched — it bypasses all gates by design.
     */
    public function run(): void
    {
        $sets = [
            'Manager' => [
                'view_any_client', 'view_client', 'create_client', 'update_client',
                'view_any_policy', 'view_policy', 'create_policy', 'update_policy',
                'view_any_quotation', 'view_quotation', 'create_quotation', 'update_quotation',
                'view_any_claim', 'view_claim', 'create_claim', 'update_claim',
                'view_any_insurance::company', 'view_insurance::company', 'create_insurance::company', 'update_insurance::company',
                'view_any_activity::log', 'view_activity::log',
                'page_Pipeline', 'page_Reports',
                'widget_DashboardStats', 'widget_RenewalStatsOverview', 'widget_UpcomingRenewalsTable',
                'widget_RevenueTrendChart', 'widget_PolicyTypeChart', 'widget_PipelineFunnelChart',
                'widget_CompanyPerformanceTable', 'widget_EmployeePerformanceTable',
            ],
            'Sales' => [
                'view_any_client', 'view_client', 'create_client', 'update_client',
                'view_any_policy', 'view_policy', 'create_policy', 'update_policy',
                'view_any_quotation', 'view_quotation', 'create_quotation', 'update_quotation',
                'view_any_claim', 'view_claim', 'create_claim', 'update_claim',
                'view_any_insurance::company', 'view_insurance::company',
                'page_Pipeline',
                'widget_DashboardStats', 'widget_RenewalStatsOverview', 'widget_UpcomingRenewalsTable',
                'widget_PolicyTypeChart', 'widget_PipelineFunnelChart',
            ],
            'Customer Service' => [
                'view_any_client', 'view_client', 'update_client',
                'view_any_policy', 'view_policy',
                'view_any_quotation', 'view_quotation',
                'view_any_claim', 'view_claim', 'create_claim', 'update_claim',
                'page_Pipeline',
                'widget_UpcomingRenewalsTable', 'widget_PipelineFunnelChart',
            ],
            'Accounting' => [
                'view_any_client', 'view_client',
                'view_any_policy', 'view_policy', 'update_policy',
                'view_any_insurance::company', 'view_insurance::company',
                'view_any_activity::log', 'view_activity::log',
                'page_Reports',
                'widget_DashboardStats', 'widget_RevenueTrendChart', 'widget_PolicyTypeChart',
                'widget_CompanyPerformanceTable', 'widget_EmployeePerformanceTable',
            ],
        ];

        foreach ($sets as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                $role->syncPermissions($permissions);
            }
        }
    }
}
