<?php

namespace App\Livewire\CostoProduccion;

use App\Models\OverheadConfig;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\ProductionProcess;
use App\Models\PackagingMaterial;
use App\Models\Supply;
use App\Services\CostCalculatorService;
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

    public $selected_supplies = [];
    public $selected_supply_id;
    public $supply_quantity = 1;

    // Margen de ganancia
    public $margin = 30;

    public function addIngredient()
    {
        $this->validate([
            'selected_material' => 'required|integer',
            'quantity_kg' => 'required|numeric|min:0.0001'
        ]);

        $material = RawMaterial::findOrFail($this->selected_material);
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
        $this->validate(['process_id' => 'required|integer']);

        $proc = ProductionProcess::findOrFail($this->process_id);

        $this->selected_processes[] = [
            'process_id' => $proc->id,
            'name' => $proc->name,
            'cost' => $this->hourlyLaborRate(),
            'hours' => (float) $proc->hours_per_batch,
        ];

        $this->reset('process_id');
    }

    public function addSupply()
    {
        $this->validate([
            'selected_supply_id' => 'required|integer',
            'supply_quantity' => 'required|numeric|min:0.0001',
        ]);

        $supply = Supply::findOrFail($this->selected_supply_id);
        $this->selected_supplies[] = [
            'id' => $supply->id,
            'name' => $supply->name,
            'quantity' => (float) $this->supply_quantity,
        ];

        $this->reset(['selected_supply_id', 'supply_quantity']);
        $this->supply_quantity = 1;
    }

    public function calculateTotals()
    {
        $results = $this->getResultadosProperty();

        return [
            'materials' => $results['material'],
            'labor' => $results['labor'],
            'total' => $results['total'],
            'suggested' => $results['suggested'],
        ];
    }

    public function saveAll()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'presentation_ml' => 'required|integer|min:1',
            'batch_size_ml' => 'required|integer|min:1',
            'packaging_material_id' => 'required|integer',
            'packaging_type' => 'required|in:frasco,funda,galon,combo,caja',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'required|integer',
            'ingredients.*.qty' => 'required|numeric|min:0.0001',
            'selected_processes' => 'array',
            'selected_processes.*.process_id' => 'required|integer',
            'selected_processes.*.hours' => 'required|numeric|min:0.01',
            'selected_supplies' => 'array',
            'selected_supplies.*.id' => 'required|integer',
            'selected_supplies.*.quantity' => 'required|numeric|min:0.0001',
            'margin' => 'required|numeric|min:0|max:1000',
        ]);
        if ($this->batch_size_ml < $this->presentation_ml) {
            $this->addError('batch_size_ml', 'El lote debe alcanzar al menos una unidad del producto.');
            return;
        }

        try {
            $companyId = Auth::user()->company_id;
            abort_if(!$companyId, 403);

            $product = DB::transaction(function () use ($companyId) {
                $packaging = PackagingMaterial::findOrFail($this->packaging_material_id);
                $materials = RawMaterial::whereIn('id', collect($this->ingredients)->pluck('id'))
                    ->get()
                    ->keyBy('id');
                $processIds = collect($this->selected_processes)->pluck('process_id')->unique();
                $processes = ProductionProcess::whereIn('id', $processIds)->get()->keyBy('id');
                $supplyIds = collect($this->selected_supplies)->pluck('id')->unique();
                $supplies = Supply::whereIn('id', $supplyIds)->get()->keyBy('id');

                if ($materials->count() !== collect($this->ingredients)->pluck('id')->unique()->count()
                    || $processes->count() !== $processIds->count()
                    || $supplies->count() !== $supplyIds->count()) {
                    throw new \RuntimeException('Uno o más insumos, procesos o suministros ya no están disponibles.');
                }

                $product = Product::create([
                    'company_id' => $companyId,
                    'name' => $this->name,
                    'sku' => 'PROD-' . strtoupper(bin2hex(random_bytes(3))),
                    'presentation_ml' => $this->presentation_ml,
                    'packaging_type' => $this->packaging_type,
                    'is_active' => true,
                ]);

                $recipe = Recipe::create([
                    'company_id' => $companyId,
                    'product_id' => $product->id,
                    'batch_size_ml' => $this->batch_size_ml,
                    'description' => 'Receta base para ' . $this->name,
                ]);

                foreach ($this->ingredients as $item) {
                    RecipeItem::create([
                        'company_id' => $companyId,
                        'recipe_id' => $recipe->id,
                        'raw_material_id' => $item['id'],
                        'quantity_kg' => $item['qty'],
                    ]);
                }

                foreach ($this->selected_processes as $process) {
                    DB::table('recipe_processes')->insert([
                        'company_id' => $companyId,
                        'recipe_id' => $recipe->id,
                        'process_id' => $process['process_id'],
                        'hours_per_batch' => $process['hours'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('recipe_packaging')->insert([
                    'company_id' => $companyId,
                    'recipe_id' => $recipe->id,
                    'packaging_material_id' => $packaging->id,
                    'units_per_batch' => max(1, (int) floor($this->batch_size_ml / $this->presentation_ml)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($this->selected_supplies as $supply) {
                    DB::table('supply_usages')->insert([
                        'company_id' => $companyId,
                        'recipe_id' => $recipe->id,
                        'supply_id' => $supply['id'],
                        'quantity' => $supply['quantity'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $cost = app(CostCalculatorService::class)->calculateUnitCost($product->id);
                $product->update([
                    'unit_cost' => $cost['unit_cost'],
                    'price' => round($cost['unit_cost'] * (1 + ($this->margin / 100)), 2),
                ]);

                return $product;
            });

            return redirect()->route('products.index')->with('swal', ['message' => '¡Producto guardado!', 'type' => 'success']);
        } catch (\Exception $e) {
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
            'all_supplies' => Supply::query()->get(),
            'res'           => $this->getResultadosProperty(),
        ]);
    }

    public function getResultadosProperty()
    {
        $materiaPrima = collect($this->ingredients)->sum(function ($ingredient) {
            $material = !empty($ingredient['id'])
                ? RawMaterial::find($ingredient['id'])
                : null;

            return (float) ($ingredient['qty'] ?? 0) * (float) ($material?->unit_cost ?? 0);
        });
        $manoObra = collect($this->selected_processes)->sum(
            fn ($process) => $this->hourlyLaborRate() * (float) ($process['hours'] ?? 0)
        );
        $costoSuministros = collect($this->selected_supplies)->sum(function ($selection) {
            $supply = !empty($selection['id']) ? Supply::find($selection['id']) : null;

            return (float) ($selection['quantity'] ?? 0) * (float) ($supply?->unit_cost ?? 0);
        });

        // Obtener el costo del empaque seleccionado.
        $costoEmpaque = 0;
        if ($this->packaging_material_id) {
            $packaging = PackagingMaterial::find($this->packaging_material_id);
            $costoEmpaque = $packaging ? $packaging->unit_cost : 0;
        }
        $unidadesLote = $this->batch_size_ml > 0 && $this->presentation_ml > 0
            ? max(1, (int) floor($this->batch_size_ml / $this->presentation_ml))
            : 0;
        $costoEmpaque *= $unidadesLote;
        $costoDirectoLote = $materiaPrima + $manoObra + $costoEmpaque + $costoSuministros;

        // Aplicar únicamente gastos indirectos al costo de producción.
        $configs = OverheadConfig::where('company_id', Auth::user()->company_id)->get();

        $porcentajeIndirectos = $configs->where('is_profit_margin', false)->sum('percentage');

        $totalConIndirectos = $costoDirectoLote * (1 + ($porcentajeIndirectos / 100));

        $costoUnitarioFinal = $unidadesLote > 0 ? $totalConIndirectos / $unidadesLote : 0;

        return [
            'material' => $materiaPrima,
            'labor'    => $manoObra,
            'packaging' => $costoEmpaque,
            'supplies' => $costoSuministros,
            'indirects_pct' => $porcentajeIndirectos,
            'total' => $totalConIndirectos,
            'total_lote' => $totalConIndirectos,
            'unit_cost'  => $costoUnitarioFinal,
            'suggested'  => $costoUnitarioFinal * (1 + ($this->margin / 100))
        ];
    }

    private function hourlyLaborRate(): float
    {
        $company = Auth::user()->company;

        return $company
            ? app(CostCalculatorService::class)->hourlyLaborRate($company)
            : 0;
    }
}
