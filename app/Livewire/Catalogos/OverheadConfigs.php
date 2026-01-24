<?php

namespace App\Livewire\Catalogos;

use App\Models\OverheadConfig;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OverheadConfigs extends Component
{
    public $isOpen = false;
    public $overheadId, $name, $percentage, $is_profit_margin = false;

    protected $rules = [
        'name' => 'required|min:3',
        'percentage' => 'required|numeric|min:0|max:100',
    ];

    public function render()
    {
        // Obtenemos los gastos indirectos y el margen por separado para la vista
        $configs = OverheadConfig::where('company_id', Auth::user()->company_id)->get();
        
        return view('livewire.catalogos.overhead-configs', [
            'indirects' => $configs->where('is_profit_margin', false),
            'margins' => $configs->where('is_profit_margin', true),
        ]);
    }

    public function create($isMargin = false)
    {
        $this->reset(['overheadId', 'name', 'percentage']);
        $this->is_profit_margin = $isMargin;
        $this->resetValidation();
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate();

        OverheadConfig::updateOrCreate(
            ['id' => $this->overheadId],
            [
                'company_id' => Auth::user()->company_id,
                'name' => $this->name,
                'percentage' => $this->percentage,
                'is_profit_margin' => $this->is_profit_margin,
            ]
        );

        $this->dispatch('swal', [
            'message' => $this->overheadId ? 'Configuración actualizada' : 'Configuración creada',
            'type' => 'success'
        ]);

        $this->isOpen = false;
    }

    public function edit($id)
    {
        $config = OverheadConfig::findOrFail($id);
        $this->overheadId = $config->id;
        $this->name = $config->name;
        $this->percentage = $config->percentage;
        $this->is_profit_margin = $config->is_profit_margin;
        $this->isOpen = true;
    }

    public function delete($id)
    {
        OverheadConfig::destroy($id);
        $this->dispatch('swal', [
            'message' => 'Eliminado correctamente',
            'type' => 'success'
        ]);
    }
}