<div class="max-w-4xl mx-auto p-6 bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700">
    <div class="flex items-center justify-between mb-10">
        @foreach(['General', 'Ingredientes', 'Mano de Obra', 'Resumen'] as $i => $label)
            <div class="flex flex-col items-center flex-1">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm {{ $step >= $i+1 ? 'bg-indigo-600 text-white' : 'bg-zinc-200 text-zinc-500' }}">
                    {{ $i+1 }}
                </div>
                <span class="text-xs mt-2 font-medium {{ $step == $i+1 ? 'text-indigo-600' : 'text-zinc-500' }}">{{ $label }}</span>
            </div>
            @if($i < 3) <div class="h-px bg-zinc-200 flex-1 mb-6"></div> @endif
        @endforeach
    </div>

    <div class="min-h-87.5">
        {{-- PASO 1: GENERAL --}}
        @if($step == 1)
            <div class="space-y-6">
                <flux:input wire:model="name" label="Nombre del Producto" placeholder="Ej. Salsa Picante" />
                
                <flux:select wire:model="packaging_material_id" label="Material de Empaque">
                    <option value="">Seleccione un empaque...</option>
                    @foreach($all_packaging as $pack)
                        <option value="{{ $pack->id }}">{{ $pack->name }}</option>
                    @endforeach
                </flux:select>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="presentation_ml" label="Contenido por unidad (ml/gr)" type="number" />
                    <flux:input wire:model="batch_size_ml" label="Tamaño del Lote Total (ml/gr)" type="number" />
                </div>
            </div>
        @endif

        {{-- PASO 2: INGREDIENTES --}}
        @if($step == 2)
            <div class="space-y-4">
                <div class="flex gap-4 items-end bg-zinc-50 p-4 rounded-lg">
                    <flux:select wire:model="selected_material" label="Ingrediente" class="flex-1">
                        <option value="">Seleccione...</option>
                        @foreach($all_materials as $mat)
                            <option value="{{ $mat->id }}">{{ $mat->name }} (${{ $mat->unit_cost }}/kg)</option>
                        @endforeach
                    </flux:select>
                    <flux:input wire:model="quantity_kg" label="Cantidad (Kg)" type="number" step="0.0001" class="w-32" />
                    <flux:button wire:click="addIngredient" variant="filled">Añadir</flux:button>
                </div>

                <table class="w-full text-left">
                    <thead><tr class="text-zinc-400 text-xs uppercase border-b">
                        <th class="py-2">Material</th><th class="py-2 text-right">Subtotal</th>
                    </tr></thead>
                    <tbody>
                        @foreach($ingredients as $ing)
                            <tr class="border-b border-zinc-100">
                                <td class="py-2 text-sm">{{ $ing['name'] }}</td>
                                <td class="py-2 text-sm text-right font-bold">${{ number_format($ing['subtotal'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- PASO 3: MANO DE OBRA --}}
        @if($step == 3)
            <div class="space-y-4">
                <div class="flex gap-4 items-end bg-zinc-50 p-4 rounded-lg">
                    <flux:select wire:model="process_id" label="Proceso / Mano de Obra" class="flex-1">
                        <option value="">Seleccione proceso...</option>
                        @foreach($all_processes as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->hours_per_batch }} hrs)</option>
                        @endforeach
                    </flux:select>
                    <flux:button wire:click="addProcess">Añadir</flux:button>
                </div>

                <div class="grid gap-2">
                    @foreach($selected_processes as $index => $sp)
                        <div class="flex justify-between p-3 bg-zinc-50 rounded border border-zinc-200 items-center">
                            <div><p class="font-bold text-sm">{{ $sp['name'] }}</p><p class="text-xs text-zinc-500">Costo: ${{ $sp['cost'] }}</p></div>
                            <div class="flex items-center gap-2">
                                <input type="number" wire:model.live="selected_processes.{{ $index }}.hours" class="w-16 p-1 text-center border rounded">
                                <span class="text-sm font-bold">${{ number_format($sp['cost'] * $sp['hours'], 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- PASO 4: RESUMEN --}}
        @if($step == 4)
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-zinc-50 p-6 rounded-xl border border-zinc-200">
                    <h3 class="font-bold mb-4 border-b pb-2">Desglose de Costos</h3>
                    <div class="flex justify-between mb-2"><span>Materia Prima:</span><strong>${{ number_format($res['materials'], 2) }}</strong></div>
                    <div class="flex justify-between mb-2 border-b pb-2"><span>Mano de Obra:</span><strong>${{ number_format($res['labor'], 2) }}</strong></div>
                    <div class="flex justify-between text-lg font-bold"><span>COSTO TOTAL:</span><span>${{ number_format($res['total'], 2) }}</span></div>
                </div>

                <div class="bg-green-600 p-6 rounded-xl text-white text-center shadow-lg">
                    <p class="text-xs uppercase font-bold opacity-80">Precio de Venta Sugerido (Ganancia {{ $margin }}%)</p>
                    <p class="text-5xl font-black mt-2">${{ number_format($res['suggested'], 2) }}</p>
                    <div class="mt-4 flex items-center justify-center gap-2">
                        <span>Ajustar Margen:</span>
                        <input type="number" wire:model.live="margin" class="w-16 bg-white/20 rounded border-none text-center font-bold"> %
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="flex justify-between mt-10 pt-6 border-t border-zinc-100">
        <flux:button wire:click="prevStep" :disabled="$step == 1">Atrás</flux:button>
        @if($step < 4)
            <flux:button wire:click="nextStep" variant="primary">Siguiente Paso</flux:button>
        @else
            <flux:button wire:click="saveAll" variant="primary" class="bg-green-600 hover:bg-green-700 text-white">Finalizar y Crear Producto</flux:button>
        @endif
    </div>
</div>