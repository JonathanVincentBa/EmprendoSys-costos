<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RawMaterial;
use App\Models\LaborCost;
use App\Models\ProductionProcess;
use App\Models\PackagingMaterial;
use App\Models\OverheadConfig;
use App\Models\Supply;

class ProductCostSeeder extends Seeder
{
    public function run($companyId = null): void
    {
        // Si no se pasa un ID, usamos el 1 por defecto
        $id = $companyId ?? 1;

        // 1. MATERIAS PRIMAS (Raw Materials)
        $materials = [
            ['code' => 'MP-HAR-'.$id, 'name' => 'Harina Industrial', 'cost' => 1.25, 'unit' => 'kg'],
            ['code' => 'MP-AZU-'.$id, 'name' => 'Azúcar Blanca', 'cost' => 0.90, 'unit' => 'kg'],
            ['code' => 'MP-AGU-'.$id, 'name' => 'Agua Purificada', 'cost' => 0.10, 'unit' => 'L'],
        ];

        foreach ($materials as $m) {
            RawMaterial::updateOrCreate(
                ['company_id' => $id, 'code' => $m['code']],
                ['name' => $m['name'], 'unit_cost' => $m['cost'], 'unit' => $m['unit']]
            );
        }

        // 2. EMPAQUES (Packaging Materials)
        // Corregido: Se añade el campo 'code' que MySQL exigía
        $packaging = [
            ['code' => 'PKG-ENV-'.$id, 'name' => 'Envase Plástico 500ml', 'cost' => 0.25],
            ['code' => 'PKG-ETI-'.$id, 'name' => 'Etiqueta Adhesiva', 'cost' => 0.05],
        ];

        foreach ($packaging as $p) {
            PackagingMaterial::updateOrCreate(
                ['company_id' => $id, 'code' => $p['code']],
                ['name' => $p['name'], 'unit_cost' => $p['cost']]
            );
        }

        // 3. PROCESOS DE PRODUCCIÓN (Production Processes)
        // Corregido: Se usa 'hours_per_batch' según tu tabla en HeidiSQL
        $processes = ['Mezclado y Amasado', 'Horneado', 'Empacado'];
        foreach ($processes as $proc) {
            ProductionProcess::updateOrCreate(
                ['company_id' => $id, 'name' => $proc],
                ['hours_per_batch' => 1.5] // Valor de ejemplo en horas
            );
        }

        // 4. SUMINISTROS (NUEVO)
        $supplies = [
            ['code' => 'SUM-LIM-'.$id, 'name' => 'Kit de Limpieza Grado Alimenticio', 'cost' => 5.50],
            ['code' => 'SUM-GUA-'.$id, 'name' => 'Guantes de Nitrilo (Caja)', 'cost' => 8.20],
            ['code' => 'SUM-GAS-'.$id, 'name' => 'Gas Industrial (Cilindro)', 'cost' => 15.00],
        ];

        foreach ($supplies as $s) {
            Supply::updateOrCreate(
                ['company_id' => $id, 'code' => $s['code']],
                ['name' => $s['name'], 'unit_cost' => $s['cost']]
            );
        }

        // 5. COSTOS LABORALES (Labor Costs)
        // Configuración de beneficios de ley para Ecuador
        LaborCost::updateOrCreate(
            ['company_id' => $id, 'role' => 'Operario de Producción'],
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

        // 6. GASTOS INDIRECTOS Y MARGEN (Overhead Config)
        // Usando tu modelo OverheadConfig existente
        OverheadConfig::updateOrCreate(
            ['company_id' => $id, 'name' => 'Servicios Básicos y Arriendo'],
            ['percentage' => 15.00, 'is_profit_margin' => false]
        );

        OverheadConfig::updateOrCreate(
            ['company_id' => $id, 'name' => 'Margen de Utilidad'],
            ['percentage' => 30.00, 'is_profit_margin' => true]
        );
    }
}