<?php

namespace App\Livewire\Administracion;

use App\Models\Company as CompanyModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Company extends Component
{
    use WithFileUploads;

    public $company_id;
    public $name;
    public $ruc;
    public $address;
    public $phone;
    public $email;
    public $logo; // Nueva imagen
    public $current_logo; // Imagen guardada

    public function mount()
    {
        $company = Auth::user()->company;

        if ($company) {
            $this->company_id = $company->id;
            $this->name = $company->name;
            $this->ruc = $company->ruc;
            $this->address = $company->address;
            $this->phone = $company->phone;
            $this->email = $company->email;
            $this->current_logo = $company->logo;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'ruc' => 'required|digits:13',
            'email' => 'required|email',
            'logo' => 'nullable|image|max:1024',
        ]);

        $company = CompanyModel::find($this->company_id);

        $data = [
            'name' => $this->name,
            'ruc' => $this->ruc,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
        ];

        if ($this->logo) {
            // Eliminar logo anterior si existe
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $this->logo->store('logos', 'public');
            $this->current_logo = $data['logo']; // Actualizar la vista previa
        }

        $company->update($data);

        // LANZAR ALERTA SWEETALERT2
        $this->dispatch('swal', [
            'message' => 'Los datos de la empresa se han actualizado correctamente',
            'type' => 'success'
        ]);
        
        // Limpiar el campo temporal del logo después de subirlo
        $this->reset('logo');
    }

    public function render()
    {
        return view('livewire.administracion.company');
    }
}