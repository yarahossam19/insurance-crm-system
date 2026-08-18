<?php

namespace Tests\Unit;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PolicyRenewalTierTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    #[DataProvider('tierProvider')]
    public function it_buckets_days_to_expiry_into_the_correct_renewal_tier(int $daysFromNow, ?string $expectedTier): void
    {
        $policy = Policy::factory()->make([
            'end_date' => now()->addDays($daysFromNow),
            'status' => PolicyStatus::Active,
        ]);

        $this->assertSame($expectedTier, $policy->renewal_tier);
    }

    public static function tierProvider(): array
    {
        return [
            'exactly at the 90-day boundary' => [90, '90'],
            'just past the 90-day boundary falls out of every tier' => [91, null],
            'inside the 60-day tier' => [45, '60'],
            'exactly at the 30-day boundary' => [30, '30'],
            'exactly at the 15-day boundary' => [15, '15'],
            'exactly at the 7-day boundary' => [7, '7'],
            'one day left' => [1, '7'],
            'expires today' => [0, '7'],
            'already expired yesterday' => [-1, 'expired'],
            'expired a month ago' => [-30, 'expired'],
        ];
    }

    #[Test]
    public function a_cancelled_policy_never_gets_a_renewal_tier_even_if_expiring_soon(): void
    {
        $policy = Policy::factory()->make([
            'end_date' => now()->addDays(3),
            'status' => PolicyStatus::Cancelled,
        ]);

        $this->assertNull($policy->renewal_tier);
    }
}
