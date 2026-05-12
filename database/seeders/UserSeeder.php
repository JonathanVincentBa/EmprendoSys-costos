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

        // 1 Administrador
        $admin = \App\Models\User::create([
            'name' => "Admin Empresa $companyId",
            'email' => "admin$companyId@test.com",
            'password' => bcrypt('password'),
            'company_id' => $companyId,
        ]);
        $admin->assignRole('admin');

        // 2 Vendedores
        for ($i = 1; $i <= 2; $i++) {
            $vendedor = \App\Models\User::create([
                'name' => "Vendedor $i - E$companyId",
                'email' => "vendedor{$i}_e{$companyId}@test.com",
                'password' => bcrypt('password'),
                'company_id' => $companyId,
            ]);
            $vendedor->assignRole('vendedor');
        }
    }
}
