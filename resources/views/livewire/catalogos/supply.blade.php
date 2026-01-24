<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Suministros</flux:heading>
            <flux:subheading>Gestiona los consumibles y gastos generales</flux:subheading>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">Nuevo Suministro</flux:button>
    </div>

    <div class="space-y-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Buscar por nombre o código..."
            class="max-w-md" />

        @if (session()->has('message'))
            <flux:badge variant="success" size="lg" class="w-full justify-start">{{ session('message') }}
            </flux:badge>
        @endif

        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <flux:heading size="xl">Suministros</flux:heading>
                    <flux:subheading>Gestiona los consumibles y gastos generales</flux:subheading>
                </div>

                <flux:button wire:click="create" variant="primary" icon="plus">Nuevo Suministro</flux:button>
            </div>

            <div class="space-y-4">
                <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Buscar por nombre o código..."
                    class="max-w-md" />

                @if (session()->has('message'))
                    <flux:badge variant="success" size="lg" class="w-full justify-start">{{ session('message') }}
                    </flux:badge>
                @endif

                {{-- TABLA ESTÁNDAR COMPATIBLE CON FLUX FREE --}}
                <div
                    class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
                                <th class="py-3 px-4 text-xs font-semibold uppercase text-zinc-500">Código</th>
                                <th class="py-3 px-4 text-xs font-semibold uppercase text-zinc-500">Suministro</th>
                                <th class="py-3 px-4 text-xs font-semibold uppercase text-zinc-500">Costo Unitario</th>
                                <th class="py-3 px-4 text-xs font-semibold uppercase text-zinc-500 text-right">Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse ($supplies as $supply)
                                <tr wire:key="{{ $supply->id }}"
                                    class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="py-3 px-4 font-mono text-xs text-zinc-600 dark:text-zinc-400">
                                        {{ $supply->code }}</td>
                                    <td class="py-3 px-4 text-sm font-medium">{{ $supply->name }}</td>
                                    <td class="py-3 px-4 text-sm">${{ number_format($supply->unit_cost, 2) }}</td>
                                    <td class="py-3 px-4 text-right space-x-2">
                                        <flux:button wire:click="edit({{ $supply->id }})" variant="ghost"
                                            size="sm" icon="pencil-square" />
                                        <flux:button wire:click="delete({{ $supply->id }})" variant="ghost"
                                            size="sm" icon="trash" color="red" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-10 text-zinc-400">No se encontraron
                                        suministros.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación estándar de Laravel --}}
                <div class="mt-4">
                    {{ $supplies->links() }}
                </div>
            </div>

            <flux:modal wire:model="isOpen" class="md:w-120">
                {{-- ... El resto de tu modal de guardado se mantiene igual ... --}}
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ $supplyId ? 'Editar Suministro' : 'Nuevo Suministro' }}
                        </flux:heading>
                        <flux:subheading>Complete la información del consumible.</flux:subheading>
                    </div>

                    <form wire:submit.prevent="store" class="space-y-4">
                        <flux:input label="Código de Referencia" wire:model="code" placeholder="Ej: SUM-001" />
                        <flux:input label="Nombre del Suministro" wire:model="name"
                            placeholder="Ej: Jabón Industrial" />
                        <flux:input label="Costo Unitario" type="number" step="0.01" icon="currency-dollar"
                            wire:model="unit_cost" />

                        <div class="flex gap-2 justify-end mt-4">
                            <flux:modal.close>
                                <flux:button variant="ghost">Cancelar</flux:button>
                            </flux:modal.close>
                            <flux:button type="submit" variant="primary">Guardar Suministro</flux:button>
                        </div>
                    </form>
                </div>
            </flux:modal>
        </div>
    </div>

    <flux:modal wire:model="isOpen" class="md:w-120">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $supplyId ? 'Editar Suministro' : 'Nuevo Suministro' }}</flux:heading>
                <flux:subheading>Complete la información del consumible.</flux:subheading>
            </div>

            <form wire:submit.prevent="store" class="space-y-4">
                <flux:input label="Código de Referencia" wire:model="code" placeholder="Ej: SUM-001" />
                <flux:input label="Nombre del Suministro" wire:model="name" placeholder="Ej: Jabón Industrial" />
                <flux:input label="Costo Unitario" type="number" step="0.01" icon="currency-dollar"
                    wire:model="unit_cost" />

                <div class="flex gap-2 justify-end mt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancelar</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">Guardar Suministro</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
