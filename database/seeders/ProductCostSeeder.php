<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\RawMaterial;
use App\Models\PackagingMaterial;
use App\Models\Supply;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\ProductionProcess;
use App\Models\RecipeProcess;
use App\Models\SupplyUsage;
use App\Models\OverheadConfig;
use App\Models\LaborCost;
use App\Models\Client;

class ProductCostSeeder extends Seeder
{
    public function run()
    {
        // 1. Empresa de ejemplo
        $company = Company::firstOrCreate([
            'ruc' => '9999999999001'
        ], [
            'name' => 'Miski Sabores',
            'address' => 'Quito, Ecuador',
            'email' => 'info@miski.com',
            'phone' => '0999999999',
            'logo' => null,
        ]);

        // 2. Materias primas
        $rawMaterialsData = [
            ['SAL-01', 'ACEITE DE OLIVA', 7.00, 'l'],
            ['SAL-02', 'ACEITE GIRASOL', 1.99, 'l'],
            ['SAL-03', 'AGUA', 0.10, 'l'],
            ['SAL-04', 'AJI ESCAMA', 2.59, 'kg'],
            ['SAL-05', 'AJI PENCA', 14.05, 'kg'],
            ['SAL-06', 'AJI ROJO', 2.00, 'kg'],
            ['SAL-07', 'AJO', 3.50, 'kg'],
            ['SAL-08', 'ALVERJA', 2.59, 'kg'],
            ['SAL-09', 'BENZOATO', 3.00, 'kg'],
            ['SAL-10', 'CEBOLLA', 2.03, 'kg'],
            ['SAL-11', 'CHILE ARBOL', 25.50, 'kg'],
            ['SAL-12', 'CHIMICHURRI', 8.52, 'kg'],
            ['SAL-13', 'CHOCHO', 2.30, 'kg'],
            ['SAL-14', 'CILANTRO', 1.42, 'kg'],
            ['SAL-15', 'COLA', 0.75, 'kg'],
            ['SAL-16', 'HUMO', 11.00, 'kg'],
            ['SAL-17', 'JALAPEÑO', 2.85, 'kg'],
            ['SAL-18', 'JALEA DE MANGO', 5.52, 'kg'],
            ['SAL-19', 'JALEA DE PIÑA', 2.97, 'kg'],
            ['SAL-20', 'JALEA JALAPEÑO', 1.79, 'kg'],
            ['SAL-21', 'JALEA MARACUYA', 5.96, 'kg'],
            ['SAL-22', 'LAUREL', 1.00, 'kg'],
            ['SAL-23', 'MAGGI', 1.80, 'kg'],
            ['SAL-24', 'MIEL', 10.00, 'kg'],
            ['SAL-25', 'MOSTAZA', 1.80, 'kg'],
            ['SAL-26', 'MOSTAZA DIJON', 8.87, 'kg'],
            ['SAL-27', 'MANI', 3.96, 'kg'],
            ['SAL-28', 'OREGANO', 7.15, 'kg'],
            ['SAL-29', 'PAPRICA', 7.50, 'kg'],
            ['SAL-30', 'PASTA DE MANI', 5.10, 'kg'],
            ['SAL-31', 'PEPA DE SAMBO', 13.50, 'kg'],
            ['SAL-32', 'PIMIENTA', 6.50, 'kg'],
            ['SAL-33', 'PIMIENTO AMARILLO', 2.59, 'kg'],
            ['SAL-34', 'PIMIENTO ROJO-AMARILLO', 2.59, 'kg'],
            ['SAL-35', 'PIMIENTO VERDE', 1.60, 'kg'],
            ['SAL-36', 'ROMERO', 2.00, 'kg'],
            ['SAL-37', 'SAL MARINA', 0.17, 'kg'],
            ['SAL-38', 'SALSA DE TOMATE', 0.57, 'kg'],
            ['SAL-39', 'SORBATO', 7.50, 'kg'],
            ['SAL-40', 'TABASCO', 13.25, 'l'],
            ['SAL-41', 'TOMATE CHERRY', 2.20, 'kg'],
            ['SAL-42', 'TOMATE R Y A', 2.20, 'kg'],
            ['SAL-43', 'TOMILLO', 1.50, 'kg'],
            ['SAL-44', 'VINAGRE BALSAMICO', 9.00, 'l'],
            ['SAL-45', 'VINAGRE BANANO', 3.00, 'l'],
            ['SAL-46', 'VINAGRE DE MANZANA', 2.09, 'l'],
            ['SAL-47', 'VINAGRE VINO', 4.20, 'l'],
            ['SAL-48', 'ZANAHORIA', 1.70, 'kg'],
            ['SAL-49', 'GOMA', 5.30, 'kg'],
            ['SAL-50', 'ACIDO CITRICO', 2.80, 'kg'],
            ['SAL-51', 'AZUCAR', 0.95, 'kg'],
        ];

        foreach ($rawMaterialsData as [$code, $name, $cost, $unit]) {
            RawMaterial::updateOrCreate(
                ['company_id' => $company->id, 'code' => $code],
                [
                    'name' => $name,
                    'unit_cost' => $cost,
                    'unit' => $unit,
                    'company_id' => $company->id,
                ]
            );
        }

        // 3. Materiales de empaque
        $packagingData = [
            ['BAN-210', 'BANDA PVC FRASCO 210', 0.007],
            ['BOT-150', 'BOTELLA ADERESO', 0.440],
            ['BOT-COR', 'BOTELLA CORCHO', 0.667],
            ['CAJGR', 'CAJA MADERA REGALO GRANDE', 2.400],
            ['CAJX4', 'CAJA REGALO PEQUEÑA CARTON', 0.350],
            ['ETQ-CART', 'ETIQUETA 35ML', 0.002],
            ['ETQ-GR', 'ETIQUETA GRANDE', 0.066],
            ['ETQ-PQ', 'ETIQUETA PEQUEÑA', 0.042],
            ['BOT-100', 'FRASCO 110 GR CORTO', 0.391],
            ['BOT-110', 'FRASCO 110 GR LARGO', 0.440],
            ['BOT-210', 'FRASCO 210 GR', 0.458],
            ['BOT-35', 'FRASCO 35 ML', 0.300],
            ['BOT-450', 'FRASCO 450 GR', 0.630],
            ['FUN-BBQ', 'FUNDA BBQ BOQUILLA', 0.012],
            ['FUN-120', 'FUNDA SALSA 120 GR', 0.075],
            ['FUN-165', 'FUNDA SALSA 165 GR', 0.085],
            ['PCEL', 'PAPEL CELOFAN REGALO GRANDE', 0.250],
            ['PIOLA', 'PIOLA REGALO', 0.000],
            ['PLASX5', 'PLASTICO REGALOS AJI PEQ', 0.002],
            ['PORX5', 'PORTA VASOS REGALOS AJI PEQ', 0.750],
            ['FUN-450', 'FUNDA GRANDE SAL PARRILLERA', 0.150],
            ['ETQ-BBQ', 'ETIQUETA BBQ', 0.050],
            ['GALON', 'GALON', 1.500],
            ['PAJA', 'PAJA', 0.437],
        ];

        foreach ($packagingData as [$code, $name, $cost]) {
            PackagingMaterial::updateOrCreate(
                ['company_id' => $company->id, 'code' => $code],
                [
                    'name' => $name,
                    'unit_cost' => $cost,
                    'company_id' => $company->id,
                ]
            );
        }

        // 4. Suministros
        $suppliesData = [
            ['ENR', 'Energía eléctrica (Kw/h)', 0.10],
            ['GAS', 'GAS', 0.106],
            ['AGUA', 'Agua (L)', 0.20],
        ];

        foreach ($suppliesData as [$code, $name, $cost]) {
            Supply::updateOrCreate(
                ['company_id' => $company->id, 'code' => $code],
                [
                    'name' => $name,
                    'unit_cost' => $cost,
                    'company_id' => $company->id,
                ]
            );
        }

        // 5. Gastos indirectos y utilidad
        $overheads = [
            ['Maquinaria', 2.00, false],
            ['Construcción', 1.00, false],
            ['GASTOS ADMINISTRATIVOS', 10.00, false],
            ['Imprevistos', 2.00, false],
            ['UTILIDAD', 10.00, true],
        ];

        foreach ($overheads as [$name, $pct, $isProfit]) {
            OverheadConfig::updateOrCreate(
                ['company_id' => $company->id, 'name' => $name],
                [
                    'percentage' => $pct,
                    'is_profit_margin' => $isProfit,
                    'company_id' => $company->id,
                ]
            );
        }

        // 6. Mano de obra
        LaborCost::updateOrCreate(
            ['company_id' => $company->id, 'role' => 'Operario'],
            [
                'monthly_salary' => 450.00,
                'iess_rate' => 9.45,
                'decimo_tercero_rate' => 8.33,
                'decimo_cuarto_rate' => 8.33,
                'vacation_rate' => 4.17,
                'fondo_reserva_rate' => 8.33,
                'severance_rate' => 13.89,
                'company_id' => $company->id,
            ]
        );

        LaborCost::updateOrCreate(
            ['company_id' => $company->id, 'role' => 'Auxiliar'],
            [
                'monthly_salary' => 800.00,
                'iess_rate' => 9.45,
                'decimo_tercero_rate' => 8.33,
                'decimo_cuarto_rate' => 8.33,
                'vacation_rate' => 4.17,
                'fondo_reserva_rate' => 8.33,
                'severance_rate' => 13.89,
                'company_id' => $company->id,
            ]
        );

        // 7. Procesos de producción
        $processes = [
            ['PICADO', 4],
            ['LAVADO', 4],
            ['REPOSADO', 4],
            ['ENVASADO', 3],
            ['ETIQUETADO', 1],
        ];

        $processModels = [];
        foreach ($processes as [$name, $hours]) {
            $processModels[] = ProductionProcess::updateOrCreate(
                ['company_id' => $company->id, 'name' => $name],
                [
                    'hours_per_batch' => $hours,
                    'company_id' => $company->id,
                ]
            );
        }

        // 8. Producto: Chimichurri frasco 210ml
        $chimiFrasco = Product::updateOrCreate(
            ['company_id' => $company->id, 'name' => 'Chimichurri frasco'],
            [
                'presentation_ml' => 210,
                'packaging_type' => 'frasco',
                'is_active' => true,
                'company_id' => $company->id,
            ]
        );

        $recipe = Recipe::updateOrCreate(
            ['product_id' => $chimiFrasco->id],
            [
                'batch_size_ml' => 12810, // 61 * 210
                'description' => 'Receta estándar mayo 2024',
                'company_id' => $company->id,
            ]
        );

        // Insumos de la receta
        $recipeItems = [
            ['SAL-14', 5.5],
            ['SAL-07', 0.258],
            ['SAL-40', 0.393],
            ['SAL-25', 0.090],
            ['SAL-02', 2.259],
            ['SAL-44', 0.516],
            ['SAL-47', 0.516],
            ['SAL-03', 0.865],
            ['SAL-28', 0.019],
            ['SAL-29', 0.039],
            ['SAL-32', 0.010],
            ['SAL-34', 0.581],
            ['SAL-33', 0.581],
            ['SAL-35', 0.581],
            ['SAL-10', 0.581],
            ['SAL-39', 0.013],
            ['SAL-09', 0.013],
        ];

        foreach ($recipeItems as [$code, $qty]) {
            $material = RawMaterial::where('company_id', $company->id)->where('code', $code)->first();
            if ($material) {
                RecipeItem::updateOrCreate(
                    ['recipe_id' => $recipe->id, 'raw_material_id' => $material->id],
                    [
                        'quantity_kg' => $qty,
                        'company_id' => $company->id,
                    ]
                );
            }
        }

        // Empaques
        $frasco210 = PackagingMaterial::where('company_id', $company->id)->where('code', 'BOT-210')->first();
        $banda = PackagingMaterial::where('company_id', $company->id)->where('code', 'BAN-210')->first();
        $etiqueta = PackagingMaterial::where('company_id', $company->id)->where('code', 'ETQ-GR')->first();

        if ($frasco210) {
            $recipe->packagingMaterials()->syncWithoutDetaching([
                $frasco210->id => ['company_id' => $company->id, 'units_per_batch' => 61]
            ]);
        }
        if ($banda) {
            $recipe->packagingMaterials()->syncWithoutDetaching([
                $banda->id => ['company_id' => $company->id, 'units_per_batch' => 61]
            ]);
        }
        if ($etiqueta) {
            $recipe->packagingMaterials()->syncWithoutDetaching([
                $etiqueta->id => ['company_id' => $company->id, 'units_per_batch' => 61]
            ]);
        }

        // Suministros usados
        $energia = Supply::where('company_id', $company->id)->where('code', 'ENR')->first();
        $agua = Supply::where('company_id', $company->id)->where('code', 'AGUA')->first();

        if ($energia) {
            SupplyUsage::updateOrCreate(
                ['recipe_id' => $recipe->id, 'supply_id' => $energia->id],
                ['quantity' => 11.67, 'company_id' => $company->id]
            );
        }
        if ($agua) {
            SupplyUsage::updateOrCreate(
                ['recipe_id' => $recipe->id, 'supply_id' => $agua->id],
                ['quantity' => 3.00, 'company_id' => $company->id]
            );
        }

        // Procesos
        foreach ($processModels as $proc) {
            RecipeProcess::updateOrCreate(
                ['recipe_id' => $recipe->id, 'process_id' => $proc->id],
                ['company_id' => $company->id]
            );
        }
    }
}