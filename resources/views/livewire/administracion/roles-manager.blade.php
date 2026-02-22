<div class="p-6">
    <h2 class="text-xl font-bold mb-4">Gestión de Roles y Permisos</h2>

    <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
        <h3 class="font-bold text-sm text-gray-700 mb-2">Crear nuevo permiso</h3>
        <div class="flex gap-2">
            <input type="text" wire:model="newPermission" placeholder="Ej: realizar ventas" class="border p-2 rounded flex-1">
            <button wire:click="createPermission" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                + Crear Permiso
            </button>
        </div>
        @error('newPermission') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div class="grid grid-cols-2 gap-6">
        <div>
            <h3 class="font-bold mb-2">Selecciona un Rol</h3>
            @foreach($roles as $role)
                <button wire:click="selectRole({{ $role->id }})" class="block w-full text-left p-2 border-b {{ $roleId == $role->id ? 'bg-blue-100' : '' }}">
                    {{ $role->name }}
                </button>
            @endforeach
        </div>

        @if($roleId)
            <div>
                <h3 class="font-bold mb-2">Permisos para {{ Role::findById($roleId)->name }}</h3>
                @foreach($allPermissions as $permission)
                    <label class="block p-1 hover:bg-gray-100 rounded">
                        <input type="checkbox" wire:model="permissions" value="{{ $permission->name }}">
                        {{ $permission->name }}
                    </label>
                @endforeach
                <button wire:click="updatePermissions" class="mt-4 bg-green-600 text-white px-4 py-2 rounded">
                    Guardar Cambios
                </button>
            </div>
        @endif
    </div>
</div>