<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Create Super Admin role if not exists
        $role = Role::firstOrCreate(['name' => 'Super Admin']);

        // Create Super Admin user
        $user = User::create([
            'name' => 'Demo User',
            'email' => 'test@crmsystem.com',
            'password' => Hash::make('12345678'),
            'core_password' => '12345678',
        ]);

        // Assign role to user
        $user->assignRole($role);
    }
}


