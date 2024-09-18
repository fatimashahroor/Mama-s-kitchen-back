<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders_dishes_additional_ings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('dish_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('additional_ings_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->string('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_dishes_additional_ings');
    }
};
