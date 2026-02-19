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

        // 1. Crear ADMIN de la empresa
        $admin = User::updateOrCreate(
            ['email' => "admin.empresa{$companyId}@test.com"],
            [
                'name' => "Admin Empresa {$companyId}",
                'password' => Hash::make('password'),
                'company_id' => $companyId,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // 2. Crear 3 VENDEDORES por cada empresa
        for ($i = 1; $i <= 3; $i++) {
            $vendedor = User::updateOrCreate(
                ['email' => "vendedor{$i}.empresa{$companyId}@test.com"],
                [
                    'name' => "Vendedor {$i} - Empresa {$companyId}",
                    'password' => Hash::make('password'),
                    'company_id' => $companyId,
                    'email_verified_at' => now(),
                ]
            );
            
            // Importante: Verifica que el rol se llame exactamente 'vendedor'
            $vendedor->assignRole('vendedor');
        }
    }
}