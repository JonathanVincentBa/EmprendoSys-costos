<div class="app-page-panel mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
    <header class="relative overflow-hidden rounded-2xl bg-indigo-950 px-5 py-6 text-white shadow-lg shadow-indigo-950/10 sm:px-7">
        <div aria-hidden="true" class="absolute -right-10 -top-28 h-64 w-64 rounded-full bg-indigo-700/50 blur-3xl"></div>
        <div aria-hidden="true" class="absolute -bottom-32 right-1/3 h-48 w-48 rounded-full bg-emerald-500/20 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-emerald-300 ring-1 ring-white/15">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l2.2 11.1A2 2 0 0 0 9.2 16h8.6a2 2 0 0 0 2-1.6L21 8H6m4 13h.01M17 21h.01M9 11h8"/></svg>
                </span>
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-emerald-300">Comercial · facturación electrónica</p>
                    <h1 class="mt-1 text-2xl font-black tracking-tight sm:text-3xl">Punto de venta</h1>
                    <p class="mt-1 text-sm text-indigo-100/80">Registra la venta, selecciona el cliente y emite el comprobante.</p>
                </div>
            </div>
            <div class="inline-flex w-fit items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-2 text-xs font-bold text-white">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                Factura electrónica SRI
            </div>
        </div>
    </header>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="min-w-0 space-y-5">
            <section class="rounded-2xl border border-indigo-100 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100 text-sm font-black text-indigo-700">01</span>
                        <div>
                            <h2 class="text-sm font-extrabold text-indigo-950">Cliente</h2>
                            <p class="text-xs text-zinc-500">Asocia la factura al comprador</p>
                        </div>
                    </div>
                    @if($selectedCustomer)
                        <button wire:click="$set('selectedCustomer', null)" type="button" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-bold text-indigo-700 transition hover:bg-indigo-50">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 3a7 7 0 1 0 0 14 7 7 0 0 0 0-14Zm3.03 9.97a.75.75 0 0 1-1.06 1.06L10 12.06l-1.97 1.97a.75.75 0 1 1-1.06-1.06L8.94 11 6.97 9.03a.75.75 0 1 1 1.06-1.06L10 9.94l1.97-1.97a.75.75 0 1 1 1.06 1.06L12.06 11l.97.97Z"/></svg>
                            Cambiar
                        </button>
                    @endif
                </div>

                @if(!$selectedCustomer)
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                        </div>
                        <input wire:model.live.debounce.450ms="customerSearch" type="search" autocomplete="off" placeholder="Buscar por nombre, cédula o RUC..."
                            class="w-full rounded-xl border border-zinc-200 bg-zinc-50 py-3 pl-10 pr-4 text-sm transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        @if($customers->isNotEmpty())
                            <div class="absolute z-40 mt-2 w-full overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-800">
                                @foreach($customers as $c)
                                    <button wire:key="pos-customer-{{ $c->id }}" wire:click="selectCustomer({{ $c->id }})" type="button" class="flex w-full items-center justify-between gap-3 border-b border-zinc-100 p-3.5 text-left transition last:border-0 hover:bg-indigo-50 dark:border-zinc-700 dark:hover:bg-indigo-950/40">
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-bold text-zinc-800 dark:text-zinc-100">{{ $c->name }}</span>
                                            <span class="mt-0.5 block text-xs text-zinc-500">{{ $c->identification }}</span>
                                        </span>
                                        <span class="shrink-0 rounded-lg bg-indigo-50 px-2.5 py-1 text-[10px] font-extrabold uppercase text-indigo-700 dark:bg-indigo-950 dark:text-indigo-200">
                                            {{ $c->identification_type == '04' ? 'RUC' : ($c->identification_type == '05' ? 'Cédula' : 'Pasaporte') }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if(strlen(trim($customerSearch)) > 1 && $customers->isEmpty())
                        <div class="mt-3 flex flex-col gap-3 rounded-xl border border-amber-200 bg-amber-50 p-3.5 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-amber-900">No encontramos un cliente con esa búsqueda. Puedes registrarlo ahora.</p>
                            <button wire:click="openCustomerModal" type="button" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-indigo-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-800">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Crear cliente
                            </button>
                        </div>
                    @endif
                @else
                    <div class="grid gap-4 rounded-xl border border-emerald-100 bg-emerald-50/60 p-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="min-w-0">
                            <span class="block text-[10px] font-black uppercase tracking-wider text-emerald-800/70">Cliente</span>
                            <p class="mt-1 truncate text-sm font-extrabold text-indigo-950">{{ $selectedCustomer['name'] }}</p>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-black uppercase tracking-wider text-emerald-800/70">Identificación</span>
                            <p class="mt-1 truncate font-mono text-sm font-bold text-indigo-800">{{ $selectedCustomer['identification'] ?? 'Sin identificación' }}</p>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-black uppercase tracking-wider text-emerald-800/70">Correo</span>
                            <p class="mt-1 truncate text-sm font-medium text-zinc-700">{{ $selectedCustomer['email'] ?? 'Sin correo registrado' }}</p>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-black uppercase tracking-wider text-emerald-800/70">Teléfono</span>
                            <p class="mt-1 truncate text-sm font-medium text-zinc-700">{{ $selectedCustomer['phone'] ?? 'No registrado' }}</p>
                        </div>
                    </div>
                @endif
            </section>

            <section class="rounded-2xl border border-indigo-100 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-4 flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-sm font-black text-emerald-700">02</span>
                    <div>
                        <h2 class="text-sm font-extrabold text-indigo-950">Agregar productos</h2>
                        <p class="text-xs text-zinc-500">Busca por nombre o SKU; resultados limitados a productos con stock</p>
                    </div>
                </div>

                <div class="grid items-end gap-3 sm:grid-cols-[minmax(0,1fr)_7rem_auto]">
                    <div class="relative min-w-0">
                        <label for="product-search" class="mb-1.5 block text-xs font-bold text-zinc-600">Producto</label>
                        <div class="relative">
                            <input id="product-search" wire:model.live.debounce.500ms="productSearch" type="search" autocomplete="off" placeholder="Nombre o SKU del producto..."
                                class="w-full rounded-xl border border-zinc-200 bg-zinc-50 py-3 pl-10 pr-4 text-sm transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3m1.8-5.2a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                        </div>
                        <p class="mt-1.5 text-xs text-zinc-500">La búsqueda comienza después de dejar de escribir.</p>

                        @error('selectedProduct')
                            <p class="mt-2 text-sm font-medium text-red-600" role="alert">{{ $message }}</p>
                        @enderror

                        <div wire:loading.delay wire:target="productSearch" class="absolute z-50 mt-2 w-full rounded-xl border border-indigo-100 bg-white px-4 py-3 text-sm text-zinc-500 shadow-lg dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                            Buscando productos...
                        </div>

                        @if(!empty($products))
                            <div class="absolute z-40 mt-2 max-h-72 w-full overflow-y-auto rounded-xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-800">
                                @foreach($products as $p)
                                    <button wire:key="pos-product-{{ $p->id }}" wire:click="selectProduct({{ $p->id }})" type="button" class="flex w-full items-center justify-between gap-3 border-b border-zinc-100 p-3.5 text-left transition last:border-0 hover:bg-indigo-50 dark:border-zinc-700 dark:hover:bg-indigo-950/40">
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-bold text-zinc-800 dark:text-zinc-100">{{ $p->name }}</span>
                                            <span class="mt-0.5 block text-xs font-medium text-zinc-500 dark:text-zinc-400">SKU: {{ $p->sku ?: 'Sin código' }}</span>
                                        </span>
                                        <span class="shrink-0 text-right">
                                            <span class="block text-sm font-extrabold text-indigo-700">${{ number_format($p->price, 2) }}</span>
                                            <span class="mt-0.5 block text-[10px] font-bold text-emerald-700">{{ $p->current_stock }} disponibles</span>
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @if(strlen(trim($productSearch)) > 1 && empty($products) && (!$selectedProduct || $productSearch !== $selectedProduct->name))
                            <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900" role="status">
                                No encontramos productos disponibles que coincidan con <strong>{{ $productSearch }}</strong>. Revisa el nombre o el SKU.
                            </div>
                        @endif

                        @if($selectedProduct && $productSearch === $selectedProduct->name)
                            <div class="mt-3 flex items-center justify-between gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-sm">
                                <span class="min-w-0 truncate font-bold text-emerald-900">Seleccionado: {{ $selectedProduct->name }}</span>
                                <span class="shrink-0 font-black text-emerald-800">${{ number_format($unit_price, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="pos-quantity" class="mb-1.5 block text-xs font-bold text-zinc-600">Cantidad</label>
                        <input id="pos-quantity" type="number" wire:model="quantity" wire:keydown.enter="addItem" min="1"
                            class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-3 text-center text-sm font-bold text-zinc-800 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    </div>

                    <button wire:click="addItem" type="button" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-700 px-5 py-3 text-sm font-extrabold text-white shadow-md shadow-indigo-900/15 transition hover:bg-indigo-800 active:scale-[0.98]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14m7-7H5"/></svg>
                        Agregar
                    </button>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-indigo-100 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-sm font-black text-amber-700">03</span>
                        <div>
                            <h2 class="text-sm font-extrabold text-indigo-950">Detalle de venta</h2>
                            <p class="text-xs text-zinc-500">Revisa los productos antes de emitir</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-extrabold text-indigo-700">{{ count($items) }} {{ count($items) === 1 ? 'ítem' : 'ítems' }}</span>
                </div>

                @if(count($items))
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[44rem] text-left">
                            <thead class="bg-zinc-50 text-[10px] font-black uppercase tracking-wider text-zinc-500">
                                <tr>
                                    <th class="px-5 py-3 sm:px-6">Producto</th>
                                    <th class="px-3 py-3 text-center">Cant.</th>
                                    <th class="px-3 py-3 text-right">Precio unit.</th>
                                    <th class="px-3 py-3 text-right">IVA</th>
                                    <th class="px-3 py-3 text-right">Subtotal</th>
                                    <th class="px-5 py-3 text-center sm:px-6">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 text-sm">
                                @foreach($items as $index => $item)
                                    <tr wire:key="pos-cart-item-{{ $item['product_id'] }}-{{ $index }}" class="transition hover:bg-indigo-50/30">
                                        <td class="px-5 py-4 font-bold text-zinc-800 sm:px-6">{{ $item['name'] }}</td>
                                        <td class="px-3 py-4 text-center font-semibold text-zinc-600">{{ $item['quantity'] }}</td>
                                        <td class="px-3 py-4 text-right font-mono text-zinc-600">${{ number_format($item['unit_price'], 2) }}</td>
                                        <td class="px-3 py-4 text-right font-mono text-zinc-500">${{ number_format($item['vat_amount'], 2) }}</td>
                                        <td class="px-3 py-4 text-right font-mono font-extrabold text-indigo-700">${{ number_format($item['subtotal'], 2) }}</td>
                                        <td class="px-5 py-4 text-center sm:px-6">
                                            <button wire:click="removeItem({{ $index }})" type="button" aria-label="Quitar {{ $item['name'] }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-400 transition hover:bg-red-50 hover:text-red-600">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center px-6 py-12 text-center">
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-400">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 3h2l2.2 11.1A2 2 0 0 0 9.2 16h8.6a2 2 0 0 0 2-1.6L21 8H6m4 13h.01M17 21h.01M9 11h8"/></svg>
                        </span>
                        <p class="mt-4 text-sm font-bold text-indigo-950">Tu venta todavía está vacía</p>
                        <p class="mt-1 text-xs text-zinc-500">Busca un producto arriba para comenzar a agregar artículos.</p>
                    </div>
                @endif
            </section>
        </div>

        <aside class="lg:sticky lg:top-6">
            <section class="overflow-hidden rounded-2xl border border-indigo-900 bg-white shadow-lg shadow-indigo-950/10">
                <div class="bg-indigo-950 px-5 py-5 text-white">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-indigo-200">Resumen de compra</p>
                            <h2 class="mt-1 text-lg font-black">Totales de la factura</h2>
                        </div>
                        <span class="rounded-lg border border-white/15 bg-white/10 px-2.5 py-1 text-[10px] font-black tracking-wider text-emerald-200">FACTURA 01</span>
                    </div>
                </div>

                <div class="space-y-5 p-5">
                    <div>
                        <label for="payment-method" class="mb-2 block text-xs font-extrabold text-zinc-600">Forma de pago SRI</label>
                        <select id="payment-method" wire:model="payment_method_sri" class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-3 text-xs font-semibold text-zinc-800 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500">
                            <option value="01">01 · Efectivo</option>
                            <option value="19">19 · Tarjeta de crédito</option>
                            <option value="20">20 · Transferencia / depósito</option>
                        </select>
                    </div>

                    <div class="space-y-3 border-y border-zinc-100 py-5 text-sm">
                        <div class="flex items-center justify-between gap-3 text-zinc-500">
                            <span>Subtotal gravado</span>
                            <span class="font-mono font-bold text-zinc-800">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 text-zinc-500">
                            <span>Subtotal 0%</span>
                            <span class="font-mono font-bold text-zinc-800">$0.00</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 text-zinc-500">
                            <span>IVA (15%)</span>
                            <span class="font-mono font-bold text-zinc-800">${{ number_format($iva, 2) }}</span>
                        </div>
                    </div>

                    <div class="rounded-xl bg-indigo-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-indigo-700">Total a pagar</p>
                        <p class="mt-1 text-3xl font-black tracking-tight text-indigo-950">${{ number_format($total, 2) }}</p>
                    </div>

                    <div>
                        <button wire:click="store" wire:loading.attr="disabled" type="button" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-500 px-5 py-3.5 text-sm font-black text-indigo-950 shadow-md shadow-emerald-700/15 transition hover:bg-emerald-400 disabled:cursor-wait disabled:opacity-60">
                            <span wire:loading.remove wire:target="store" class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m5 12 4 4L19 6"/></svg>
                                Confirmar y emitir
                            </span>
                            <span wire:loading wire:target="store" class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Procesando factura...
                            </span>
                        </button>
                        <p class="mt-3 text-center text-[11px] leading-5 text-zinc-500">Verifica el cliente y los productos antes de enviar el comprobante al SRI.</p>
                    </div>
                </div>
            </section>
        </aside>
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
