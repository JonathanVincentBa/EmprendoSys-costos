<div class="p-6">
    <div class="mb-6">
        <flux:heading size="xl">Configuración de Costos e Impuestos</flux:heading>
        <flux:subheading>Define los porcentajes de gastos indirectos y márgenes de utilidad.</flux:subheading>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <flux:heading size="lg">Gastos Indirectos (CIF)</flux:heading>
                <flux:button wire:click="create(false)" variant="filled" size="sm" icon="plus" />
            </div>
            
            <div class="space-y-3">
                @forelse($indirects as $item)
                    <div class="flex justify-between items-center p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg border border-zinc-100 dark:border-zinc-700">
                        <div>
                            <p class="font-medium text-zinc-900 dark:text-white">{{ $item->name }}</p>
                            <p class="text-xs text-zinc-500">{{ number_format($item->percentage, 2) }}% sobre el costo directo</p>
                        </div>
                        <div class="flex gap-2">
                            <flux:button wire:click="edit({{ $item->id }})" variant="ghost" size="sm" icon="pencil-square" />
                            <flux:button wire:click="delete({{ $item->id }})" variant="ghost" size="sm" icon="trash" color="red" />
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-zinc-500 text-center py-4">No hay gastos indirectos definidos.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <flux:heading size="lg">Márgenes de Utilidad</flux:heading>
                <flux:button wire:click="create(true)" variant="filled" size="sm" icon="plus" color="emerald" />
            </div>

            <div class="space-y-3">
                @forelse($margins as $item)
                    <div class="flex justify-between items-center p-3 bg-emerald-50/50 dark:bg-emerald-950/20 rounded-lg border border-emerald-100 dark:border-emerald-900/50">
                        <div>
                            <p class="font-medium text-emerald-900 dark:text-emerald-400">{{ $item->name }}</p>
                            <p class="text-xs text-emerald-600 dark:text-emerald-500">{{ number_format($item->percentage, 2) }}% de ganancia esperada</p>
                        </div>
                        <div class="flex gap-2">
                            <flux:button wire:click="edit({{ $item->id }})" variant="ghost" size="sm" icon="pencil-square" />
                            <flux:button wire:click="delete({{ $item->id }})" variant="ghost" size="sm" icon="trash" color="red" />
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-zinc-500 text-center py-4">No hay márgenes definidos.</p>
                @endforelse
            </div>
        </div>
    </div>

    <flux:modal wire:model="isOpen" class="md:w-[25rem]">
        <form wire:submit="store" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $is_profit_margin ? 'Margen de Utilidad' : 'Gasto Indirecto' }}</flux:heading>
                <flux:subheading>Define el nombre y el porcentaje correspondiente.</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input label="Descripción / Nombre" wire:model="name" placeholder="Ej: Electricidad o Margen Minorista" />
                <flux:input label="Porcentaje" type="number" step="0.01" wire:model="percentage" suffix="%" />
            </div>

            <div class="flex gap-2 justify-end">
                <flux:modal.close><flux:button variant="ghost">Cancelar</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">Guardar Configuración</flux:button>
            </div>
        </form>
    </flux:modal>
</div>