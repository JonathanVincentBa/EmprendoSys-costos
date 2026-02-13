<?php

namespace App\Livewire\Catalogos;

use App\Models\PackagingMaterial;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PackagingMaterials extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $packagingId, $code, $name, $unit_cost;

    protected $rules = [
        'code' => 'required|unique:packaging_materials,code',
        'name' => 'required|min:3',
        'unit_cost' => 'required|numeric|min:0',
    ];

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Iniciamos el Query Builder (SIN ejecutar all())
        $query = PackagingMaterial::query();

        // 2. Filtro Multi-tenant (Seguridad)
        if (!$user->hasRole('super-admin')) {
            $query->where('company_id', $user->company_id);
        }

        // 3. Filtro de búsqueda (Si existe el campo search)
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        // 4. Ordenar y Paginación (Ahora sí funcionará)
        $packagings = $query->latest()->paginate(10);

        return view('livewire.catalogos.packaging-materials', [
            'packagings' => $packagings
        ]);
    }

    public function create()
    {
        $this->reset(['packagingId', 'code', 'name', 'unit_cost']);
        $this->resetValidation();
        $this->isOpen = true;
    }

    public function store()
    {
        $validationRules = $this->rules;
        if ($this->packagingId) {
            $validationRules['code'] = 'required|unique:packaging_materials,code,' . $this->packagingId;
        }

        $this->validate($validationRules);

        PackagingMaterial::updateOrCreate(
            ['id' => $this->packagingId],
            [
                'company_id' => Auth::user()->company_id,
                'code' => $this->code,
                'name' => $this->name,
                'unit_cost' => $this->unit_cost,
            ]
        );

        $this->dispatch('swal', [
            'message' => $this->packagingId ? 'Empaque actualizado' : 'Empaque creado con éxito',
            'type' => 'success'
        ]);

        $this->isOpen = false;
    }

    public function edit($id)
    {
        $packaging = PackagingMaterial::findOrFail($id);
        $this->packagingId = $packaging->id;
        $this->code = $packaging->code;
        $this->name = $packaging->name;
        $this->unit_cost = $packaging->unit_cost;
        $this->isOpen = true;
    }

    public function delete($id)
    {
        PackagingMaterial::destroy($id);
        $this->dispatch('swal', [
            'message' => 'Empaque eliminado correctamente',
            'type' => 'success'
        ]);
    }
}
