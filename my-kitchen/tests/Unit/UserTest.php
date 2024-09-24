<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Hash;


class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itCanCreateAUser()
    {
        $user = User::create([
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'phone' => '+9611234567',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com'
        ]);
    }
}
