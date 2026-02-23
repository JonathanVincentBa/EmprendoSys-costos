<div class="p-6 w-full" x-data="{ modalOpen: false }">
    <h2 class="text-2xl font-bold mb-6 text-zinc-800 dark:text-white">Gestión de Roles y Permisos</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 w-full">

        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 shadow rounded-lg overflow-hidden border border-zinc-200">
            <div class="p-4 border-b flex gap-2">
                <input type="text" wire:model="newPermission" placeholder="Nuevo permiso..."
                    class="border p-2 rounded flex-1">
                <button wire:click="createPermission" class="bg-indigo-600 text-white px-4 py-2 rounded">Crear</button>
            </div>
            <table class="w-full text-left">
                <thead class="bg-zinc-100">
                    <tr>
                        <th class="p-4">Nombre del Permiso</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($allPermissions as $p)
                        <tr>
                            <td class="p-4">
                                @if ($editingPermissionId === $p->id)
                                    <input wire:model="editPermissionName" class="border p-1 w-full">
                                @else
                                    {{ $p->name }}
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                @if ($editingPermissionId === $p->id)
                                    <button wire:click="updatePermissionName"
                                        class="text-green-600 font-bold">Guardar</button>
                                @else
                                    <button wire:click="editPermission({{ $p->id }})"
                                        class="text-blue-500 mr-3">Editar</button>
                                    <button onclick="confirmDelete({{ $p->id }})"
                                        class="text-red-500">Eliminar</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-zinc-50 p-4 rounded-lg border">
            <h3 class="font-bold mb-4">Roles Disponibles</h3>
            @foreach ($roles as $role)
                <button wire:click="selectRole({{ $role->id }})" @click="modalOpen = true"
                    class="w-full p-4 mb-3 bg-white shadow-sm border rounded hover:border-indigo-500 transition text-left cursor-pointer">
                    {{ $role->name }}
                </button>
            @endforeach
        </div>
    </div>

    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
        style="display: none;">
        <div @click.away="modalOpen = false" class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-2xl">
            <h3 class="text-xl font-bold mb-6">Permisos de: {{ $selectedRoleName }}</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-96 overflow-y-auto">
                @foreach ($allPermissions as $permission)
                    <label class="flex items-center space-x-2 p-2 border rounded hover:bg-zinc-50 cursor-pointer">
                        <input type="checkbox" wire:model="permissions" value="{{ $permission->name }}">
                        <span class="text-xs">{{ $permission->name }}</span>
                    </label>
                @endforeach
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button @click="modalOpen = false" class="px-4 py-2 text-zinc-500">Cerrar</button>
                <button wire:click="updatePermissions" @click="modalOpen = false"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción es irreversible.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('deletePermission', id);
            }
        })
    }
</script>
