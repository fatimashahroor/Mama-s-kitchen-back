<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Auth\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'city' => $this->faker->city(),
            'region' => $this->faker->citySuffix() . ' ' . $this->faker->word(),
            'building' => $this->faker->buildingNumber(),
            'street' => $this->faker->streetName(),
            'floor' => $this->faker->numberBetween(1, 10),
            'near' => $this->faker->word(),
        ];
    }
}
