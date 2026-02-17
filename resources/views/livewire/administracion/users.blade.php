<div class="space-y-6">
    <flux:header>
        <div>
            <flux:heading size="xl underline">Gestión de Usuarios</flux:heading>
            <flux:subheading>
                {{ auth()->user()->hasRole('super-admin') ? 'Administración Global de Usuarios' : 'Gestión de Personal de ' . auth()->user()->company->name }}
            </flux:subheading>
        </div>
        <flux:spacer />
        <flux:button variant="primary" icon="user-plus" wire:click="create">Nuevo Usuario</flux:button>
    </flux:header>

    {{-- Filtros Rápidos --}}
    <div class="flex items-center gap-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Buscar por nombre o email..." class="max-w-md" />
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Nombre / Email</flux:table.column>
            <flux:table.column>Empresa</flux:table.column>
            <flux:table.column>Rol</flux:table.column>
            <flux:table.column>Estado</flux:table.column>
            <flux:table.column align="end">Acciones</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($users as $user)
                <flux:table.row :wire:key="$user->id">
                    <flux:table.cell>
                        <div class="flex flex-col">
                            <span class="font-medium text-zinc-800 dark:text-white">{{ $user->name }}</span>
                            <span class="text-xs text-zinc-500">{{ $user->email }}</span>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        @if($user->company)
                            <flux:badge color="blue" variant="outline" size="sm">{{ $user->company->name }}</flux:badge>
                        @else
                            <flux:badge color="orange" variant="outline" size="sm">SISTEMA (GLOBAL)</flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        @foreach($user->getRoleNames() as $role)
                            <flux:badge size="sm" color="zinc">
                                {{ strtoupper($role) }}
                            </flux:badge>
                        @endforeach
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge :color="$user->is_active ? 'green' : 'red'" size="sm" variant="solid">
                            {{ $user->is_active ? 'Activo' : 'Bloqueado' }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            {{-- Botón de Bloqueo/Desbloqueo --}}
                            <flux:tooltip :content="$user->is_active ? 'Bloquear acceso' : 'Permitir acceso'">
                                <flux:button variant="ghost" 
                                    icon="{{ $user->is_active ? 'lock-open' : 'lock-closed' }}" 
                                    :color="$user->is_active ? 'zinc' : 'red'"
                                    wire:click="toggleStatus({{ $user->id }})" 
                                    size="sm" />
                            </flux:tooltip>
                            
                            <flux:button variant="ghost" icon="pencil-square" size="sm" wire:click="edit({{ $user->id }})" />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

    {{-- MODAL DE CREACIÓN / EDICIÓN --}}
    <flux:modal wire:model="showModal" class="md:w-112.5 space-y-6">
        <div>
            <flux:heading size="lg">{{ $userId ? 'Modificar Usuario' : 'Registrar Nuevo Usuario' }}</flux:heading>
            <flux:subheading>Complete los datos de acceso del usuario.</flux:subheading>
        </div>

        <div class="space-y-4">
            <flux:input wire:model="name" label="Nombre Completo" placeholder="Ej. Juan Pérez" />
            <flux:input wire:model="email" label="Correo Electrónico" placeholder="usuario@dominio.com" />
            
            @if(!$userId)
                <flux:input wire:model="password" type="password" label="Contraseña Inicial" placeholder="Min. 8 caracteres" />
            @else
                <flux:subheading class="text-xs text-orange-500">Deje la contraseña en blanco para no cambiarla.</flux:subheading>
                <flux:input wire:model="password" type="password" label="Nueva Contraseña (Opcional)" />
            @endif

            {{-- Selección de Empresa: Solo Super-Admin puede cambiarla --}}
            @if(auth()->user()->hasRole('super-admin'))
                <flux:select wire:model="company_id" label="Empresa Responsable">
                    <option value="">-- Sin Empresa (Acceso Global) --</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </flux:select>
            @else
                {{-- Admin de Empresa solo ve su empresa como dato fijo --}}
                <flux:input label="Empresa" value="{{ auth()->user()->company->name }}" disabled />
            @endif

            <flux:select wire:model="role" label="Rol de Usuario">
                <option value="">Seleccione un nivel de acceso</option>
                @foreach($roles as $r)
                    <option value="{{ $r->name }}">{{ strtoupper($r->name) }}</option>
                @endforeach
            </flux:select>
        </div>

        <div class="flex gap-2">
            <flux:spacer />
            <flux:button variant="ghost" wire:click="$set('showModal', false)">Cancelar</flux:button>
            <flux:button variant="primary" wire:click="save">
                {{ $userId ? 'Actualizar Datos' : 'Crear Usuario' }}
            </flux:button>
        </div>
    </flux:modal>
</div>