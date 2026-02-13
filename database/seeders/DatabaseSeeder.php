<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Estructura global (Roles y Permisos)
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // 2. Definición de las 10 empresas
        $companies = [
            ['name' => 'Miski Sabores', 'ruc' => '9999999999001'],
            ['name' => 'Lácteos del Norte', 'ruc' => '1790000000001'],
            ['name' => 'Conservas del Pacífico', 'ruc' => '1790000000002'],
            ['name' => 'Panificadora La Unión', 'ruc' => '1790000000003'],
            ['name' => 'Bebidas Vital S.A.', 'ruc' => '1790000000004'],
            ['name' => 'Snacks del Campo', 'ruc' => '1790000000005'],
            ['name' => 'Cárnicos Gourmet', 'ruc' => '1790000000006'],
            ['name' => 'Especias Amazónicas', 'ruc' => '1790000000007'],
            ['name' => 'Salsas Tradicionales', 'ruc' => '1790000000008'],
            ['name' => 'Frutas Deshidratadas Sol', 'ruc' => '1790000000009'],
        ];

        foreach ($companies as $data) {
            $company = Company::updateOrCreate(['ruc' => $data['ruc']], [
                'name' => $data['name'],
                'address' => 'Av. Industrial ' . fake()->buildingNumber(),
                'email' => 'gerencia@' . str_replace(' ', '', strtolower($data['name'])) . '.com',
                'phone' => '02' . fake()->numerify('#######'),
            ]);

            // Llamamos a los seeders específicos pasando el ID de la empresa actual
            $this->callWith(ProductCostSeeder::class, ['companyId' => $company->id]);
            $this->callWith(CustomerSeeder::class, ['companyId' => $company->id]);
            $this->callWith(UserSeeder::class, ['companyId' => $company->id]);
        }

        // 3. Crear al Super-Admin GLOBAL (Sin empresa)
        $this->call(RolesAndAdminSeeder::class);
    }
}