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

    // Propiedades generales y legales
    public $company_id;
    public $name;
    public $razon_social;
    public $ruc;
    public $address;
    public $establishment_address;
    public $phone;
    public $email;
    public $logo;
    public $current_logo;
    public $status = 'active';

    // Configuración Tributaria SRI
    public $estab = '001';
    public $pto_emi = '001';
    public $contribuyente_especial;
    public $obligado_contabilidad = 'NO';
    public $contribuyente_rimpe;
    public $sri_environment = '1';

    // Propiedades para Firma Digital
    public $signature_file;
    public $signature_password = '';
    public $has_signature = false;

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

    public function createCompany()
    {
        abort_unless($this->isSuperAdmin(), 403);

        $this->reset([
            'company_id',
            'name',
            'razon_social',
            'ruc',
            'address',
            'establishment_address',
            'phone',
            'email',
            'logo',
            'current_logo',
            'contribuyente_especial',
            'contribuyente_rimpe',
            'signature_file',
            'signature_password'
        ]);

        $this->status = 'active';
        $this->estab = '001';
        $this->pto_emi = '001';
        $this->obligado_contabilidad = 'NO';
        $this->sri_environment = '1';
        $this->has_signature = false;
        $this->isEditing = true;
    }

    public function editCompany($id)
    {
        $this->reset(['logo', 'signature_file', 'signature_password']);
        /** @var User $user */
        $user = Auth::user();
        $company = $user->hasRole('super-admin')
            ? CompanyModel::find($id)
            : CompanyModel::whereKey($user->company_id)->find($id);

        if ($company) {
            $this->company_id             = $company->id;
            $this->name                   = $company->name;
            $this->razon_social           = $company->razon_social;
            $this->ruc                    = $company->ruc;
            $this->address                = $company->address;
            $this->establishment_address  = $company->establishment_address;
            $this->phone                  = $company->phone;
            $this->email                  = $company->email;
            $this->current_logo           = $company->logo;
            $this->status                 = $company->status ?? 'active';

            // Campos SRI
            $this->estab                  = $company->estab ?? '001';
            $this->pto_emi                = $company->pto_emi ?? '001';
            $this->contribuyente_especial = $company->contribuyente_especial;
            $this->obligado_contabilidad  = $company->obligado_contabilidad ?? 'NO';
            $this->contribuyente_rimpe    = $company->contribuyente_rimpe;
            $this->sri_environment        = $company->sri_environment ?? '1';

            // Firma
            $this->has_signature          = !empty($company->signature_path);
            $this->isEditing              = true;
        }
    }

    public function toggleStatus($id)
    {
        abort_unless($this->isSuperAdmin(), 403);

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

    public function deleteCompany($id)
    {
        abort_unless($this->isSuperAdmin(), 403);

        $company = CompanyModel::find($id);
        if ($company) {
            if ($company->logo) Storage::disk('public')->delete($company->logo);
            if ($company->signature_path) Storage::disk('local')->delete($company->signature_path);

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

        if ($user->hasRole('admin')) {
            abort_unless($this->company_id && (int) $this->company_id === (int) $user->company_id, 403);
            $this->company_id = $user->company_id;
        }

        $rules = [
            'name'                  => 'required|min:3',
            'razon_social'          => 'required|min:3',
            'ruc'                   => 'required|digits:13',
            'email'                 => 'required|email',
            'estab'                 => 'required|digits:3',
            'pto_emi'               => 'required|digits:3',
            'obligado_contabilidad' => 'required|in:SI,NO',
            'sri_environment'       => 'required|in:1,2',
            'logo'                  => 'nullable|image|max:1024',
            'signature_file'        => 'nullable|file|mimes:p12,pfx|max:2048',
        ];

        $this->validate($rules);

        $company = CompanyModel::updateOrCreate(
            ['id' => $this->company_id],
            [
                'name'                  => $this->name,
                'razon_social'          => $this->razon_social,
                'ruc'                   => $this->ruc,
                'address'               => $this->address,
                'establishment_address' => $this->establishment_address ?? $this->address,
                'phone'                 => $this->phone,
                'email'                 => $this->email,
                'status'                => $this->status,
                'estab'                 => $this->estab,
                'pto_emi'               => $this->pto_emi,
                'contribuyente_especial' => $this->contribuyente_especial,
                'obligado_contabilidad' => $this->obligado_contabilidad,
                'contribuyente_rimpe'   => $this->contribuyente_rimpe,
                'sri_environment'       => $this->sri_environment,
            ]
        );

        // Subida de Logo
        if ($this->logo) {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $company->update([
                'logo' => $this->logo->store('logos', 'public')
            ]);
        }

        // Subida de Archivo .p12 (Privado en storage local)
        if ($this->signature_file) {
            if ($company->signature_path && Storage::disk('local')->exists($company->signature_path)) {
                Storage::disk('local')->delete($company->signature_path);
            }
            $path = $this->signature_file->store("signatures/{$company->id}", 'local');
            $company->signature_path = $path;
        }

        // Actualizar contraseña solo si se ingresó una nueva
        if (!empty($this->signature_password)) {
            $company->signature_password = $this->signature_password;
        }

        $company->save();

        $this->has_signature = !empty($company->signature_path);
        $this->signature_password = '';
        $this->signature_file = null;

        $this->dispatch('swal', [
            'message' => $this->company_id ? 'Datos actualizados correctamente' : 'Nueva empresa creada con éxito',
            'type'    => 'success'
        ]);

        if ($user->hasRole('super-admin')) {
            $this->isEditing = false;
            $this->reset([
                'company_id',
                'name',
                'razon_social',
                'ruc',
                'address',
                'establishment_address',
                'phone',
                'email',
                'logo',
                'current_logo',
                'contribuyente_especial',
                'contribuyente_rimpe',
                'signature_file',
                'signature_password'
            ]);
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

    private function isSuperAdmin(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->hasRole('super-admin');
    }
}
