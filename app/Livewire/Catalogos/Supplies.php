<?php

namespace App\Livewire\Catalogos;

use App\Models\Supply;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Supplies extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;

    // Propiedades del formulario
    public $supplyId, $code, $name, $unit_cost;

    public function render()
    {
        $query = Supply::query();

        // Filtro de Empresa (Capa de seguridad adicional)
        if (!Auth::user()->hasRole('super-admin')) {
            $query->where('company_id', Auth::user()->company_id);
        }

        // Filtro de Búsqueda
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.catalogos.supplies', [
            'supplies' => $query->latest()->paginate(10)
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    private function resetInputFields()
    {
        $this->supplyId = null;
        $this->code = '';
        $this->name = '';
        $this->unit_cost = '';
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|max:50|unique:supplies,code,' . ($this->supplyId ?? 'NULL'),
            'name' => 'required|min:3|max:255',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        Supply::updateOrCreate(['id' => $this->supplyId], [
            'company_id' => Auth::user()->company_id,
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'unit_cost' => $this->unit_cost,
        ]);

        $this->isOpen = false;
        
        $this->dispatch('swal', [
            'message' => $this->supplyId ? 'Suministro actualizado correctamente' : 'Suministro creado con éxito',
            'type' => 'success'
        ]);

        $this->resetInputFields();
    }

    public function edit($id)
    {
        $supply = Supply::findOrFail($id);
        $this->supplyId = $id;
        $this->code = $supply->code;
        $this->name = $supply->name;
        $this->unit_cost = $supply->unit_cost;
        $this->isOpen = true;
    }

    public function delete($id)
    {
        Supply::findOrFail($id)->delete();
        $this->dispatch('swal', ['message' => 'Suministro eliminado', 'type' => 'warning']);
    }
}