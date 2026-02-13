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

    // Propiedades del modelo según tu migración
    public $supplyId, $code, $name, $unit_cost;

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Iniciamos la consulta (Query Builder)
        $query = Supply::query();

        // 2. Filtro de Empresa (Tenant)
        if (!$user->hasRole('super-admin')) {
            $query->where('company_id', $user->company_id);
        }

        // 3. Filtro de Búsqueda
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        // 4. Orden y Paginación
        $supplies = $query->latest()->paginate(10);

        return view('livewire.catalogos.supplies', [
            'supplies' => $supplies
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
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|unique:supplies,code,' . $this->supplyId,
            'name' => 'required|min:3',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        $isUpdate = !is_null($this->supplyId);

        Supply::updateOrCreate(['id' => $this->supplyId], [
            'company_id' => Auth::user()->company_id, //
            'code' => $this->code,
            'name' => $this->name,
            'unit_cost' => $this->unit_cost,
        ]);

        $this->isOpen = false;

        $this->dispatch('swal', [
            'message' => $this->supplyId ? 'Registro actualizado' : 'Registro creado con éxito',
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
        Supply::find($id)->delete();

        $this->dispatch('swal', [
            'message' => 'El suministro ha sido eliminado',
            'type' => 'warning' // Color naranja para indicar borrado
        ]);
    }
}
