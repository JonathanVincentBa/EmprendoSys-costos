<div class="p-6">
    {{-- Cabecera --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Productos Finales</flux:heading>
            <flux:subheading>Define tus productos, gestiona su stock elaborado y sus fórmulas de costos.</flux:subheading>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus">Nuevo Producto</flux:button>
    </div>

    {{-- Card de Contenido --}}
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm p-4">

        {{-- Buscador --}}
        <div class="flex items-center justify-between mb-4">
            <flux:input wire:model.live="search" placeholder="Buscar producto..." class="max-w-xs" icon="magnifying-glass" />
        </div>

        {{-- Tabla de Productos --}}
        <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 uppercase tracking-wider">Presentación</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 uppercase tracking-wider">Stock Elaborado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-800">
                    @forelse ($products as $product)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $product->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-center text-zinc-600 dark:text-zinc-400">
                                {{ $product->presentation_ml }} ml/gr
                            </td>
                            <td class="px-6 py-4 text-center capitalize text-zinc-600 dark:text-zinc-400">
                                {{ $product->packaging_type }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300">
                                    {{ $product->current_stock ?? 0 }} unids
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($product->is_active)
                                    <flux:badge color="green">Activo</flux:badge>
                                @else
                                    <flux:badge color="zinc">Inactivo</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-1">
                                <flux:button wire:click="openStockModal({{ $product->id }})" variant="subtle" icon="archive-box-arrow-down" title="Ingresar Producción" />
                                <flux:button wire:click="edit({{ $product->id }})" variant="ghost" icon="pencil-square" />
                                <flux:button href="{{ route('products.recipe', $product->id) }}" variant="ghost" icon="beaker" />
                                <flux:button wire:click="delete({{ $product->id }})" variant="ghost" icon="trash" color="red" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-zinc-500">No hay productos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>

    {{-- MODAL DE CREACIÓN / EDICIÓN --}}
    <flux:modal wire:model="isOpen" class="md:w-120">
        <form wire:submit="store" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $productId ? 'Actualizar Producto' : 'Nuevo Producto' }}</flux:heading>
                <flux:subheading>Define las características generales de tu producto terminado.</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input label="Nombre del Producto" wire:model="name" placeholder="Ej. Salsa de Tomate" />
                <flux:input label="Presentación (ml/gr)" type="number" wire:model="presentation_ml" suffix="ml/gr" />

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tipo de Empaque</label>
                    <select wire:model="packaging_type"
                        class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                        <option value="frasco">Frasco</option>
                        <option value="funda">Funda</option>
                        <option value="galon">Galón</option>
                        <option value="combo">Combo</option>
                        <option value="caja">Caja</option>
                    </select>
                    @error('packaging_type')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center space-x-3 mt-4">
                    <flux:checkbox label="Producto disponible para la venta" wire:model="is_active" />
                </div>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:button wire:click="$set('isOpen', false)" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $productId ? 'Guardar Cambios' : 'Crear Producto' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- MODAL DE INGRESO DE STOCK ELABORADO --}}
    <flux:modal wire:model="isStockModalOpen" class="md:w-100">
        <form wire:submit="addStock" class="space-y-6">
            <div>
                <flux:heading size="lg">Ingresar Stock Elaborado</flux:heading>
                <flux:subheading>Producto: {{ $name }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input label="Cantidad Producida (Unidades)" type="number" wire:model="stockToAdd" placeholder="Ej. 25" min="1" />
            </div>

            <div class="flex gap-2 justify-end">
                <flux:button wire:click="$set('isStockModalOpen', false)" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" class="bg-indigo-600 hover:bg-indigo-700 text-white">
                    Registrar Ingreso
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>