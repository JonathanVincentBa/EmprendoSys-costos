<?php

namespace App\Livewire\CostoProduccion;

use App\Models\OverheadConfig;
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

    public $selected_supplies = []; // Array para suministros seleccionados

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

            // 1. Crear Producto con SKU (Campo obligatorio en tu DB)
            $product = Product::create([
                'company_id' => Auth::user()->company_id,
                'name' => $this->name,
                'sku' => 'PROD-' . strtoupper(bin2hex(random_bytes(3))),
                'presentation_ml' => $this->presentation_ml,
                'packaging_type' => $this->packaging_type,
                'is_active' => true,
            ]);

            // 2. Crear Receta con descripción para evitar nulos
            $recipe = Recipe::create([
                'company_id' => Auth::user()->company_id,
                'product_id' => $product->id,
                'batch_size_ml' => $this->batch_size_ml,
                'description' => 'Receta base para ' . $this->name,
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

            // 4. Guardar Procesos (SIN minutes_required)
            foreach ($this->selected_processes as $proc) {
                DB::table('recipe_processes')->insert([
                    'company_id' => Auth::user()->company_id,
                    'recipe_id'  => $recipe->id,
                    'process_id' => $proc['process_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 5. Guardar Empaque
            DB::table('recipe_packaging')->insert([
                'company_id' => Auth::user()->company_id,
                'recipe_id'  => $recipe->id,
                'packaging_material_id' => $this->packaging_material_id,
                'units_per_batch' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 6. Guardar Uso de Suministros (NUEVO)
            foreach ($this->selected_supplies as $s) {
                DB::table('supply_usages')->insert([
                    'company_id' => Auth::user()->company_id,
                    'recipe_id'  => $recipe->id,
                    'supply_id'  => $s['id'],
                    'quantity'   => 1, // Por ahora 1 kit/unidad por lote
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('products.index')->with('swal', ['message' => '¡Producto guardado!', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            // Esto te mostrará el error real en un mensaje de alerta si algo falla
            $this->dispatch('swal', ['message' => 'Error técnico: ' . $e->getMessage(), 'type' => 'error']);
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
        $user = Auth::user();

        $queryMaterials = RawMaterial::query();
        $queryProcesses = ProductionProcess::query();
        $queryPackaging = PackagingMaterial::query();

        // Si el usuario TIENE una empresa asignada, filtramos.
        // Si es NULL (Super Admin), no filtramos y vemos todo.
        if ($user && !is_null($user->company_id)) {
            $queryMaterials->where('company_id', $user->company_id);
            $queryProcesses->where('company_id', $user->company_id);
            $queryPackaging->where('company_id', $user->company_id);
        }

        return view('livewire.costo-produccion.product-wizard', [
            'all_materials' => $queryMaterials->get(),
            'all_processes' => $queryProcesses->get(),
            'all_packaging' => $queryPackaging->get(),
            'res'           => $this->getResultadosProperty(),
        ]);
    }

    public function getResultadosProperty()
    {
        $materiaPrima = collect($this->ingredients)->sum('subtotal');
        $manoObra = collect($this->selected_processes)->sum('subtotal');

        // 1. Calcular Costo de Suministros Seleccionados
        $totalSuministros = 0;
        foreach ($this->selected_supplies as $s) {
            $totalSuministros += (float) $s['unit_cost']; // Asumiendo costo por lote
        }

        // 2. Obtener el costo del empaque seleccionado
        $costoEmpaque = 0;
        if ($this->packaging_material_id) {
            $packaging = PackagingMaterial::find($this->packaging_material_id);
            $costoEmpaque = $packaging ? $packaging->unit_cost : 0;
        }
        // 3. Sumar al Costo Directo
        // Ahora incluye: Materia Prima + Mano de Obra + Empaque + Suministros
        $costoDirectoLote = $materiaPrima + $manoObra + $costoEmpaque + $totalSuministros;

        // 4. Cargar configuraciones de la empresa actual
        $configs = OverheadConfig::where('company_id', Auth::user()->company_id)->get();

        // Sumar todos los porcentajes que NO son margen de utilidad (Gastos Indirectos)
        $porcentajeIndirectos = $configs->where('is_profit_margin', false)->sum('percentage');

        // 5. Aplicar Gastos Indirectos al costo directo
        $totalConIndirectos = $costoDirectoLote * (1 + ($porcentajeIndirectos / 100));

        // 6. Calcular Unidades por Lote y Costo Unitario
        $unidadesLote = $this->batch_size_ml > 0 && $this->presentation_ml > 0
            ? $this->batch_size_ml / $this->presentation_ml
            : 1;

        $costoUnitarioFinal = $unidadesLote > 0 ? $totalConIndirectos / $unidadesLote : 0;

        return [
            'material' => $materiaPrima,
            'labor'    => $manoObra,
            'packaging' => $costoEmpaque,
            'supplies'  => $totalSuministros,
            'indirects_pct' => $porcentajeIndirectos,
            'total_lote' => $totalConIndirectos,
            'unit_cost'  => $costoUnitarioFinal,
            'suggested'  => $costoUnitarioFinal * (1 + ($this->margin / 100))
        ];
    }
}
