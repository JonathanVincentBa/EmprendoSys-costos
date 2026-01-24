<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Materiales de Empaque</flux:heading>
            <flux:subheading>Gestiona botellas, etiquetas, cajas y otros embalajes.</flux:subheading>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus">Nuevo Empaque</flux:button>
    </div>

    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm p-4 space-y-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Buscar empaque..." class="max-w-md" />

        <div class="overflow-hidden border border-zinc-200 dark:border-zinc-700 rounded-lg">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider">Costo Unitario</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($packagings as $item)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-zinc-600 dark:text-zinc-400">
                                {{ $item->code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $item->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-zinc-900 dark:text-white">
                                ${{ number_format($item->unit_cost, 4) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <flux:button wire:click="edit({{ $item->id }})" variant="ghost" size="sm" icon="pencil-square" />
                                    <flux:button 
                                        x-on:click="
                                            Swal.fire({
                                                title: '¿Eliminar empaque?',
                                                text: 'Esta acción no se puede deshacer',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonText: 'Sí, eliminar',
                                                cancelButtonText: 'Cancelar'
                                            }).then((result) => {
                                                if (result.isConfirmed) { $wire.delete({{ $item->id }}) }
                                            })
                                        " 
                                        variant="ghost" size="sm" icon="trash" color="red" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-zinc-500">No hay empaques registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $packagings->links() }}</div>
    </div>

    <flux:modal wire:model="isOpen" class="md:w-[25rem]">
        <form wire:submit="store" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $packagingId ? 'Editar Empaque' : 'Nuevo Empaque' }}</flux:heading>
                <flux:subheading>Ingresa los datos del material de embalaje.</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input label="Código" wire:model="code" />
                <flux:input label="Nombre del Empaque" wire:model="name" />
                <flux:input label="Costo Unitario" type="number" step="0.0001" icon="currency-dollar" wire:model="unit_cost" />
            </div>

            <div class="flex gap-2 justify-end">
                <flux:modal.close><flux:button variant="ghost">Cancelar</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">Guardar</flux:button>
            </div>
        </form>
    </flux:modal>
</div>