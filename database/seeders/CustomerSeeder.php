<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run($companyId = null): void
    {
        if (!$companyId) return;

        Customer::create([
            'company_id' => $companyId,
            'name' => 'Cliente Mayorista E' . $companyId,
            'identification' => '17' . fake()->numerify('########') . '001',
            'email' => 'compras@cliente' . $companyId . '.com',
            'type' => 'mayorista'
        ]);
    }
}