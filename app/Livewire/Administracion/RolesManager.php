<?php

namespace App\Livewire\Administracion;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesManager extends Component
{
    public $newPermission;
    public $roleId;
    public $permissions = []; 
    public $allPermissions;

    public function mount()
    {
        $this->allPermissions = Permission::all();
    }

    public function createPermission()
    {
        $this->validate(['newPermission' => 'required|unique:permissions,name']);
        Permission::create(['name' => $this->newPermission]);
        $this->reset('newPermission');
        $this->allPermissions = Permission::all();
    }

    public function selectRole($id)
    {
        $this->roleId = $id;
        $role = Role::findById($id);
        $this->permissions = $role->permissions->pluck('name')->toArray();
    }

    public function updatePermissions()
    {
        $role = Role::findById($this->roleId);
        $role->syncPermissions($this->permissions);
        session()->flash('message', 'Permisos actualizados con éxito.');
    }

    public function render()
    {
        return view('livewire.administracion.roles-manager', [
            'roles' => Role::all()
        ]);
    }
}