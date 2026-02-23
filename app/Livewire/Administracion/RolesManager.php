<?php

namespace App\Livewire\Administracion;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesManager extends Component
{
    public $roles;
    public $allPermissions;
    public $selectedRoleName = '';
    public $newPermission = '';
    public $roleId = null;
    public $permissions = [];

    public $editingPermissionId = null;
    public $editPermissionName = '';

    public function mount()
    {
        $this->allPermissions = Permission::all();
        $this->roles = Role::all();
    }

    public function createPermission()
    {
        $this->validate(['newPermission' => 'required|unique:permissions,name']);
        Permission::create(['name' => $this->newPermission]);
        $this->reset('newPermission');
        $this->allPermissions = Permission::all();
    }

    public function deletePermission($id)
    {
        Permission::findById($id)->delete();
        $this->allPermissions = Permission::all();
    }

    public function editPermission($id)
    {
        $permission = Permission::findById($id);
        $this->editingPermissionId = $id;
        $this->editPermissionName = $permission->name;
    }

    public function updatePermissionName()
    {
        $permission = Permission::findById($this->editingPermissionId);
        $permission->update(['name' => $this->editPermissionName]);
        $this->editingPermissionId = null;
        $this->allPermissions = Permission::all();
    }

    public function selectRole($id)
    {
        $this->roleId = $id;
        $role = Role::findById($id);
        $this->selectedRoleName = $role->name;
        $this->permissions = $role->permissions->pluck('name')->toArray();
    }

    public function updatePermissions()
    {
        if ($this->roleId) {
            $role = Role::findById($this->roleId);
            $role->syncPermissions($this->permissions);
            
            $this->dispatch('swal', [
                'type' => 'success',
                'message' => 'Permisos actualizados con éxito.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.administracion.roles-manager');
    }
}
