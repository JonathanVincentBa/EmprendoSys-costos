<?php

namespace App\Livewire\Administracion;

use App\Models\User;
use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class Users extends Component
{
    use WithPagination;

    // Propiedades de control
    public $search = '';
    public $showModal = false;
    public $userId;

    // Campos del formulario
    public $name, $email, $password, $company_id, $role;
    public $is_active = true;

    // Resetear paginación al buscar
    public function updatingSearch() { $this->resetPage(); }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(User $user)
    {
        $this->resetForm();
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->company_id = $user->company_id;
        $this->is_active = $user->is_active;
        $this->role = $user->roles->first()?->name;
        
        $this->showModal = true;
    }

    public function save()
    {
        $currentUser = Auth::user();

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'role' => 'required',
            'password' => $this->userId ? 'nullable|min:8' : 'required|min:8',
            'company_id' => $currentUser->hasRole('super-admin') ? 'nullable' : 'required',
        ]);

        // Lógica Multi-tenant: Si no es SuperAdmin, forzar SU empresa
        $finalCompanyId = $currentUser->hasRole('super-admin') 
            ? $this->company_id 
            : $currentUser->company_id;

        $user = User::updateOrCreate(['id' => $this->userId], [
            'name' => $this->name,
            'email' => $this->email,
            'company_id' => $finalCompanyId,
            'is_active' => $this->is_active,
            'password' => $this->password ? Hash::make($this->password) : ($this->userId ? User::find($this->userId)->password : Hash::make('12345678')),
        ]);

        $user->syncRoles($this->role);

        $this->showModal = false;
        $this->dispatch('swal', ['type' => 'success', 'message' => $this->userId ? 'Usuario actualizado' : 'Usuario creado']);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Evitar que te bloquees a ti mismo
        if ($user->id === Auth::id()) {
            $this->dispatch('swal', ['type' => 'error', 'message' => 'No puedes bloquear tu propia cuenta']);
            return;
        }

        $user->update(['is_active' => !$user->is_active]);
        $this->dispatch('swal', ['type' => 'info', 'message' => 'Estado actualizado']);
    }

    public function resetForm()
    {
        $this->reset(['userId', 'name', 'email', 'password', 'company_id', 'role', 'is_active']);
    }

    public function render()
    {
        $currentUser = Auth::user();
        
        $query = User::query()->with(['company', 'roles']);

        // FILTRO DE EMPRESA (Multi-tenancy)
        if (!$currentUser->hasRole('super-admin')) {
            $query->where('company_id', $currentUser->company_id);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.administracion.users', [
            'users' => $query->latest()->paginate(10),
            'companies' => Company::all(),
            'roles' => Role::where('name', '!=', 'super-admin')->get()
        ]);
    }
}