<?php

namespace Database\Factories;

use App\Models\Collection;
use App\Models\Policy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Collection>
 */
class CollectionFactory extends Factory
{
    protected $model = Collection::class;

    public function definition(): array
    {
        return [
            'policy_id' => Policy::factory(),
            'amount' => $this->faker->numberBetween(500, 5000),
            'collected_at' => now(),
        ];
    }
}
