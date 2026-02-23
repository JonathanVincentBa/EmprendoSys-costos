<?php

namespace App\Livewire\Administracion;

use App\Models\Company as CompanyModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class Company extends Component
{
    use WithFileUploads, WithPagination;

    // Propiedades para el formulario
    public $company_id;
    public $name;
    public $ruc;
    public $address;
    public $phone;
    public $email;
    public $logo;
    public $current_logo;
    public $status = 'active'; // Nuevo: Para manejar el estado

    // Estado de la vista
    public $isEditing = false;

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && !$user->hasRole('super-admin')) {
            $this->editCompany($user->company_id);
        }
    }

    /**
     * Prepara el formulario para crear una nueva empresa
     */
    public function createCompany()
    {
        $this->reset(['name', 'ruc', 'address', 'phone', 'email', 'logo', 'current_logo', 'company_id']);
        $this->status = 'active';
        $this->isEditing = true;
    }

    public function editCompany($id)
    {
        $this->reset(['logo']);
        $company = CompanyModel::find($id);

        if ($company) {
            $this->company_id   = $company->id;
            $this->name         = $company->name;
            $this->ruc          = $company->ruc;
            $this->address      = $company->address;
            $this->phone        = $company->phone;
            $this->email        = $company->email;
            $this->current_logo = $company->logo;
            $this->status       = $company->status ?? 'active';
            $this->isEditing    = true;
        }
    }

    /**
     * Alterna el estado de la empresa (Activo/Suspendido)
     */
    public function toggleStatus($id)
    {
        $company = CompanyModel::find($id);
        if ($company) {
            $company->status = ($company->status === 'active') ? 'suspended' : 'active';
            $company->save();

            $this->dispatch('swal', [
                'type' => 'success',
                'message' => 'Estado de empresa actualizado a: ' . ucfirst($company->status)
            ]);
        }
    }

    /**
     * Elimina una empresa
     */
    public function deleteCompany($id)
    {
        $company = CompanyModel::find($id);
        if ($company) {
            // Opcional: Eliminar logo del storage
            if ($company->logo) Storage::disk('public')->delete($company->logo);

            $company->delete();
            $this->dispatch('swal', [
                'message' => 'Empresa eliminada correctamente',
                'type'    => 'info'
            ]);
        }
    }

    public function save()
    {
        /** @var User $user */
        $user = Auth::user();

        $this->validate([
            'name'  => 'required|min:3',
            'ruc'   => 'required|digits:13',
            'email' => 'required|email',
            'logo'  => 'nullable|image|max:1024',
        ]);

        // Usamos updateOrCreate para que sirva para Crear y Editar
        $company = CompanyModel::updateOrCreate(
            ['id' => $this->company_id],
            [
                'name'    => $this->name,
                'ruc'     => $this->ruc,
                'address' => $this->address,
                'phone'   => $this->phone,
                'email'   => $this->email,
                'status'  => $this->status,
            ]
        );

        if ($this->logo) {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $company->update([
                'logo' => $this->logo->store('logos', 'public')
            ]);
        }

        $this->dispatch('swal', [
            'message' => $this->company_id ? 'Datos actualizados correctamente' : 'Nueva empresa creada con éxito',
            'type'    => 'success'
        ]);

        if ($user->hasRole('super-admin')) {
            $this->isEditing = false;
            $this->reset(['name', 'ruc', 'address', 'phone', 'email', 'logo', 'current_logo', 'company_id']);
        }
    }

    public function render()
    {
        /** @var User $user */
        $user = Auth::user();

        return view('livewire.administracion.company', [
            'companies' => $user->hasRole('super-admin')
                ? CompanyModel::paginate(10)
                : collect([])
        ]);
    }
}
