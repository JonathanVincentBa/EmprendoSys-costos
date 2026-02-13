<?php

namespace App\Livewire\CostoProduccion; //

use App\Models\Product; //
use Illuminate\Support\Facades\Auth; //
use Livewire\Component; //
use Livewire\WithPagination; //

class Products extends Component
{
    use WithPagination;

    public $isOpen = false;
    public $productId, $name, $presentation_ml;
    public $packaging_type = 'frasco'; // Valor por defecto válido para el ENUM
    public $is_active = true;
    public $search = '';

    protected $rules = [
        'name' => 'required|min:3',
        'presentation_ml' => 'required|numeric|min:1',
        'packaging_type' => 'required|in:frasco,funda,galon,combo,caja', // Validación según BD
        'is_active' => 'boolean',
    ];

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Iniciamos con query() para que sea un generador de consultas SQL
        $query = Product::query();

        // 2. Filtro de seguridad multi-empresa
        if ($user && !$user->hasRole('super-admin')) {
            $query->where('company_id', $user->company_id);
        }

        // 3. Filtro de búsqueda (usando el query builder)
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // 4. Ordenamos y paginamos en la base de datos
        $products = $query->orderBy('name', 'asc')->paginate(10);

        return view('livewire.costo-produccion.products', ['products' => $products]);
    }

    public function create()
    {
        $this->reset(['productId', 'name', 'presentation_ml', 'packaging_type', 'is_active']);
        $this->packaging_type = 'frasco'; // Asegurar valor inicial
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
        $this->dispatch('swal', ['message' => 'Producto guardado correctamente', 'type' => 'success']);
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->presentation_ml = $product->presentation_ml;
        $this->packaging_type = $product->packaging_type;
        $this->is_active = $product->is_active;
        $this->isOpen = true;
    }
}
