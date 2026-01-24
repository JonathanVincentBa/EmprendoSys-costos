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
        return view('livewire.catalogos.supplies', [
            'supplies' => Supply::where('company_id', Auth::user()->company_id)
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(10)
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
