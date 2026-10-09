<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Company;
use App\Models\Recipe;
use Exception;

class CostCalculatorService
{
    /**
     * Calcula el costo unitario de un producto.
     *
     * @param int $productId
     * @return array Resultado detallado del cálculo
     * @throws Exception Si no se encuentra el producto o su receta
     */
    public function calculateUnitCost(int $productId): array
    {
        // Cargar el producto con todas sus relaciones necesarias
        $product = Product::with([
            'recipes',
            'recipes.items.rawMaterial',
            'recipes.packagingMaterials',
            'recipes.supplyUsages.supply',
            'company.laborCosts',
            'company.overheadConfigs'
        ])->findOrFail($productId);

        $recipe = $product->recipes->first();
        if (!$recipe) {
            throw new Exception("No se encontró una receta para el producto: {$product->name}");
        }
        if ($recipe->batch_size_ml <= 0 || $product->presentation_ml <= 0) {
            throw new Exception("El lote y la presentación deben ser mayores que cero.");
        }

        // 1. Materiales directos
        $directMaterials = 0;
        foreach ($recipe->items as $item) {
            $directMaterials += $item->quantity_kg * ($item->rawMaterial?->unit_cost ?? 0);
        }

        // 2. Materiales de empaque
        $packagingCost = 0;
        foreach ($recipe->packagingMaterials as $packaging) {
            $units = $packaging->pivot->units_per_batch;
            $packagingCost += $units * $packaging->unit_cost;
        }

        // 3. Suministros (agua, luz, gas)
        $suppliesCost = 0;
        foreach ($recipe->supplyUsages as $usage) {
            $suppliesCost += $usage->quantity * ($usage->supply?->unit_cost ?? 0);
        }

        // 4. Mano de obra por hora de proceso, con beneficios legales incluidos.
        $laborCost = $this->calculateLaborCost($recipe);

        // 5. Subtotal antes de gastos indirectos y utilidad
        $subtotal = $directMaterials + $packagingCost + $suppliesCost + $laborCost;

        // 6. Aplicar gastos indirectos y utilidad
        $overheads = 0;
        $profitMarginRate = 0;
        foreach ($product->company->overheadConfigs as $config) {
            if ($config->is_profit_margin) {
                $profitMarginRate += $config->percentage;
            } else {
                $overheads += ($config->percentage / 100) * $subtotal;
            }
        }

        // 7. El costo de producción no incluye utilidad; esta solo incrementa el precio sugerido.
        $totalCost = $subtotal + $overheads;
        $unitsPerBatch = intdiv($recipe->batch_size_ml, $product->presentation_ml);
        if ($unitsPerBatch <= 0) {
            throw new Exception("El tamaño del lote debe alcanzar al menos una unidad de {$product->name}.");
        }

        $unitCost = $totalCost / $unitsPerBatch;
        $profitMargin = $totalCost * ($profitMarginRate / 100);

        // Devolver resultado estructurado
        return [
            'product_name' => $product->name,
            'presentation_ml' => $product->presentation_ml,
            'batch_size_ml' => $recipe->batch_size_ml,
            'units_per_batch' => $unitsPerBatch,
            'direct_materials' => round($directMaterials, 2),
            'packaging' => round($packagingCost, 2),
            'supplies' => round($suppliesCost, 2),
            'labor' => round($laborCost, 2),
            'subtotal_before_overheads' => round($subtotal, 2),
            'overheads' => round($overheads, 2),
            'profit_margin' => round($profitMargin, 2),
            'total_cost' => round($totalCost, 2),
            'unit_cost' => round($unitCost, 2),
            'suggested_price' => round($unitCost * (1 + ($profitMarginRate / 100)), 2),
        ];
    }

    public function calculateLaborCost(Recipe $recipe): float
    {
        $recipe->loadMissing(['company.laborCosts', 'processes']);
        $hoursPerBatch = $recipe->processes->sum(
            fn ($process) => $process->pivot->hours_per_batch ?: $process->hours_per_batch
        );

        return $this->hourlyLaborRate($recipe->company) * $hoursPerBatch;
    }

    public function hourlyLaborRate(Company $company): float
    {
        $monthlyLaborCost = $company->laborCosts->sum(function ($laborCost) {
            $rates = $laborCost->iess_rate
                + $laborCost->decimo_tercero_rate
                + $laborCost->decimo_cuarto_rate
                + $laborCost->vacation_rate
                + $laborCost->fondo_reserva_rate
                + $laborCost->severance_rate;

            return $laborCost->monthly_salary * (1 + ($rates / 100));
        });

        return $monthlyLaborCost / 160;
    }
}