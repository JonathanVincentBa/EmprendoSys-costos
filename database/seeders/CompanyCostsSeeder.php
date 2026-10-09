<?php

namespace Database\Seeders;

use App\Models\OverheadConfig;
use App\Models\PackagingMaterial;
use App\Models\ProductionProcess;
use App\Models\RawMaterial;
use Illuminate\Database\Seeder;

class CompanyCostsSeeder extends Seeder
{
    public function run($companyId = null): void
    {
        if (!$companyId) {
            return;
        }

        $materials = [
            ['code' => "MP-SAL-C{$companyId}", 'name' => 'Sal', 'cost' => 1.00, 'unit' => 'kg'],
            ['code' => "MP-AZU-C{$companyId}", 'name' => 'Azúcar', 'cost' => 2.00, 'unit' => 'kg'],
            ['code' => "MP-AGU-C{$companyId}", 'name' => 'Agua Filtrada', 'cost' => 1.00, 'unit' => 'l'],
        ];

        foreach ($materials as $material) {
            RawMaterial::firstOrCreate(
                ['company_id' => $companyId, 'code' => $material['code']],
                [
                    'name' => $material['name'],
                    'unit_cost' => $material['cost'],
                    'unit' => $material['unit'],
                ]
            );
        }

        $packagingMaterials = [
            ['name' => 'Envase Estándar', 'cost' => 0.25, 'code' => 'ENV'],
            ['name' => 'Etiqueta Frontal', 'cost' => 0.05, 'code' => 'ETQ'],
            ['name' => 'Tapa de Seguridad', 'cost' => 0.10, 'code' => 'TAP'],
        ];

        foreach ($packagingMaterials as $material) {
            PackagingMaterial::firstOrCreate(
                ['code' => $material['code'] . '-E' . $companyId],
                [
                    'company_id' => $companyId,
                    'name' => $material['name'],
                    'unit_cost' => $material['cost'],
                ]
            );
        }

        ProductionProcess::firstOrCreate(
            ['company_id' => $companyId, 'name' => 'Proceso de Mezclado'],
            ['hours_per_batch' => 4.50]
        );

        OverheadConfig::updateOrCreate(
            ['company_id' => $companyId, 'is_profit_margin' => false],
            ['name' => 'Servicios y Mantenimiento', 'percentage' => 10.00]
        );

        OverheadConfig::updateOrCreate(
            ['company_id' => $companyId, 'is_profit_margin' => true],
            ['name' => 'Margen de Ganancia Sugerido', 'percentage' => 30.00]
        );
    }
}
