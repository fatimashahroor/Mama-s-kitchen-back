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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('password');
            $table->integer('age')->nullable();
            $table->string('phone')->nullable();
            $table->string('located_in')->nullable();
            $table->text('bio')->nullable();
            $table->string('image_path')->nullable();
            $table->double('rating')->nullable();
            $table->bigInteger('rating_count')->nullable();
            $table->enum('status', ['available', 'busy', 'offline'])->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');

    }
};
