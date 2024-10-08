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
    public function run(): void
    {
       $permissions = [
           'role-list', 'role-create', 'role-edit', 'role-delete',
           'dish-list', 'dish-create', 'dish-edit', 'dish-delete', 
           'user-list', 'user-create', 'user-edit','user-delete', 
           'review-list', 'review-create', 'review-edit', 'review-delete',
           'rating-list', 'rating-create',
           'cooks-list',
           'order-list', 'order-create', 'order-edit',
           'payment-list','payment-create', 
           'additional_ing-list', 'additional_ing-create', 'additional_ing-edit', 'additional_ing-delete',
           'location-list','location-create', 'location-edit', 'location-delete',
        ];


        foreach ($permissions as $permission) {
             Permission::create(attributes: ['name' => $permission, 'guard_name'=> 'api']);
        }
    }
}