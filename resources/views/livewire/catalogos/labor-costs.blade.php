<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Mano de Obra</flux:heading>
            <flux:subheading>Configura los roles de producción y sus costos asociados.</flux:subheading>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus">Nuevo Costo Laboral</flux:button>
    </div>

    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Rol / Cargo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Sueldo Base</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Costo Real Estimado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($roles as $role)
                @php
                    $totalRate = 1 + (($role->iess_rate + $role->decimo_tercero_rate + $role->decimo_cuarto_rate + $role->vacation_rate + $role->fondo_reserva_rate + $role->severance_rate) / 100);
                    $realCost = $role->monthly_salary * $totalRate;
                @endphp
                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                    <td class="px-6 py-4 text-sm font-medium text-zinc-900 dark:text-white">{{ $role->role }}</td>
                    <td class="px-6 py-4 text-sm text-right text-zinc-600 dark:text-zinc-400">${{ number_format($role->monthly_salary, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-right font-bold text-zinc-900 dark:text-white">${{ number_format($realCost, 2) }}</td>
                    <td class="px-6 py-4 text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            <flux:button wire:click="edit({{ $role->id }})" variant="ghost" size="sm" icon="pencil-square" />
                            <flux:button wire:click="delete({{ $role->id }})" variant="ghost" size="sm" icon="trash" color="red" />
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">{{ $roles->links() }}</div>
    </div>

    <flux:modal wire:model="isOpen" class="md:w-140">
        <form wire:submit="store" class="space-y-6">
            <flux:heading size="lg">{{ $laborId ? 'Editar Rol' : 'Nuevo Rol de Producción' }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <flux:input label="Nombre del Rol (Ej: Maestro Panadero)" wire:model="role" />
                </div>
                <flux:input label="Sueldo Mensual Base" type="number" step="0.01" wire:model="monthly_salary" />
                <flux:input label="% IESS Patrón" type="number" step="0.01" wire:model="iess_rate" />
                <flux:input label="% Décimo Tercero" type="number" step="0.01" wire:model="decimo_tercero_rate" />
                <flux:input label="% Décimo Cuarto" type="number" step="0.01" wire:model="decimo_cuarto_rate" />
                <flux:input label="% Vacaciones" type="number" step="0.01" wire:model="vacation_rate" />
                <flux:input label="% Fondo Reserva" type="number" step="0.01" wire:model="fondo_reserva_rate" />
            </div>

            <div class="flex gap-2 justify-end">
                <flux:modal.close><flux:button variant="ghost">Cancelar</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">Guardar</flux:button>
            </div>
        </form>
    </flux:modal>
</div>