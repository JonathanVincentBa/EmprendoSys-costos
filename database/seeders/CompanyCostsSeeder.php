<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RawMaterial;
use App\Models\PackagingMaterial;
use App\Models\ProductionProcess;
use App\Models\OverheadConfig; // Usando tu modelo existente

class CompanyCostsSeeder extends Seeder
{
    public function run($companyId = null): void
    {
        if (!$companyId) return;

        // 1. Materias Primas (raw_materials)
        $materiales = ['Sal', 'Azúcar', 'Agua Filtrada'];
        foreach ($materiales as $m) {
            RawMaterial::create([
                'company_id' => $companyId,
                'code' => "MP-" . rand(100, 999),
                'name' => $m,
                'unit_cost' => rand(1, 5),
                'unit' => 'kg'
            ]);
        }

        // 2. Empaques (packaging_materials)
        $empaques = [
            ['name' => 'Envase Estándar', 'cost' => 0.25, 'suffix' => 'ENV'],
            ['name' => 'Etiqueta Frontal', 'cost' => 0.05, 'suffix' => 'ETQ'],
            ['name' => 'Tapa de Seguridad', 'cost' => 0.10, 'suffix' => 'TAP'],
        ];

        foreach ($empaques as $e) {
            PackagingMaterial::create([
                'company_id' => $companyId,
                'code'       => $e['suffix'] . "-" . rand(100, 999) . "-E" . $companyId, // <--- ESTO FALTA
                'name'       => $e['name'],
                'unit_cost'  => $e['cost'],
            ]);
        }

        // 3. Mano de Obra (production_processes)
        ProductionProcess::create([
            'company_id' => $companyId,
            'name' => 'Proceso de Mezclado',
            'hours_per_batch' => 4.50,
        ]);

        // 4. CONFIGURACIONES DE GASTOS (Tu modelo OverheadConfig)
        // Creamos un Gasto Indirecto (ej. 10% para cubrir servicios)
        OverheadConfig::create([
            'company_id' => $companyId,
            'name' => 'Servicios y Mantenimiento',
            'percentage' => 10.00,
            'is_profit_margin' => false, // Es un costo
        ]);

        // Creamos el Margen de Utilidad deseado (ej. 30%)
        OverheadConfig::create([
            'company_id' => $companyId,
            'name' => 'Margen de Ganancia Sugerido',
            'percentage' => 30.00,
            'is_profit_margin' => true, // Es utilidad
        ]);
    }
}
