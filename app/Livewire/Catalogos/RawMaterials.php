<?php

namespace App\Livewire\Catalogos;

use App\Models\RawMaterial;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RawMaterials extends Component
{
    use WithPagination;

    // Propiedades del modelo
    public $materialId, $code, $name, $unit, $unit_cost;
    
    // Control de UI
    public $search = '';
    public $isOpen = false; 

    protected $rules = [
        'code' => 'required|string|max:20',
        'name' => 'required|string|min:3',
        'unit' => 'required|in:kg,gr,l,ml,unidad',
        'unit_cost' => 'required|numeric|min:0',
    ];

    public function render()
    {
        $materials = RawMaterial::where('company_id', Auth::user()->company_id)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.catalogos.raw-materials', [
            'materials' => $materials
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->materialId = null;
        $this->code = '';
        $this->name = '';
        $this->unit = '';
        $this->unit_cost = '';
    }

    public function store()
    {
        $this->validate();

        $isUpdate = !is_null($this->materialId);

        RawMaterial::updateOrCreate(['id' => $this->materialId], [
            'company_id' => Auth::user()->company_id,
            'code' => $this->code,
            'name' => $this->name,
            'unit' => $this->unit,
            'unit_cost' => $this->unit_cost,
        ]);

        // Lanzar alerta de SweetAlert2
        $this->dispatch('swal', [
            'message' => $isUpdate ? 'Materia prima actualizada con éxito' : 'Materia prima guardada correctamente',
            'type' => 'success'
        ]);

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $material = RawMaterial::findOrFail($id);
        $this->materialId = $id;
        $this->code = $material->code;
        $this->name = $material->name;
        $this->unit = $material->unit;
        $this->unit_cost = $material->unit_cost;

        $this->openModal();
    }

    public function delete($id)
    {
        RawMaterial::find($id)->delete();
        
        // Lanzar alerta de eliminación
        $this->dispatch('swal', [
            'message' => 'Materia prima eliminada',
            'type' => 'warning'
        ]);
    }
}