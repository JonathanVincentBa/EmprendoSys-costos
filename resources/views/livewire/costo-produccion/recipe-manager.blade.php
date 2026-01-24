<div class="p-6">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <flux:heading size="xl">Configuración de Receta: {{ $product->name }}</flux:heading>
            <flux:subheading>Define insumos, tiempos de proceso y empaques para el cálculo de costos.</flux:subheading>
        </div>
        <flux:button href="{{ route('products.index') }}" variant="ghost" icon="arrow-left">Volver</flux:button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <flux:heading size="lg" class="mb-4">1. Definición del Lote (Batch)</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                    <flux:input label="Tamaño del Lote (ml)" type="number" wire:model="batch_size_ml" suffix="ml" />
                    <flux:button wire:click="saveBaseRecipe" variant="primary">Actualizar Base</flux:button>
                </div>
            </div>

            @if($recipe->exists)
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <flux:heading size="lg" class="mb-4">2. Materias Primas / Ingredientes</flux:heading>
                <div class="flex gap-4 mb-6 items-end">
                    <div class="flex-1">
                        <flux:select label="Insumo" wire:model="selected_material_id" placeholder="Seleccione material">
                            @foreach($materials as $m)
                                <flux:select.option value="{{ $m->id }}">{{ $m->name }} (${{ $m->unit_cost }}/kg)</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <flux:input label="Cantidad (kg)" type="number" step="0.0001" wire:model="quantity_kg" class="w-32" />
                    <flux:button wire:click="addIngredient" variant="filled" icon="plus" />
                </div>

                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead>
                        <tr class="text-left text-xs font-medium text-zinc-500 uppercase">
                            <th class="py-2">Ingrediente</th>
                            <th class="py-2 text-right">Cantidad</th>
                            <th class="py-2 text-right">Costo Subtotal</th>
                            <th class="py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @php $totalInsumos = 0; @endphp
                        @foreach($recipe->items as $item)
                        @php $sub = $item->quantity_kg * $item->rawMaterial->unit_cost; $totalInsumos += $sub; @endphp
                        <tr class="text-sm">
                            <td class="py-3 font-medium">{{ $item->rawMaterial->name }}</td>
                            <td class="py-3 text-right">{{ number_format($item->quantity_kg, 4) }} kg</td>
                            <td class="py-3 text-right">${{ number_format($sub, 2) }}</td>
                            <td class="py-3 text-right">
                                <flux:button wire:click="removeIngredient({{ $item->id }})" variant="ghost" size="sm" icon="trash" color="red" />
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-zinc-900 text-white p-6 rounded-xl shadow-lg border border-zinc-800">
                <flux:heading size="lg" class="text-white mb-4">Resumen de Costo</flux:heading>
                
                <div class="space-y-3">
                    <div class="flex justify-between text-zinc-400">
                        <span>Materia Prima (Lote):</span>
                        <span class="text-white font-mono">${{ number_format($totalInsumos ?? 0, 2) }}</span>
                    </div>
                    <div class="border-t border-zinc-800 my-2"></div>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Costo Total Lote:</span>
                        <span class="text-emerald-400">${{ number_format($totalInsumos ?? 0, 2) }}</span>
                    </div>
                    
                    @if($batch_size_ml > 0 && ($product->presentation_ml ?? 0) > 0)
                    @php 
                        $unidadesPorLote = $batch_size_ml / $product->presentation_ml;
                        $costoUnitario = ($totalInsumos ?? 0) / $unidadesPorLote;
                    @endphp
                    <div class="bg-zinc-800 p-4 rounded-lg mt-4">
                        <p class="text-xs text-zinc-500 uppercase">Costo Unitario (Materiales)</p>
                        <p class="text-2xl font-black text-white">${{ number_format($costoUnitario, 4) }}</p>
                        <p class="text-xs text-zinc-400">Rinde: {{ number_format($unidadesPorLote, 1) }} unidades</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>