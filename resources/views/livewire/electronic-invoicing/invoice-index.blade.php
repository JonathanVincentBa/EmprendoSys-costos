<div class="p-6 space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Facturación Electrónica</flux:heading>
            <flux:subheading>Consulta y administra el estado de tus comprobantes ante el SRI.</flux:subheading>
        </div>
        <flux:badge color="green" icon="signal">SRI en línea</flux:badge>
    </div>

    <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_14rem]">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
            placeholder="Buscar por venta, cliente, identificación o clave..." />
        <flux:select wire:model.live="statusFilter">
            <flux:select.option value="">Todos los estados</flux:select.option>
            <flux:select.option value="AUTORIZADO">Autorizado</flux:select.option>
            <flux:select.option value="RECIBIDA">Recibida</flux:select.option>
            <flux:select.option value="PENDING">Pendiente</flux:select.option>
            <flux:select.option value="DEVUELTA">Devuelta</flux:select.option>
            <flux:select.option value="ERROR">Error</flux:select.option>
        </flux:select>
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="min-w-262.5 divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800/80">
                    <tr>
                        <th class="w-24 px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Venta</th>
                        <th class="w-32 px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Fecha</th>
                        <th class="w-56 px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Cliente</th>
                        <th class="w-28 px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500">Total</th>
                        <th class="w-80 px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Clave de acceso</th>
                        <th class="w-32 px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Estado</th>
                        <th class="w-36 px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($sales as $sale)
                        <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
                            <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-zinc-900 dark:text-white">#{{ $sale->id }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                <div>{{ optional($sale->sale_date)->format('d/m/Y') }}</div>
                                <div class="text-xs text-zinc-500">{{ optional($sale->sale_date)->format('H:i') }}</div>
                            </td>
                            <td class="px-5 py-4 text-sm">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $sale->customer->name ?? 'Consumidor Final' }}</div>
                                <div class="text-xs text-zinc-500">{{ $sale->customer->identification ?? 'S/I' }}</div>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-emerald-600 dark:text-emerald-400">${{ number_format($sale->total, 2) }}</td>
                            <td class="w-80 px-5 py-4 text-xs font-mono text-zinc-500">
                                @if($sale->sri_access_key)
                                    <span class="block break-all leading-5">{{ $sale->sri_access_key }}</span>
                                @else
                                    <span class="text-zinc-400">Sin generar</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                @if($sale->sri_status === 'AUTORIZADO')
                                    <flux:badge color="green">Autorizado</flux:badge>
                                @elseif($sale->sri_status === 'RECIBIDA')
                                    <flux:badge color="blue">Recibida</flux:badge>
                                @elseif(in_array($sale->sri_status, ['DEVUELTA', 'NO AUTORIZADO']))
                                    <flux:badge color="red">Devuelta</flux:badge>
                                @elseif($sale->sri_status === 'ERROR')
                                    <flux:badge color="amber">Error</flux:badge>
                                @else
                                    <flux:badge color="zinc">Pendiente</flux:badge>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                @if(in_array($sale->sri_status, ['DEVUELTA', 'NO AUTORIZADO', 'ERROR', 'PENDING', 'RECIBIDA', 'EN PROCESO', null]))
                                    <flux:button wire:click="reemitirSri({{ $sale->id }})" wire:loading.attr="disabled"
                                        wire:target="reemitirSri({{ $sale->id }})" variant="ghost" size="sm" icon="arrow-path">Reenviar</flux:button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-zinc-500">No se encontraron comprobantes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
            <div class="flex flex-col gap-3 border-t border-zinc-200 px-5 py-4 text-sm sm:flex-row sm:items-center sm:justify-between dark:border-zinc-700">
                <p class="text-zinc-500 dark:text-zinc-400">
                    Mostrando <span class="font-medium text-zinc-700 dark:text-zinc-200">{{ $sales->firstItem() }}</span>
                    a <span class="font-medium text-zinc-700 dark:text-zinc-200">{{ $sales->lastItem() }}</span>
                    de <span class="font-medium text-zinc-700 dark:text-zinc-200">{{ $sales->total() }}</span> comprobantes
                </p>

                <nav class="flex items-center gap-1" aria-label="Paginación">
                    <flux:button wire:click="previousPage" wire:loading.attr="disabled" :disabled="$sales->onFirstPage()"
                        variant="ghost" size="sm" icon="chevron-left" aria-label="Página anterior" />

                    @for($page = 1; $page <= $sales->lastPage(); $page++)
                        @if($page === $sales->currentPage())
                            <flux:button variant="primary" size="sm">{{ $page }}</flux:button>
                        @else
                            <flux:button wire:click="gotoPage({{ $page }})" variant="ghost" size="sm">{{ $page }}</flux:button>
                        @endif
                    @endfor

                    <flux:button wire:click="nextPage" wire:loading.attr="disabled" :disabled="!$sales->hasMorePages()"
                        variant="ghost" size="sm" icon="chevron-right" aria-label="Página siguiente" />
                </nav>
            </div>
        @endif
    </div>
</div>
