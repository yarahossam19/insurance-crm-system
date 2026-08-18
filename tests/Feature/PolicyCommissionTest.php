<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Policy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PolicyCommissionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function commission_amount_and_net_office_commission_are_always_computed_from_premium_and_rate(): void
    {
        $policy = Policy::factory()->create([
            'premium_amount' => 10000,
            'commission_rate' => 12,
            'employee_commission_amount' => 200,
            // deliberately wrong values — the model must overwrite these on save
            'commission_amount' => 999999,
            'net_office_commission' => 999999,
        ]);

        $this->assertEquals(1200.00, $policy->commission_amount);
        $this->assertEquals(1000.00, $policy->net_office_commission);
    }

    #[Test]
    public function recalculation_also_happens_on_update_not_just_create(): void
    {
        $policy = Policy::factory()->create([
            'premium_amount' => 10000,
            'commission_rate' => 10,
            'employee_commission_amount' => 0,
        ]);

        $this->assertEquals(1000.00, $policy->commission_amount);

        $policy->update(['premium_amount' => 20000]);

        $this->assertEquals(2000.00, $policy->fresh()->commission_amount);
        $this->assertEquals(2000.00, $policy->fresh()->net_office_commission);
    }

    #[Test]
    public function zero_premium_produces_zero_commission_without_error(): void
    {
        $policy = Policy::factory()->create([
            'premium_amount' => 0,
            'commission_rate' => 15,
        ]);

        $this->assertEquals(0.0, $policy->commission_amount);
        $this->assertEquals(0.0, $policy->net_office_commission);
    }

    #[Test]
    public function outstanding_amount_is_the_net_commission_minus_what_has_actually_been_collected(): void
    {
        $policy = Policy::factory()->create([
            'premium_amount' => 10000,
            'commission_rate' => 10,
            'employee_commission_amount' => 0,
        ]);

        $this->assertEquals(1000.0, $policy->outstanding);

        Collection::factory()->create(['policy_id' => $policy->id, 'amount' => 400]);

        $policy->refresh();
        $this->assertEquals(400.0, $policy->collected_total);
        $this->assertEquals(600.0, $policy->outstanding);
    }

    #[Test]
    public function outstanding_never_goes_negative_even_if_overcollected(): void
    {
        $policy = Policy::factory()->create([
            'premium_amount' => 10000,
            'commission_rate' => 10,
            'employee_commission_amount' => 0,
        ]);

        Collection::factory()->create(['policy_id' => $policy->id, 'amount' => 5000]);

        $this->assertEquals(0.0, $policy->refresh()->outstanding);
    }
}
