<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Additional_ing>
 */
class AdditionalIngFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ingredients= ['cheese', 'bbq sauce', 'mushrooms', 'pepper'];
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->randomElement($ingredients),
            'cost'->$this->faker->numberBetween(0, 20),
        ];
    }
}
