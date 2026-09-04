<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            RolesAndAdminSeeder::class,
        ]);

        for ($i = 1; $i <= 10; $i++) {
            $company = Company::create([
                'name' => "Empresa $i",
                'razon_social' => "Empresa Demo $i S.A.S.",
                'ruc' => '1790000000' . str_pad($i, 3, '0', STR_PAD_LEFT), // Garantiza 13 dígitos exactos
                'address' => "Calle Industrial $i",
                'establishment_address' => "Calle Industrial $i",
                'email' => "empresa$i@test.com",
                'status' => 'active',
                'estab' => '001',
                'pto_emi' => '001',
                'obligado_contabilidad' => 'NO',
                'sri_environment' => '1', // 1: Pruebas
            ]);

            $this->callWith(UserSeeder::class, ['companyId' => $company->id]);
            $this->callWith(CustomerSeeder::class, ['companyId' => $company->id]);

            // ORDEN IMPORTANTE:
            $this->callWith(CompanyCostsSeeder::class, ['companyId' => $company->id]);
            $this->callWith(ProductCostSeeder::class, ['companyId' => $company->id]);
            $this->callWith(ProductSeeder::class, ['companyId' => $company->id]);
            $this->callWith(SaleSeeder::class, ['companyId' => $company->id]);
        }
    }
}