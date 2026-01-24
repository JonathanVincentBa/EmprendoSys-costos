<?php

namespace App\Livewire\CostoProduccion;

use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\ProductionProcess;
use App\Models\PackagingMaterial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductWizard extends Component
{
    public $step = 1;

    // Datos del Producto
    public $name, $presentation_ml, $batch_size_ml = 1000;
    public $packaging_type = 'frasco';
    public $packaging_material_id; //

    // Materia Prima
    public $ingredients = [];
    public $selected_material, $quantity_kg;

    // Mano de Obra
    public $selected_processes = [];
    public $process_id;

    // Margen de ganancia
    public $margin = 30;

    public function addIngredient()
    {
        $this->validate([
            'selected_material' => 'required',
            'quantity_kg' => 'required|numeric|min:0.0001'
        ]);

        $material = RawMaterial::find($this->selected_material);
        $costoUnitario = (float) $material->unit_cost;
        $subtotalCalculado = $costoUnitario * (float) $this->quantity_kg;

        $this->ingredients[] = [
            'id' => $material->id,
            'name' => $material->name,
            'price' => $costoUnitario,
            'qty' => (float) $this->quantity_kg,
            'subtotal' => $subtotalCalculado
        ];

        $this->reset(['selected_material', 'quantity_kg']);
    }

    public function addProcess()
    {
        $this->validate(['process_id' => 'required']);

        $proc = ProductionProcess::find($this->process_id);

        // Ahora que el modelo tiene 'hours_per_batch', esto dejará de ser 0
        $costo = (float) ($proc->hours_per_batch ?? 0);

        $this->selected_processes[] = [
            'process_id' => $proc->id,
            'name' => $proc->name,
            'cost' => $costo,
            'hours' => 1.0 // Valor inicial
        ];

        $this->reset('process_id');
    }

    public function calculateTotals()
    {
        $materialCost = collect($this->ingredients)->sum('subtotal');

        // Suma robusta de mano de obra
        $laborCost = collect($this->selected_processes)->reduce(function ($carry, $item) {
            return $carry + ($item['cost'] * $item['hours']);
        }, 0);

        $total = $materialCost + $laborCost;

        return [
            'materials' => $materialCost,
            'labor' => $laborCost,
            'total' => $total,
            'suggested' => ($this->margin < 100 && $total > 0)
                ? $total / (1 - ($this->margin / 100))
                : 0
        ];
    }

    public function saveAll()
    {
        $this->validate([
            'name' => 'required|min:3',
            'presentation_ml' => 'required|numeric',
            'packaging_material_id' => 'required',
            'ingredients' => 'required|array|min:1'
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear Producto
            $product = Product::create([
                'company_id' => Auth::user()->company_id,
                'name' => $this->name,
                'presentation_ml' => $this->presentation_ml,
                'packaging_type' => $this->packaging_type,
                'is_active' => true,
            ]);

            // 2. Crear Receta
            $recipe = Recipe::create([
                'company_id' => Auth::user()->company_id,
                'product_id' => $product->id,
                'batch_size_ml' => $this->batch_size_ml,
                'is_active' => true,
            ]);

            // 3. Guardar Ingredientes
            foreach ($this->ingredients as $item) {
                RecipeItem::create([
                    'company_id' => Auth::user()->company_id,
                    'recipe_id' => $recipe->id,
                    'raw_material_id' => $item['id'],
                    'quantity_kg' => $item['qty'],
                ]);
            }

            // 4. Guardar Procesos (Tabla recipe_processes)
            foreach ($this->selected_processes as $proc) {
                DB::table('recipe_processes')->insert([
                    'company_id' => Auth::user()->company_id,
                    'recipe_id'  => $recipe->id,
                    'process_id' => $proc['process_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 5. Guardar Empaque (Tabla recipe_packaging)
            DB::table('recipe_packaging')->insert([
                'company_id' => Auth::user()->company_id,
                'recipe_id'  => $recipe->id,
                'packaging_material_id' => $this->packaging_material_id,
                'units_per_batch' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('products.index')->with('swal', ['message' => '¡Producto guardado!', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function nextStep()
    {
        $this->step++;
    }
    public function prevStep()
    {
        $this->step--;
    }

    public function render()
    {
        return view('livewire.costo-produccion.product-wizard', [
            'all_materials' => RawMaterial::where('company_id', Auth::user()->company_id)->get(),
            'all_processes' => ProductionProcess::where('company_id', Auth::user()->company_id)->get(),
            'all_packaging' => PackagingMaterial::where('company_id', Auth::user()->company_id)->get(),
            'res' => $this->calculateTotals()
        ]);
    }
}
