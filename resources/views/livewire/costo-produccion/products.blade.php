<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Productos Finales</flux:heading>
            <flux:subheading>Define tus productos para luego configurar sus fórmulas y costos.</flux:subheading>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus">Nuevo Producto</flux:button>
    </div>

    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm p-4">
        <flux:input wire:model.live="search" placeholder="Buscar producto..." class="mb-4 max-w-xs"
            icon="magnifying-glass" />

        <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 uppercase">Presentación</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($products as $product)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="px-6 py-4 text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $product->name }}
                                <span
                                    class="block text-xs text-zinc-500 font-normal">{{ $product->packaging_type }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-center text-zinc-600 dark:text-zinc-400">
                                {{ $product->presentation_ml }} ml
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:badge size="sm" color="{{ $product->is_active ? 'emerald' : 'zinc' }}"
                                    inset="top bottom">
                                    {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <div class="flex justify-end gap-2">
                                    <flux:button href="{{ route('products.recipe', $product->id) }}" variant="filled"
                                        size="sm" icon="beaker" title="Configurar Receta" color="indigo" />

                                    <flux:button wire:click="edit({{ $product->id }})" variant="ghost" size="sm"
                                        icon="pencil-square" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-zinc-500">No hay productos
                                registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $products->links() }}</div>
    </div>

    <flux:modal wire:model="isOpen" class="md:w-100">
        <form wire:submit="store" class="space-y-6">
            <flux:heading size="lg">{{ $productId ? 'Editar Producto' : 'Nuevo Producto' }}</flux:heading>

            <div class="space-y-4">
                <flux:input label="Nombre del Producto" wire:model="name" placeholder="Ej: Salsa de Tomate Picante" />
                <flux:input label="Presentación (ml/gr)" type="number" wire:model="presentation_ml" suffix="ml" />
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Tipo de Empaque</label>
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

                <flux:checkbox label="Producto Activo" wire:model="is_active" />
            </div>

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Guardar Producto</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
