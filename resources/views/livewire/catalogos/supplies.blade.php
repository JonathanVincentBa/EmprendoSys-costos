<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Suministros</flux:heading>
            <flux:subheading>Gestiona consumibles de la empresa</flux:subheading>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus">Nuevo Suministro</flux:button>
    </div>

    <div class="mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Buscar suministro..." class="max-w-md" />
    </div>

    <div class="overflow-hidden border border-zinc-200 dark:border-zinc-700 rounded-lg">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Costo Unitario</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($supplies as $supply)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-zinc-600 dark:text-zinc-400">
                            {{ $supply->code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                            {{ $supply->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                            ${{ number_format($supply->unit_cost, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <flux:button wire:click="edit({{ $supply->id }})" variant="ghost" size="sm" icon="pencil-square" />
                            <flux:button wire:click="delete({{ $supply->id }})" variant="ghost" size="sm" icon="trash" color="red" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-zinc-500">No hay suministros registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $supplies->links() }}
    </div>

    <flux:modal wire:model="isOpen" class="md:w-120">
        <form wire:submit="store" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $supplyId ? 'Editar Suministro' : 'Nuevo Suministro' }}</flux:heading>
                <flux:subheading>Información básica para el costeo.</flux:subheading>
            </div>

            <flux:input label="Código" wire:model="code" />
            <flux:input label="Nombre" wire:model="name" />
            <flux:input label="Costo Unitario" type="number" step="0.01" icon="currency-dollar" wire:model="unit_cost" />

            <div class="flex gap-2 justify-end">
                <flux:button wire:click="$set('isOpen', false)" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary">Guardar</flux:button>
            </div>
        </form>
    </flux:modal>
</div>