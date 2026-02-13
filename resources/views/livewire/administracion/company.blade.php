<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <flux:heading size="xl">
                {{ auth()->user()->hasRole('super-admin') ? ($isEditing ? 'Configuración de Empresa' : 'Gestión de Clientes') : 'Mi Empresa' }}
            </flux:heading>
            <flux:subheading>Administra la información legal y el estado de los negocios vinculados.</flux:subheading>
        </div>

        @if (auth()->user()->hasRole('super-admin'))
            @if ($isEditing)
                <flux:button wire:click="$set('isEditing', false)" variant="subtle" icon="arrow-left">Volver al listado
                </flux:button>
            @else
                <flux:button wire:click="createCompany" variant="primary" icon="plus">Nuevo Cliente</flux:button>
            @endif
        @endif
    </div>

    @if (auth()->user()->hasRole('super-admin') && !$isEditing)
        {{-- TABLA DE GESTIÓN PARA SUPER-ADMIN --}}
        <div
            class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Empresa</flux:table.column>
                    <flux:table.column>RUC / Email</flux:table.column>
                    <flux:table.column>Estado</flux:table.column>
                    <flux:table.column>Acciones</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($companies as $item)
                        <flux:table.row :key="$item->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    @if ($item->logo)
                                        <img src="{{ Storage::url($item->logo) }}"
                                            class="w-8 h-8 rounded-lg object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center">
                                            <flux:icon name="building-office" variant="micro" />
                                        </div>
                                    @endif
                                    <span class="font-medium text-zinc-800 dark:text-white">{{ $item->name }}</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="text-sm">{{ $item->ruc }}</div>
                                <div class="text-xs text-zinc-500">{{ $item->email }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="{{ $item->status === 'active' ? 'green' : 'red' }}" size="sm"
                                    inset>
                                    {{ $item->status === 'active' ? 'Activo' : 'Suspendido' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button wire:click="editCompany({{ $item->id }})" variant="subtle"
                                        icon="pencil-square" size="sm" />

                                    <flux:button wire:click="toggleStatus({{ $item->id }})" variant="subtle"
                                        icon="{{ $item->status === 'active' ? 'pause-circle' : 'play-circle' }}"
                                        size="sm"
                                        title="{{ $item->status === 'active' ? 'Suspender' : 'Activar' }}" />

                                    <flux:button
                                        wire:confirm="¿Estás seguro de eliminar esta empresa? Esta acción no se puede deshacer."
                                        wire:click="deleteCompany({{ $item->id }})" variant="subtle" color="red"
                                        icon="trash" size="sm" />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $companies->links() }}
            </div>
        </div>
    @else
        {{-- FORMULARIO DE EDICIÓN / CREACIÓN --}}
        <div class="max-w-4xl mx-auto">
            <div
                class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-sm overflow-hidden">
                <form wire:submit="save" class="p-6 space-y-6">
                    {{-- Sección Logo y Estado --}}
                    <div
                        class="flex flex-col md:flex-row gap-6 items-start md:items-center pb-6 border-b border-zinc-200 dark:border-zinc-700">
                        <div class="relative group">
                            <div
                                class="w-32 h-32 rounded-2xl border-2 border-dashed border-zinc-300 dark:border-zinc-600 flex items-center justify-center overflow-hidden bg-zinc-50 dark:bg-zinc-800">
                                @if ($logo)
                                    <img src="{{ $logo->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif($current_logo)
                                    <img src="{{ Storage::url($current_logo) }}" class="w-full h-full object-cover">
                                @else
                                    <flux:icon name="photo" class="text-zinc-400" />
                                @endif
                            </div>
                            <input type="file" wire:model="logo" class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>

                        <div class="flex-1 space-y-2">
                            <flux:heading>Logo Institucional</flux:heading>
                            <p class="text-xs text-zinc-500 mb-4">PNG o JPG cuadrado (Máx. 1MB)</p>

                            @if (auth()->user()->hasRole('super-admin'))
                                <flux:select label="Estado del Cliente" wire:model="status">
                                    <flux:select.option value="active">Activo (Acceso completo)</flux:select.option>
                                    <flux:select.option value="suspended">Suspendido (Acceso bloqueado)
                                    </flux:select.option>
                                </flux:select>
                            @endif
                        </div>
                    </div>

                    {{-- Datos del Formulario --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input label="Nombre / Razón Social" wire:model="name" />
                        <flux:input label="RUC / Identificación" wire:model="ruc" />
                        <flux:input type="email" label="Correo Electrónico" wire:model="email" />
                        <flux:input label="Teléfono" wire:model="phone" />
                        <div class="md:col-span-2">
                            <flux:textarea label="Dirección Física" wire:model="address" rows="2" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">
                            {{ $company_id ? 'Actualizar Datos' : 'Registrar Empresa' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
