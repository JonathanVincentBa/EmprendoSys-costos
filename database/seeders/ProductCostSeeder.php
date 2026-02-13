<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RawMaterial;
use App\Models\LaborCost;

class ProductCostSeeder extends Seeder
{
    public function run($companyId = null): void
    {
        $id = $companyId ?? 1;

        // Modificamos el CÓDIGO para que sea único por empresa: RM-01-E1, RM-01-E2...
        $materials = [
            ['RM-01-E' . $id, 'Harina Especial E' . $id, 1.20, 'kg'],
            ['RM-02-E' . $id, 'Azúcar Blanca E' . $id, 0.85, 'kg'],
            ['RM-03-E' . $id, 'Agua Filtrada E' . $id, 0.05, 'l'],
        ];

        foreach ($materials as $m) {
            // Usamos updateOrCreate para que no falle si el registro ya existe
            RawMaterial::updateOrCreate(
                ['company_id' => $id, 'code' => $m[0]], // Criterio de búsqueda
                [
                    'name' => $m[1],
                    'unit_cost' => $m[2],
                    'unit' => $m[3],
                ]
            );
        }

        // Lo mismo para Mano de Obra
        LaborCost::updateOrCreate(
            ['company_id' => $id, 'role' => 'Operario de Planta E' . $id],
            [
                'monthly_salary' => 460.00,
                'iess_rate' => 12.15,
                'decimo_tercero_rate' => 8.33,
                'decimo_cuarto_rate' => 8.33,
                'vacation_rate' => 4.17,
                'fondo_reserva_rate' => 8.33,
                'severance_rate' => 0,
            ]
        );
    }
}