<?php

namespace App\Livewire\Administracion;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesManager extends Component
{
    public $selectedRoleName = '';
    public $newPermission = '';
    public $roleId = null;
    public $permissions = [];

    public $editingPermissionId = null;
    public $editPermissionName = '';

    public function createPermission()
    {
        $this->validate(['newPermission' => 'required|unique:permissions,name']);
        Permission::create(['name' => $this->newPermission]);
        $this->reset('newPermission');
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function deletePermission($id)
    {
        Permission::findById($id)->delete();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
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

            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            $this->dispatch('swal', [
                'type' => 'success',
                'message' => 'Permisos actualizados con éxito.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.administracion.roles-manager', [
            'roles' => Role::all(),
            'allPermissions' => Permission::all(),
        ]);
    }
}
