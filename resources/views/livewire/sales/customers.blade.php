<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Clientes</flux:heading>
            <flux:subheading>Gestiona los clientes de la empresa</flux:subheading>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus">Nuevo Cliente</flux:button>
    </div>

    <div class="mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Buscar cliente..." class="max-w-md" />
    </div>

    <div class="overflow-hidden border border-zinc-200 dark:border-zinc-700 rounded-lg">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Identificación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Nombre / Razón Social</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Teléfono / Email</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($customers as $customer)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-zinc-600 dark:text-zinc-400">
                            {{ $customer->identification }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                            {{ $customer->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">
                            <div class="flex flex-col">
                                <span>{{ $customer->phone ?? 'S/N' }}</span>
                                <span class="text-xs opacity-70">{{ $customer->email ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <flux:button wire:click="edit({{ $customer->id }})" variant="ghost" size="sm" icon="pencil-square" />
                                <flux:button wire:click="delete({{ $customer->id }})" wire:confirm="¿Estás seguro de eliminar este cliente?" variant="ghost" size="sm" icon="trash" color="red" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-zinc-500 italic">No hay clientes registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $customers->links() }}
    </div>

    <flux:modal wire:model="isModalOpen" class="md:w-120">
        <form wire:submit="store" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $customer_id ? 'Editar Cliente' : 'Nuevo Cliente' }}</flux:heading>
                <flux:subheading>Información básica del cliente para facturación.</flux:subheading>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <flux:input label="Identificación (Cédula/RUC)" wire:model="identification" />
                <flux:input label="Nombre Completo" wire:model="name" />
                
                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="Teléfono" wire:model="phone" />
                    <flux:input label="Email" type="email" wire:model="email" />
                </div>

                <flux:textarea label="Dirección" wire:model="address" rows="2" />
            </div>

            <div class="flex gap-2 justify-end">
                <flux:button wire:click="closeModal" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary">Guardar Cliente</flux:button>
            </div>
        </form>
    </flux:modal>
</div>