<?php

namespace Tests\Feature;

use App\Models\Policy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RenewalNotificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_notifies_the_responsible_employee_when_a_policy_enters_a_renewal_tier(): void
    {
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::create(['name' => 'Manager', 'guard_name' => 'web']);

        $employee = User::factory()->create();
        $policy = Policy::factory()->create([
            'end_date' => now()->addDays(10), // falls in the 15-day tier
            'responsible_user_id' => $employee->id,
        ]);

        $this->artisan('renewals:notify')->assertExitCode(0);

        $this->assertEquals(1, $employee->fresh()->unreadNotifications()->count());
        $this->assertEquals('15', $policy->fresh()->last_notified_tier);
    }

    #[Test]
    public function it_does_not_send_a_duplicate_notification_for_the_same_tier_twice(): void
    {
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::create(['name' => 'Manager', 'guard_name' => 'web']);

        $employee = User::factory()->create();
        Policy::factory()->create([
            'end_date' => now()->addDays(5),
            'responsible_user_id' => $employee->id,
        ]);

        $this->artisan('renewals:notify');
        $this->artisan('renewals:notify'); // running it again the same day should be a no-op

        $this->assertEquals(1, $employee->fresh()->unreadNotifications()->count());
    }

    #[Test]
    public function it_notifies_again_when_the_policy_moves_into_a_more_urgent_tier(): void
    {
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::create(['name' => 'Manager', 'guard_name' => 'web']);

        $employee = User::factory()->create();
        $policy = Policy::factory()->create([
            'end_date' => now()->addDays(25), // 30-day tier
            'responsible_user_id' => $employee->id,
        ]);

        $this->artisan('renewals:notify');
        $this->assertEquals(1, $employee->fresh()->unreadNotifications()->count());

        // time passes, the same policy is now inside the 15-day tier
        $policy->update(['end_date' => now()->addDays(12)]);
        $this->artisan('renewals:notify');

        $this->assertEquals(2, $employee->fresh()->unreadNotifications()->count());
    }

    #[Test]
    public function cancelled_policies_are_never_notified_about(): void
    {
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::create(['name' => 'Manager', 'guard_name' => 'web']);

        $employee = User::factory()->create();
        Policy::factory()->create([
            'end_date' => now()->addDays(3),
            'status' => 'cancelled',
            'responsible_user_id' => $employee->id,
        ]);

        $this->artisan('renewals:notify');

        $this->assertEquals(0, $employee->fresh()->unreadNotifications()->count());
    }
}
