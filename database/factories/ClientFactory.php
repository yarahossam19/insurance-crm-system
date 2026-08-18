<?php

namespace Database\Factories;

use App\Enums\ClientType;
use App\Enums\PipelineStage;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'type' => ClientType::Individual->value,
            'name' => $this->faker->name(),
            'phone' => $this->faker->numerify('01#########'),
            'email' => $this->faker->safeEmail(),
            'address' => $this->faker->address(),
            'pipeline_stage' => PipelineStage::NewLead->value,
        ];
    }

    public function company(): static
    {
        return $this->state(fn () => [
            'type' => ClientType::Company->value,
            'name' => $this->faker->company(),
            'commercial_register' => $this->faker->numerify('CR-#####'),
        ]);
    }
}
