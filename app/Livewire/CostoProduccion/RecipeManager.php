<?php

namespace App\Livewire\CostoProduccion;

use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\RawMaterial;
use App\Models\ProductionProcess;
use App\Models\PackagingMaterial;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RecipeManager extends Component
{
    public Product $product;
    public $recipe;
    
    // Datos de la Receta
    public $description, $batch_size_ml;
    
    // Para agregar ingredientes
    public $selected_material_id, $quantity_kg;
    
    // Para agregar procesos y empaques
    public $selected_process_id;
    public $selected_packaging_id, $units_per_batch;

    protected $rules = [
        'batch_size_ml' => 'required|numeric|min:1',
        'description' => 'nullable|string|max:255',
    ];

    public function mount(Product $product)
    {
        $this->product = $product;
        // Buscamos si ya tiene una receta o creamos una instancia nueva
        $this->recipe = Recipe::firstOrNew([
            'product_id' => $product->id,
            'company_id' => Auth::user()->company_id
        ]);

        if ($this->recipe->exists) {
            $this->batch_size_ml = $this->recipe->batch_size_ml;
            $this->description = $this->recipe->description;
        }
    }

    public function saveBaseRecipe()
    {
        $this->validate();
        
        $this->recipe->fill([
            'company_id' => Auth::user()->company_id,
            'description' => $this->description,
            'batch_size_ml' => $this->batch_size_ml,
        ]);
        
        $this->recipe->save();
        $this->dispatch('swal', ['message' => 'Base de receta guardada', 'type' => 'success']);
    }

    public function addIngredient()
    {
        $this->validate([
            'selected_material_id' => 'required',
            'quantity_kg' => 'required|numeric|min:0.0001'
        ]);

        RecipeItem::create([
            'company_id' => Auth::user()->company_id,
            'recipe_id' => $this->recipe->id,
            'raw_material_id' => $this->selected_material_id,
            'quantity_kg' => $this->quantity_kg
        ]);

        $this->reset(['selected_material_id', 'quantity_kg']);
        $this->recipe->load('items');
    }

    public function removeIngredient($id)
    {
        RecipeItem::destroy($id);
        $this->recipe->load('items');
    }

    public function addProcess()
    {
        if($this->selected_process_id) {
            $this->recipe->processes()->attach($this->selected_process_id, [
                'company_id' => Auth::user()->company_id 
            ]);
            $this->recipe->load('processes');
        }
    }

    public function addPackaging()
    {
        $this->validate([
            'selected_packaging_id' => 'required',
            'units_per_batch' => 'required|numeric|min:1'
        ]);

        $this->recipe->packagingMaterials()->attach($this->selected_packaging_id, [
            'units_per_batch' => $this->units_per_batch
        ]);
        
        $this->reset(['selected_packaging_id', 'units_per_batch']);
        $this->recipe->load('packagingMaterials');
    }

    public function render()
    {
        return view('livewire.costo-produccion.recipe-manager', [
            'materials' => RawMaterial::all(),
            'all_processes' => ProductionProcess::all(),
            'all_packagings' => PackagingMaterial::all(),
        ]);
    }
}