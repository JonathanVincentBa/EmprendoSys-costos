<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <flux:heading size="xl">
                {{ auth()->user()->hasRole('super-admin') ? ($isEditing ? 'Configuración de Empresa' : 'Gestión de Clientes') : 'Mi Empresa' }}
            </flux:heading>
            <flux:subheading>Administra la información legal, datos tributarios SRI y firma electrónica para emisión de comprobantes.</flux:subheading>
        </div>

        @if (auth()->user()->hasRole('super-admin'))
            @if ($isEditing)
                <flux:button wire:click="$set('isEditing', false)" variant="subtle" icon="arrow-left">Volver al listado</flux:button>
            @else
                <flux:button wire:click="createCompany" variant="primary" icon="plus">Nuevo Cliente</flux:button>
            @endif
        @endif
    </div>

    @if (auth()->user()->hasRole('super-admin') && !$isEditing)
        {{-- TABLA DE GESTIÓN PARA SUPER-ADMIN --}}
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Empresa</flux:table.column>
                    <flux:table.column>RUC / Email</flux:table.column>
                    <flux:table.column>Estab. / Pt. Emi</flux:table.column>
                    <flux:table.column>Ambiente SRI</flux:table.column>
                    <flux:table.column>Estado</flux:table.column>
                    <flux:table.column>Acciones</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($companies as $item)
                        <flux:table.row :key="$item->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    @if ($item->logo)
                                        <img src="{{ Storage::url($item->logo) }}" class="w-8 h-8 rounded-lg object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center">
                                            <flux:icon name="building-office" variant="micro" />
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-zinc-800 dark:text-white">{{ $item->name }}</div>
                                        <div class="text-xs text-zinc-400">{{ $item->razon_social }}</div>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="text-sm font-mono">{{ $item->ruc }}</div>
                                <div class="text-xs text-zinc-500">{{ $item->email }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="text-xs font-mono bg-zinc-100 dark:bg-zinc-700 px-2 py-1 rounded">
                                    {{ $item->estab ?? '001' }}-{{ $item->pto_emi ?? '001' }}
                                </span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="{{ $item->sri_environment === '2' ? 'green' : 'amber' }}" size="sm">
                                    {{ $item->sri_environment === '2' ? 'Producción' : 'Pruebas' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="{{ $item->status === 'active' ? 'green' : 'red' }}" size="sm" inset>
                                    {{ $item->status === 'active' ? 'Activo' : 'Suspendido' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button wire:click="editCompany({{ $item->id }})" variant="subtle" icon="pencil-square" size="sm" />
                                    <flux:button wire:click="toggleStatus({{ $item->id }})" variant="subtle" icon="{{ $item->status === 'active' ? 'pause-circle' : 'play-circle' }}" size="sm" title="{{ $item->status === 'active' ? 'Suspender' : 'Activar' }}" />
                                    <flux:button wire:confirm="¿Estás seguro de eliminar esta empresa? Esta acción no se puede deshacer." wire:click="deleteCompany({{ $item->id }})" variant="subtle" color="red" icon="trash" size="sm" />
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
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-sm overflow-hidden">
                <form wire:submit="save" class="p-6 space-y-8">
                    
                    {{-- 1. SECCIÓN: LOGO Y ESTADO --}}
                    <div class="flex flex-col md:flex-row gap-6 items-start md:items-center pb-6 border-b border-zinc-200 dark:border-zinc-700">
                        <div class="relative group">
                            <div class="w-32 h-32 rounded-2xl border-2 border-dashed border-zinc-300 dark:border-zinc-600 flex items-center justify-center overflow-hidden bg-zinc-50 dark:bg-zinc-800">
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
                                    <flux:select.option value="suspended">Suspendido (Acceso bloqueado)</flux:select.option>
                                </flux:select>
                            @endif
                        </div>
                    </div>

                    {{-- 2. SECCIÓN: DATOS GENERALES Y LEGALES --}}
                    <div>
                        <flux:heading size="lg" class="mb-4">Información General y Legal</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:input label="Nombre Comercial" wire:model="name" placeholder="Ej: Mi Negocio" />
                            <flux:input label="Razón Social" wire:model="razon_social" placeholder="Ej: Mi Negocio S.A.S." />
                            <flux:input label="RUC (13 dígitos)" wire:model="ruc" maxlength="13" placeholder="1790000000001" />
                            <flux:input type="email" label="Correo Electrónico" wire:model="email" />
                            <flux:input label="Teléfono" wire:model="phone" />
                            
                            <div class="md:col-span-2">
                                <flux:textarea label="Dirección Matriz" wire:model="address" rows="2" placeholder="Dirección registrada en la matriz..." />
                            </div>
                            <div class="md:col-span-2">
                                <flux:textarea label="Dirección del Establecimiento" wire:model="establishment_address" rows="2" placeholder="Si es igual a la matriz, volver a escribirla..." />
                            </div>
                        </div>
                    </div>

                    {{-- 3. SECCIÓN: CONFIGURACIÓN TRIBUTARIA (SRI) --}}
                    <div class="pt-6 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:heading size="lg" class="mb-4">Configuración Tributaria (SRI)</flux:heading>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:input label="Establecimiento" wire:model="estab" placeholder="001" maxlength="3" />
                            <flux:input label="Punto de Emisión" wire:model="pto_emi" placeholder="001" maxlength="3" />
                            
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Obligado a Llevar Contabilidad</label>
                                <select wire:model="obligado_contabilidad" class="w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                                    <option value="NO">NO</option>
                                    <option value="SI">SI</option>
                                </select>
                            </div>

                            <flux:input label="Contribuyente Especial (N° Resolución)" wire:model="contribuyente_especial" placeholder="Opcional (Ej: 1234)" />

                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Régimen RIMPE</label>
                                <select wire:model="contribuyente_rimpe" class="w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                                    <option value="">No aplica / Régimen General</option>
                                    <option value="CONTRIBUYENTE RÉGIMEN RIMPE">CONTRIBUYENTE RÉGIMEN RIMPE</option>
                                    <option value="CONTRIBUYENTE NEGOCIO POPULAR - RÉGIMEN RIMPE">CONTRIBUYENTE NEGOCIO POPULAR - RÉGIMEN RIMPE</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Ambiente de Emisión SRI</label>
                                <select wire:model="sri_environment" class="w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                                    <option value="1">1 - Pruebas (Test)</option>
                                    <option value="2">2 - Producción (Validez Legal)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 4. SECCIÓN: SERVIDOR DE CORREO --}}
                    <div class="pt-6 border-t border-zinc-200 dark:border-zinc-700 space-y-4">
                        <div>
                            <flux:heading size="lg">Correo para envío de facturas</flux:heading>
                            <p class="mt-1 text-xs text-zinc-500">Configura el SMTP de la empresa para enviar el XML autorizado al cliente.</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <flux:input label="Servidor SMTP" wire:model="mail_host" placeholder="smtp.gmail.com" />
                            <flux:input label="Puerto" type="number" wire:model="mail_port" placeholder="587" />
                            <div>
                                <label class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Cifrado</label>
                                <select wire:model="mail_encryption" class="w-full rounded-md border-zinc-300 text-sm shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                    <option value="none">Sin cifrado</option>
                                </select>
                            </div>
                            <flux:input label="Usuario SMTP" type="email" wire:model="mail_username" placeholder="correo@empresa.com" />
                            <flux:input label="Contraseña SMTP" type="password" wire:model="mail_password" placeholder="Dejar vacío para conservarla" />
                            <flux:input label="Nombre del remitente" wire:model="mail_from_name" placeholder="Mi Empresa" />
                        </div>
                    </div>

                    {{-- 4. SECCIÓN: FIRMA ELECTRÓNICA --}}
                    <div class="pt-6 border-t border-zinc-200 dark:border-zinc-700 space-y-4">
                        <flux:heading size="lg">Firma Digital (.p12 / .pfx)</flux:heading>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col justify-center">
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Estado de Firma Digital</label>
                                <div>
                                    @if ($has_signature)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                            ✓ Archivo .p12 Registrado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                            ⚠ Pendiente de Cargar (.p12)
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <flux:input label="Contraseña de la Firma Digital" type="password" wire:model="signature_password" placeholder="••••••••" />

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Cargar/Reemplazar Archivo de Firma (.p12 / .pfx)</label>
                                <input type="file" wire:model="signature_file" accept=".p12,.pfx" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-zinc-700 dark:file:text-zinc-300" />
                                @error('signature_file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- BOTÓN GUARDAR --}}
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