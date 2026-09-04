<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RawMaterial;
use App\Models\ProductionProcess;
use App\Models\PackagingMaterial;
use App\Models\LaborCost;
use App\Models\OverheadConfig;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run($companyId = null): void
    {
        if (!$companyId) return;

        // Cargar insumos creados por ProductCostSeeder
        $materials = RawMaterial::where('company_id', $companyId)->get();
        $processes = ProductionProcess::where('company_id', $companyId)->get();
        $packagings = PackagingMaterial::where('company_id', $companyId)->get();
        $labor = LaborCost::where('company_id', $companyId)->first();
        $overhead = OverheadConfig::where('company_id', $companyId)->where('is_profit_margin', false)->first();
        $margin = OverheadConfig::where('company_id', $companyId)->where('is_profit_margin', true)->first();

        // Calcular costo por hora de mano de obra
        $hourlyLaborCost = 0;
        if ($labor) {
            $totalMonthlySalary = $labor->monthly_salary * (1 + ($labor->iess_rate + $labor->decimo_tercero_rate + $labor->decimo_cuarto_rate + $labor->vacation_rate + $labor->fondo_reserva_rate) / 100);
            $hourlyLaborCost = $totalMonthlySalary / 160; // 160 horas laborales al mes
        }

        for ($i = 1; $i <= 10; $i++) {
            $batchSizeMl = 1000;
            $batchCostAcc = 0;

            // 1. Crear Producto Base
            $product = Product::create([
                'company_id' => $companyId,
                'name' => "Producto Elaborado $i - E$companyId",
                'presentation_ml' => 500,
                'packaging_type' => 'frasco',
                'current_stock' => 50,
                'unit_cost' => 0,
                'price' => 0,
                'is_active' => true,
            ]);

            // 2. Crear Receta
            $recipe = Recipe::create([
                'product_id' => $product->id,
                'company_id' => $companyId,
                'description' => "Fórmula Maestra $i",
                'batch_size_ml' => $batchSizeMl,
            ]);

            // 3. MATERIAS PRIMAS
            if ($materials->isNotEmpty()) {
                $material = $materials->random();
                $qtyKg = 0.5;
                DB::table('recipe_items')->insert([
                    'recipe_id' => $recipe->id,
                    'company_id' => $companyId,
                    'raw_material_id' => $material->id,
                    'quantity_kg' => $qtyKg,
                ]);
                $batchCostAcc += ($material->unit_cost * $qtyKg);
            }

            // 4. MANO DE OBRA
            if ($processes->isNotEmpty()) {
                $process = $processes->random();
                DB::table('recipe_processes')->insert([
                    'recipe_id' => $recipe->id,
                    'process_id' => $process->id,
                    'company_id' => $companyId,
                ]);
                $batchCostAcc += ($process->hours_per_batch * $hourlyLaborCost);
            }

            // 5. EMPAQUES
            $unitsPerBatch = 2; // Rinde 2 frascos de 500ml por cada 1000ml de lote
            if ($packagings->isNotEmpty()) {
                $packaging = $packagings->random();
                DB::table('recipe_packaging')->insert([
                    'recipe_id' => $recipe->id,
                    'packaging_material_id' => $packaging->id,
                    'company_id' => $companyId,
                    'units_per_batch' => $unitsPerBatch,
                ]);
                $batchCostAcc += ($packaging->unit_cost * $unitsPerBatch);
            }

            // 6. CÁLCULO DE COSTOS INDIRECTOS (OVERHEAD) Y MARGEN DE GANANCIA
            $overheadPercent = $overhead ? $overhead->percentage : 15.0;
            $profitMarginPercent = $margin ? $margin->percentage : 30.0;

            // Costo total del lote con indirectos
            $totalBatchCost = $batchCostAcc * (1 + ($overheadPercent / 100));

            // Costo unitario por producto
            $unitCost = $totalBatchCost / $unitsPerBatch;

            // Precio de venta sugerido
            $salePrice = $unitCost * (1 + ($profitMarginPercent / 100));

            // Actualizar producto con los valores finales
            $product->update([
                'unit_cost' => round($unitCost, 4),
                'price' => round($salePrice, 2),
            ]);
        }
    }
}
