<?php

namespace Database\Factories;

use App\Models\Additional_ing;
use App\Models\Dish;
use App\Models\Location;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $order = Order::inRandomOrder()->first() ?? Order::factory()->create();
        $dishes = Dish::where('order_id', $order->id)->inRandomOrder()->get();
        $totalDishesPrice = $dishes->sum('price');
        $additional_ings_cost= Additional_ing::where('order_id', $order->id)->sum('cost');
        $order_price= $totalDishesPrice + $additional_ings_cost;
        return [
            'user_id' => User::factory(),
            'location_id' => Location::factory(),
            'status' => $this->faker->randomElement(['requested','pending', 'issued', 'canceled']),
            'order_price' => $order_price,
        ];
    }
}
