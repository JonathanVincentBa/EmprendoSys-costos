<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run($companyId = null): void
    {
        if (!$companyId) return;

        $user = User::updateOrCreate(
            ['email' => "admin.empresa{$companyId}@test.com"],
            [
                'name' => "Admin Empresa {$companyId}",
                'password' => Hash::make('password'),
                'company_id' => $companyId,
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('admin');
    }
}