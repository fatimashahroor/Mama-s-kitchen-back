<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'payment_status' => $this->faker->randomElement(['pending', 'completed']),
            'payment_method' => $this->faker->randomElement(['cash', 'card']),
        ];
    }

    public function calculateTotalAmount()
    {
        return $this->afterMaking(function (Payment $payment) {
            if ($order = Order::find($payment->order_id)) {
                $payment->total_amount = $order->order_price + $order->user->delivery_charge;
            }
        });
    }
}
