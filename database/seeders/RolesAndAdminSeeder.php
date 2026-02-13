<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super-admin')->first();

        $user = User::updateOrCreate(
            ['email' => 'jonathanvincent@outlook.com'],
            [
                'name' => 'Jonathan Vincent',
                'password' => Hash::make('password'),
                'company_id' => null, // El Super-Admin no tiene empresa para ver todo
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole($superAdminRole);
    }
}