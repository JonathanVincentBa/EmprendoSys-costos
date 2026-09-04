<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run($companyId = null): void
    {
        if (!$companyId) return;

        // 1. Cliente Mayorista por defecto (el que tenías originalmente)
        Customer::create([
            'company_id'     => $companyId,
            'name'           => 'Cliente Mayorista E' . $companyId,
            'identification' => '17' . fake()->unique()->numerify('########') . '001',
            'email'          => 'compras@cliente' . $companyId . '.com',
            'type'           => 'mayorista'
        ]);

        // 2. Generar 49 clientes adicionales (50 en total por empresa)
        for ($i = 2; $i <= 50; $i++) {
            Customer::create([
                'company_id'     => $companyId,
                'name'           => fake()->name(),
                'identification' => '17' . fake()->unique()->numerify('########') . '001',
                'email'          => fake()->unique()->safeEmail(),
                'type'           => fake()->randomElement(['minorista', 'mayorista']),
            ]);
        }
    }
}