<?php

namespace Database\Factories;

use App\Enums\PolicyStatus;
use App\Enums\PolicyType;
use App\Models\Client;
use App\Models\InsuranceCompany;
use App\Models\Policy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Policy>
 */
class PolicyFactory extends Factory
{
    protected $model = Policy::class;

    public function definition(): array
    {
        $premium = $this->faker->numberBetween(2000, 50000);
        $rate = $this->faker->randomElement([5, 10, 12, 15, 20]);
        $commission = round($premium * $rate / 100, 2);

        return [
            'client_id' => Client::factory(),
            'insurance_company_id' => InsuranceCompany::factory(),
            'policy_number' => 'POL-'.$this->faker->unique()->numerify('####-####'),
            'type' => PolicyType::Motor->value,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'premium_amount' => $premium,
            'commission_rate' => $rate,
            'commission_amount' => $commission,
            'employee_commission_amount' => 0,
            'net_office_commission' => $commission,
            'status' => PolicyStatus::Active->value,
        ];
    }

    public function expiringInDays(int $days): static
    {
        return $this->state(fn () => [
            'end_date' => now()->addDays($days),
        ]);
    }
}
