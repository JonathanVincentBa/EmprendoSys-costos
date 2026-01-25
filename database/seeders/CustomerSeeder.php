<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Company;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('ruc', '9999999999001')->first();

        $customers = [
            [
                'name' => 'Consumidor Final',
                'identification' => '9999999999999',
                'email' => 'consumidor@final.com',
                'type' => 'minorista',
            ],
            [
                'name' => 'Juan Pérez',
                'identification' => '1712345678',
                'email' => 'juan.perez@mail.com',
                'type' => 'minorista',
            ],
            [
                'name' => 'Corporación Favorita',
                'identification' => '1790016919001',
                'email' => 'compras@favorita.com',
                'type' => 'mayorista',
            ],
        ];

        foreach ($customers as $data) {
            Customer::create(array_merge($data, ['company_id' => $company->id]));
        }
    }
}