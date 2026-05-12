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
                'ruc' => "179000000000$i",
                'address' => "Calle Industrial $i",
                'email' => "empresa$i@test.com",
            ]);

            $this->callWith(UserSeeder::class, ['companyId' => $company->id]);
            $this->callWith(CustomerSeeder::class, ['companyId' => $company->id]);

            // ORDEN IMPORTANTE:
            $this->callWith(CompanyCostsSeeder::class, ['companyId' => $company->id]); // Crea los costos
            $this->callWith(ProductSeeder::class, ['companyId' => $company->id]);      // Vincula los costos al producto
            $this->callWith(ProductCostSeeder::class, ['companyId' => $company->id]); // Asegura que los costos estén actualizados para cada empresa
            $this->callWith(SaleSeeder::class, ['companyId' => $company->id]);
        }
    }
}
