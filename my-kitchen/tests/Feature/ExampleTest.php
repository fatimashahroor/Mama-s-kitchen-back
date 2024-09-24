<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User; 
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Role; 

class ExampleTest extends TestCase
{
    public function test_user_authenticated_route()
    {
        $roles = Role::all();
    
        $user = User::factory()->create();
        $role = Role::where('name', 'customer')->first();
    
        if (!$role) {
            $this->fail('Role "customer" not found in the database. Available roles: ' . $roles->pluck('name'));
        }
    
        $user->assignRole($role->name);
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/user');
    
        $response->assertStatus(200);
    }
    
}
