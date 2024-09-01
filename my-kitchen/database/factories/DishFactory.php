<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dish>
 */
class DishFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $foodNames = [
            'Spaghetti Carbonara',
            'Margherita Pizza',
            'Caesar Salad',
            'Beef Stroganoff',
            'Chicken Tikka Masala',
            'Vegetable Stir Fry',
            'Quinoa Salad',
            'Pulled Pork Sandwich'
        ];
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement($foodNames),
            'image_path' => fake()->imageUrl(),
            'price' => fake()->numberBetween(1, 50),
            'steps' => fake()->paragraphs(3, true),
            'available_on' => fake()->randomElement('Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday, Daily')
        ];
    }
}
