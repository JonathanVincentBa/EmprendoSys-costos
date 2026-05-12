<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Suministros</flux:heading>
            <flux:subheading>Gestiona consumibles y gastos generales de producción</flux:subheading>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus">Nuevo Suministro</flux:button>
    </div>

    <div class="space-y-4">
        <div class="flex items-center gap-4">
            <flux:input wire:model.live="search" icon="magnifying-glass" 
                placeholder="Buscar por nombre o código..." class="max-w-md" />
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Código</flux:table.column>
                <flux:table.column>Nombre</flux:table.column>
                <flux:table.column>Costo Unitario</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($supplies as $item)
                    <flux:table.row :key="$item->id">
                        <flux:table.cell class="font-medium">{{ $item->code }}</flux:table.cell>
                        <flux:table.cell>{{ $item->name }}</flux:table.cell>
                        <flux:table.cell>${{ number_format($item->unit_cost, 4) }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button wire:click="edit({{ $item->id }})" variant="ghost" icon="pencil-square" size="sm" />
                                <flux:button x-on:click="confirm('¿Eliminar suministro?') && $wire.delete({{ $item->id }})" 
                                    variant="ghost" icon="trash" size="sm" color="red" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center py-8 text-gray-400">
                            No se encontraron suministros registrados.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $supplies->links() }}
        </div>
    </div>

    {{-- Modal de Formulario --}}
    <flux:modal wire:model="isOpen" class="md:w-120">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $supplyId ? 'Editar Suministro' : 'Nuevo Suministro' }}</flux:heading>
                <flux:subheading>Ingrese los detalles del consumible.</flux:subheading>
            </div>

            <form wire:submit.prevent="store" class="space-y-4">
                <flux:input label="Código" wire:model="code" placeholder="Ej: SUM-001" />
                <flux:input label="Nombre" wire:model="name" placeholder="Ej: Jabón Industrial" />
                <flux:input label="Costo Unitario" type="number" step="0.0001" icon="currency-dollar" wire:model="unit_cost" />

                <div class="flex gap-2 justify-end mt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancelar</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">Guardar Cambios</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>