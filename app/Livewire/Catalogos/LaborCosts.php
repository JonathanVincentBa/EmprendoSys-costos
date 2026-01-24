<?php

namespace App\Livewire\Catalogos;

use App\Models\LaborCost;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class LaborCosts extends Component
{
    use WithPagination;

    public $isOpen = false;
    public $laborId, $role, $monthly_salary;
    public $iess_rate = 12.15; // Valores por defecto comunes
    public $decimo_tercero_rate = 8.33;
    public $decimo_cuarto_rate = 8.33;
    public $vacation_rate = 4.17;
    public $fondo_reserva_rate = 8.33;
    public $severance_rate = 0;

    protected $rules = [
        'role' => 'required|min:3',
        'monthly_salary' => 'required|numeric|min:0',
        'iess_rate' => 'required|numeric',
        'decimo_tercero_rate' => 'required|numeric',
        'decimo_cuarto_rate' => 'required|numeric',
        'vacation_rate' => 'required|numeric',
        'fondo_reserva_rate' => 'required|numeric',
        'severance_rate' => 'required|numeric',
    ];

    public function render()
    {
        $roles = LaborCost::where('company_id', Auth::user()->company_id)
            ->orderBy('role', 'asc')
            ->paginate(10);

        return view('livewire.catalogos.labor-costs', [
            'roles' => $roles
        ]);
    }

    public function create()
    {
        $this->reset(['laborId', 'role', 'monthly_salary']);
        $this->resetValidation();
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate();

        LaborCost::updateOrCreate(
            ['id' => $this->laborId],
            [
                'company_id' => Auth::user()->company_id,
                'role' => $this->role,
                'monthly_salary' => $this->monthly_salary,
                'iess_rate' => $this->iess_rate,
                'decimo_tercero_rate' => $this->decimo_tercero_rate,
                'decimo_cuarto_rate' => $this->decimo_cuarto_rate,
                'vacation_rate' => $this->vacation_rate,
                'fondo_reserva_rate' => $this->fondo_reserva_rate,
                'severance_rate' => $this->severance_rate,
            ]
        );

        $this->dispatch('swal', [
            'message' => $this->laborId ? 'Rol actualizado' : 'Rol creado con éxito',
            'type' => 'success'
        ]);

        $this->isOpen = false;
    }

    public function edit($id)
    {
        $labor = LaborCost::findOrFail($id);
        $this->laborId = $labor->id;
        $this->role = $labor->role;
        $this->monthly_salary = $labor->monthly_salary;
        $this->iess_rate = $labor->iess_rate;
        $this->decimo_tercero_rate = $labor->decimo_tercero_rate;
        $this->decimo_cuarto_rate = $labor->decimo_cuarto_rate;
        $this->vacation_rate = $labor->vacation_rate;
        $this->fondo_reserva_rate = $labor->fondo_reserva_rate;
        $this->severance_rate = $labor->severance_rate;
        $this->isOpen = true;
    }

    public function delete($id)
    {
        LaborCost::destroy($id);
        $this->dispatch('swal', ['message' => 'Rol eliminado', 'type' => 'success']);
    }
}