<?php

namespace App\Services;

use App\Models\Product;
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

        // 1. Materiales directos
        $directMaterials = 0;
        foreach ($recipe->items as $item) {
            $directMaterials += $item->quantity_kg * $item->rawMaterial->unit_cost;
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
            $suppliesCost += $usage->quantity * $usage->supply->unit_cost;
        }

        // 4. Mano de obra (cálculo dinámico con beneficios legales)
        $laborCost = 0;
        foreach ($product->company->laborCosts as $lc) {
            $base = $lc->monthly_salary;
            $total = $base +
                     ($base * $lc->iess_rate / 100) +
                     ($base * $lc->decimo_tercero_rate / 100) +
                     ($base * $lc->decimo_cuarto_rate / 100) +
                     ($base * $lc->vacation_rate / 100) +
                     ($base * $lc->fondo_reserva_rate / 100) +
                     ($base * $lc->severance_rate / 100);
            $laborCost += $total;
        }

        // 5. Subtotal antes de gastos indirectos y utilidad
        $subtotal = $directMaterials + $packagingCost + $suppliesCost + $laborCost;

        // 6. Aplicar gastos indirectos y utilidad
        $overheads = 0;
        $profitMargin = 0;
        foreach ($product->company->overheadConfigs as $config) {
            $amount = ($config->percentage / 100) * $subtotal;
            if ($config->is_profit_margin) {
                $profitMargin += $amount;
            } else {
                $overheads += $amount;
            }
        }

        // 7. Total final y costo por unidad
        $totalCost = $subtotal + $overheads + $profitMargin;
        $unitCost = ($totalCost / $recipe->batch_size_ml) * $product->presentation_ml;

        // Devolver resultado estructurado
        return [
            'product_name' => $product->name,
            'presentation_ml' => $product->presentation_ml,
            'batch_size_ml' => $recipe->batch_size_ml,
            'units_per_batch' => round($recipe->batch_size_ml / $product->presentation_ml),
            'direct_materials' => round($directMaterials, 2),
            'packaging' => round($packagingCost, 2),
            'supplies' => round($suppliesCost, 2),
            'labor' => round($laborCost, 2),
            'subtotal_before_overheads' => round($subtotal, 2),
            'overheads' => round($overheads, 2),
            'profit_margin' => round($profitMargin, 2),
            'total_cost' => round($totalCost, 2),
            'unit_cost' => round($unitCost, 2),
        ];
    }
}