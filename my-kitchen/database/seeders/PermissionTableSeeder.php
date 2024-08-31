<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;


class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $permissions = [
           'role-list', 'role-create', 'role-edit', 'role-delete',
           'dish-list', 'dish-create', 'dish-edit', 'dish-delete', 
           'user-list', 'user-create', 'user-edit','user-delete', 
           'review-list', 'review-create', 'review-edit', 'review-delete',
           'order-list', 'order-create', 'order-edit', 'order-delete',
           'payment-list','payment-create', 'payment-delete',
           'additional_ing-list', 'additional_ing-edit', 'additional_ing-delete',
           'ingredient-list', 'ingredient-create', 'ingredient-edit', 'ingredient-delete',
           'location-list','location-create', 'location-edit', 'location-delete',
        ];


        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission, 'guard_name'=> 'api']);
        }
    }
}