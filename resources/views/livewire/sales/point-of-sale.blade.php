<div class="mx-auto min-h-screen max-w-7xl space-y-6 bg-zinc-50/70 p-4 dark:bg-zinc-950 sm:p-6">
    {{-- Header Principal --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-zinc-200 dark:border-zinc-800">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white flex items-center gap-2">
                <span class="rounded-xl bg-indigo-700 p-2 text-white shadow-lg shadow-indigo-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </span>
                Punto de Venta / Facturación SRI
            </h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Ventas e información fiscal organizadas en un solo flujo.</p>
        </div>
        
        <div class="flex items-center gap-2">
            <span class="flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-800 dark:border-indigo-900 dark:bg-indigo-950 dark:text-indigo-200">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Punto de venta
            </span>
        </div>
    </div>

    {{-- Layout Principal: 2 Columnas --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- Columna Izquierda --}}
        <div class="lg:col-span-8 space-y-6">
            
            {{-- Tarjeta 1: Selección de Cliente --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-sm font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Datos del Cliente
                    </h2>
                    @if($selectedCustomer)
                        <button wire:click="$set('selectedCustomer', null)" class="text-xs font-bold text-red-500 hover:text-red-600 transition-colors flex items-center gap-1">
                            ✕ Cambiar Cliente
                        </button>
                    @endif
                </div>

                @if(!$selectedCustomer)
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input wire:model.live.debounce.450ms="customerSearch" type="text" placeholder="Buscar cliente por nombre, cédula o RUC..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 text-sm focus:bg-white dark:focus:bg-zinc-800 focus:ring-2 focus:ring-indigo-500 dark:text-white transition-all">

                        @if($customers->isNotEmpty())
                            <div class="absolute z-50 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-2xl mt-2 overflow-hidden divide-y divide-zinc-100 dark:divide-zinc-700/50">
                                @foreach($customers as $c)
                                    <button wire:click="selectCustomer({{ $c->id }})" class="w-full text-left p-3.5 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 flex justify-between items-center transition-colors">
                                        <div>
                                            <p class="font-bold text-sm text-zinc-800 dark:text-zinc-100">{{ $c->name }}</p>
                                            <p class="text-xs text-zinc-500 font-mono mt-0.5">{{ $c->identification }}</p>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-md bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300">
                                            {{ $c->identification_type == '04' ? 'RUC' : ($c->identification_type == '05' ? 'Cédula' : 'Pasaporte') }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if(strlen(trim($customerSearch)) > 1 && $customers->isEmpty())
                        <div class="mt-3 flex flex-col gap-3 rounded-xl border border-indigo-100 bg-indigo-50 p-3 dark:border-indigo-900/50 dark:bg-indigo-950/30 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-indigo-900 dark:text-indigo-200">No encontramos un cliente con esa búsqueda.</p>
                            <button wire:click="openCustomerModal" type="button"
                                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-indigo-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Crear cliente
                            </button>
                        </div>
                    @endif
                @else
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-zinc-50 dark:bg-zinc-800/40 p-4 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase text-zinc-400 tracking-wider block">Razón Social</span>
                            <p class="text-sm font-bold text-zinc-800 dark:text-zinc-100 truncate">{{ $selectedCustomer['name'] }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase text-zinc-400 tracking-wider block">Identificación</span>
                            <p class="text-sm font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $selectedCustomer['identification'] ?? 'Sin identificación' }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase text-zinc-400 tracking-wider block">Correo Electrónico</span>
                            <p class="text-sm text-zinc-600 dark:text-zinc-300 truncate">{{ $selectedCustomer['email'] ?? 'Sin Correo' }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase text-zinc-400 tracking-wider block">Teléfono</span>
                            <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ $selectedCustomer['phone'] ?? 'S/N' }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Tarjeta 2: Selección y Adición de Productos --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm">
                <div class="flex flex-col items-stretch gap-3 w-full sm:flex-row sm:items-end">
                    <div class="relative min-w-0 flex-1">
                        <label for="product-search" class="text-[10px] font-extrabold text-zinc-500 uppercase tracking-wider block mb-1.5">Producto</label>
                        <div class="relative">
                            <input id="product-search" wire:model.live.debounce.500ms="productSearch" type="search" autocomplete="off" placeholder="Buscar por nombre o SKU..."
                                   class="w-full rounded-xl border border-zinc-200 bg-zinc-50 py-3 pl-10 pr-4 text-sm transition-all focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-800/50 dark:text-white dark:focus:bg-zinc-800">
                            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3m1.8-5.2a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"></path></svg>
                        </div>
                        <p class="mt-1.5 text-xs text-zinc-500 dark:text-zinc-400">Escribe al menos 2 caracteres. Buscaremos cuando termines de escribir.</p>

                        @error('selectedProduct')
                            <p class="mt-2 text-sm font-medium text-red-600" role="alert">{{ $message }}</p>
                        @enderror

                        <div wire:loading.delay wire:target="productSearch" class="absolute z-50 mt-2 w-full rounded-xl border border-indigo-100 bg-white px-4 py-3 text-sm text-zinc-500 shadow-lg dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                            Buscando productos...
                        </div>

                        @if(!empty($products))
                            <div class="absolute z-40 mt-2 w-full overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-800">
                                @foreach($products as $p)
                                    <button wire:key="pos-product-{{ $p->id }}" wire:click="selectProduct({{ $p->id }})" type="button" class="flex w-full items-center justify-between gap-3 border-b border-zinc-100 p-3.5 text-left transition last:border-0 hover:bg-indigo-50 dark:border-zinc-700 dark:hover:bg-indigo-950/40">
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-bold text-zinc-800 dark:text-zinc-100">{{ $p->name }}</span>
                                            <span class="mt-0.5 block text-xs font-medium text-zinc-500 dark:text-zinc-400">SKU: {{ $p->sku ?: 'Sin código' }} · ${{ number_format($p->price, 2) }}</span>
                                        </span>
                                        <span class="shrink-0 rounded-lg bg-emerald-50 px-2.5 py-1 text-[10px] font-extrabold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                                            {{ $p->current_stock }} disponibles
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @if(strlen(trim($productSearch)) > 1 && empty($products) && (!$selectedProduct || $productSearch !== $selectedProduct->name))
                            <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200" role="status">
                                No encontramos productos disponibles que coincidan con <strong>{{ $productSearch }}</strong>. Revisa el nombre o el SKU.
                            </div>
                        @endif
                    </div>

                    <div class="w-full sm:w-28">
                        <label class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block mb-1.5 text-center">Cantidad</label>
                        <input type="number" wire:model="quantity" wire:keydown.enter="addItem" min="1" 
                               class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 py-2.5 text-center font-bold text-sm dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="w-full sm:w-auto">
                        <button wire:click="addItem" class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/20 text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Agregar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tarjeta 3: Tabla de Detalle de Venta --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-wider">Detalle de la Venta</h3>
                    <span class="text-xs font-medium text-zinc-400">{{ count($items) }} Ítems agregados</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-[10px] font-black text-zinc-400 uppercase tracking-wider border-b border-zinc-100 dark:border-zinc-800">
                            <tr>
                                <th class="px-5 py-3">Descripción</th>
                                <th class="px-5 py-3 text-center">Cant.</th>
                                <th class="px-5 py-3 text-right">P. Unitario</th>
                                <th class="px-5 py-3 text-right">IVA (15%)</th>
                                <th class="px-5 py-3 text-right">Subtotal</th>
                                <th class="px-5 py-3 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 text-sm">
                            @forelse($items as $index => $item)
                            <tr class="dark:text-zinc-200 hover:bg-zinc-50/80 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="px-5 py-3.5 font-medium">{{ $item['name'] }}</td>
                                <td class="px-5 py-3.5 text-center font-bold text-zinc-600 dark:text-zinc-300">{{ $item['quantity'] }}</td>
                                <td class="px-5 py-3.5 text-right font-mono">${{ number_format($item['unit_price'], 2) }}</td>
                                <td class="px-5 py-3.5 text-right font-mono text-zinc-400">${{ number_format($item['vat_amount'], 2) }}</td>
                                <td class="px-5 py-3.5 text-right font-mono font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($item['subtotal'], 2) }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    <button wire:click="removeItem({{ $index }})" class="p-1 text-zinc-400 hover:text-red-500 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-zinc-400 italic">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-zinc-300 dark:text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                                    No hay productos seleccionados en esta orden.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Resumen Fiscal y Checkout --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 p-6 shadow-sm sticky top-6">
                <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100 uppercase tracking-wider pb-4 border-b border-zinc-100 dark:border-zinc-800 mb-6 flex items-center justify-between">
                    <span>Resumen Fiscal SRI</span>
                    <span class="text-[10px] font-black bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 px-2 py-0.5 rounded">FACTURA 01</span>
                </h3>

                {{-- Selector Forma de Pago --}}
                <div class="mb-6 space-y-2">
                    <label class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Forma de Pago SRI</label>
                    <select wire:model="payment_method_sri" class="w-full bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 text-xs rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 font-medium">
                        <option value="01">01 - Sin Utilización del Sistema Financiero (Efectivo)</option>
                        <option value="19">19 - Tarjeta de Crédito</option>
                        <option value="20">20 - Otros con Sistema Financiero (Transferencia/Depósito)</option>
                    </select>
                </div>

                {{-- Totales Desglosados --}}
                <div class="space-y-3 pt-2 pb-6 border-t border-b border-zinc-100 dark:border-zinc-800 font-mono text-sm">
                    <div class="flex justify-between text-zinc-500 dark:text-zinc-400">
                        <span>Subtotal (15%):</span>
                        <span class="font-bold text-zinc-800 dark:text-zinc-200">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-zinc-500 dark:text-zinc-400">
                        <span>Subtotal (0%):</span>
                        <span class="font-bold text-zinc-800 dark:text-zinc-200">$0.00</span>
                    </div>
                    <div class="flex justify-between text-zinc-500 dark:text-zinc-400">
                        <span>Monto IVA (15%):</span>
                        <span class="font-bold text-zinc-800 dark:text-zinc-200">${{ number_format($iva, 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-baseline pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <span class="text-xs font-extrabold font-sans uppercase text-zinc-900 dark:text-white">Total a Pagar</span>
                        <span class="text-3xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">${{ number_format($total, 2) }}</span>
                    </div>
                </div>

                {{-- Botón de Acción Principal --}}
                <div class="mt-6">
                    <button wire:click="store" wire:loading.attr="disabled" 
                            class="w-full py-4 px-6 bg-emerald-500 hover:bg-emerald-600 text-zinc-950 font-black rounded-xl shadow-lg shadow-emerald-500/25 uppercase text-xs tracking-wider flex items-center justify-center gap-2 transition-all hover:-translate-y-0.5 active:translate-y-0">
                        <span wire:loading.remove class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Confirmar y Emitir Factura
                        </span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4 text-zinc-950" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Emitiendo comprobante SRI...
                        </span>
                    </button>
                </div>
            </div>
        </div>

    <flux:modal wire:model="isCustomerModalOpen" class="md:w-140">
        <form wire:submit="saveCustomer" class="space-y-6">
            <div>
                <flux:heading size="lg">Registrar cliente</flux:heading>
                <flux:subheading>El cliente quedará seleccionado automáticamente en esta factura.</flux:subheading>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <flux:select label="Tipo de documento" wire:model="newCustomerIdentificationType">
                        <flux:select.option value="05">Cédula</flux:select.option>
                        <flux:select.option value="04">RUC</flux:select.option>
                        <flux:select.option value="06">Pasaporte</flux:select.option>
                        <flux:select.option value="07">Consumidor final</flux:select.option>
                    </flux:select>
                    <div class="sm:col-span-2">
                        <flux:input label="Identificación" wire:model="newCustomerIdentification" />
                    </div>
                </div>
                <flux:input label="Nombre / Razón social" wire:model="newCustomerName" />
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input label="Correo electrónico" type="email" wire:model="newCustomerEmail" />
                    <flux:input label="Teléfono" wire:model="newCustomerPhone" />
                </div>
                <flux:textarea label="Dirección" wire:model="newCustomerAddress" rows="2" />
            </div>

            <div class="flex justify-end gap-2">
                <flux:button type="button" wire:click="closeCustomerModal" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="primary">Guardar y seleccionar</flux:button>
            </div>
        </form>
    </flux:modal>
</div>