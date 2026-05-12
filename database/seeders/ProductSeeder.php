<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RawMaterial;
use App\Models\ProductionProcess;
use App\Models\PackagingMaterial;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run($companyId = null): void
    {
        if (!$companyId) return;

        for ($i = 1; $i <= 10; $i++) {
            // 1. Crear Producto
            $product = Product::create([
                'company_id' => $companyId,
                'name' => "Producto Elaborado $i - E$companyId",
                'presentation_ml' => 500,
                'packaging_type' => 'frasco',
                'current_stock' => 50,
                'is_active' => true,
            ]);

            // 2. Crear Receta Base
            $recipe = Recipe::create([
                'product_id' => $product->id,
                'company_id' => $companyId,
                'description' => "Fórmula Maestra $i",
                'batch_size_ml' => 1000,
            ]);

            // 3. VINCULAR COSTOS (Materia Prima)
            $material = RawMaterial::where('company_id', $companyId)->inRandomOrder()->first();
            if ($material) {
                DB::table('recipe_items')->insert([
                    'recipe_id' => $recipe->id,
                    'company_id' => $companyId,
                    'raw_material_id' => $material->id,
                    'quantity_kg' => 0.5,
                ]);
            }

            // 4. VINCULAR MANO DE OBRA
            $process = ProductionProcess::where('company_id', $companyId)->inRandomOrder()->first();
            if ($process) {
                DB::table('recipe_processes')->insert([
                    'recipe_id' => $recipe->id,
                    'process_id' => $process->id,
                    'company_id' => $companyId,
                ]);
            }

            // 5. VINCULAR EMPAQUE
            $packaging = PackagingMaterial::where('company_id', $companyId)->inRandomOrder()->first();
            if ($packaging) {
                DB::table('recipe_packaging')->insert([
                    'recipe_id' => $recipe->id,
                    'packaging_material_id' => $packaging->id,
                    'company_id' => $companyId,
                    'units_per_batch' => 2, // Se usan 2 unidades por lote
                ]);
            }
        }
    }
}