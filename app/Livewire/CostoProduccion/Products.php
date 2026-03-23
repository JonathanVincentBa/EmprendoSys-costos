<?php

namespace App\Livewire\CostoProduccion;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Products extends Component
{
    use WithPagination;

    public $isOpen = false;
    public $search = '';

    // Propiedades del formulario (Sin tipos estrictos para evitar el error de inicialización)
    public $productId;
    public $name;
    public $presentation_ml;
    public $packaging_type = 'frasco';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|min:3',
        'presentation_ml' => 'required|numeric|min:1',
        'packaging_type' => 'required|in:frasco,funda,galon,combo,caja',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Product::query();

        if ($user && !$user->hasRole('super-admin')) {
            $query->where('company_id', $user->company_id);
        }

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.costo-produccion.products', [
            'products' => $query->orderBy('name', 'asc')->paginate(10)
        ]);
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['productId', 'name', 'presentation_ml', 'packaging_type', 'is_active']);
        $this->packaging_type = 'frasco'; 
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $product = Product::findOrFail($id);
        
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->presentation_ml = $product->presentation_ml;
        $this->packaging_type = $product->packaging_type;
        $this->is_active = (bool)$product->is_active;

        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate();

        Product::updateOrCreate(
            ['id' => $this->productId],
            [
                'company_id' => Auth::user()->company_id,
                'name' => $this->name,
                'presentation_ml' => $this->presentation_ml,
                'packaging_type' => $this->packaging_type,
                'is_active' => $this->is_active,
            ]
        );

        $this->isOpen = false;
        $this->dispatch('swal', [
            'message' => $this->productId ? 'Producto actualizado correctamente' : 'Producto creado con éxito',
            'type' => 'success'
        ]);
    }

    public function delete($id)
    {
        Product::destroy($id);
        $this->dispatch('swal', ['message' => 'Producto eliminado', 'type' => 'info']);
    }
}