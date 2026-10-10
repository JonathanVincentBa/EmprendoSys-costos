<x-layouts.app :title="config('app.name') . ' - Dashboard'">
    <div class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-6 p-4 sm:p-6">
        <div class="relative overflow-hidden rounded-2xl bg-indigo-950 px-6 py-7 text-white shadow-lg shadow-indigo-950/10 sm:px-8">
            <div aria-hidden="true" class="absolute -right-10 -top-24 h-64 w-64 rounded-full bg-emerald-400/20 blur-3xl"></div>
            <div class="relative">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-emerald-300">EmprendoSys · Resumen</p>
                <h1 class="mt-2 text-2xl font-black tracking-tight sm:text-3xl">Panel de control</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-indigo-100/80">Revisa tus ventas y mantén a la vista los productos que necesitan reposición.</p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="relative overflow-hidden rounded-2xl border border-indigo-100 bg-white p-6 shadow-sm">
                <div aria-hidden="true" class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-indigo-50"></div>
                <div class="relative flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v20m5-15H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </span>
                    <div>
                        <h2 class="text-xs font-black uppercase tracking-wider text-zinc-500">Ventas de hoy</h2>
                        <p class="mt-1 text-xs font-medium text-zinc-400">Ventas completadas</p>
                    </div>
                </div>
                <p class="relative mt-6 text-3xl font-black tracking-tight text-indigo-950">
                    ${{ number_format($todaySales ?? 0, 2) }}
                </p>
            </div>

            <div class="flex items-center gap-4 rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9ZM3.5 7.8 12 12l8.5-4.2M12 12v9"/></svg>
                </span>
                <div>
                    <p class="text-sm font-extrabold text-indigo-950">Inventario conectado</p>
                    <p class="mt-1 text-sm leading-5 text-zinc-500">Consulta existencias y alertas de stock bajo en tu operación.</p>
                </div>
            </div>

            <div class="flex items-center gap-4 rounded-2xl border border-amber-100 bg-white p-6 shadow-sm">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M9 13h6m-6 4h6"/></svg>
                </span>
                <div>
                    <p class="text-sm font-extrabold text-indigo-950">Facturación electrónica</p>
                    <p class="mt-1 text-sm leading-5 text-zinc-500">Administra el estado de tus comprobantes electrónicos.</p>
                </div>
            </div>
        </div>

        <section class="overflow-hidden rounded-2xl border border-indigo-100 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-zinc-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 4h.01M10.3 4.8 2.9 17.6A1.6 1.6 0 0 0 4.3 20h15.4a1.6 1.6 0 0 0 1.4-2.4L13.7 4.8a2 2 0 0 0-3.4 0Z"/></svg>
                    </span>
                    <div>
                        <h2 class="text-base font-extrabold text-indigo-950">Alertas de inventario bajo</h2>
                        <p class="mt-0.5 text-xs text-zinc-500">Productos que requieren atención</p>
                    </div>
                </div>
                @if ($lowStockProducts->isNotEmpty())
                    <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700">
                        <span class="h-2 w-2 rounded-full bg-red-500"></span>
                        {{ $lowStockProducts->count() }} requieren atención
                    </span>
                @endif
            </div>

            @if ($lowStockProducts->isEmpty())
                <div class="flex flex-col items-center justify-center px-6 py-14 text-center">
                    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg>
                    </span>
                    <p class="mt-4 text-sm font-bold text-indigo-950">Todo el inventario está en niveles óptimos.</p>
                    <p class="mt-1 text-xs text-zinc-500">No hay productos por debajo de su stock mínimo.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-100">
                        <thead class="bg-zinc-50/80">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-[11px] font-black uppercase tracking-wider text-zinc-500">Producto</th>
                                <th class="px-6 py-3.5 text-center text-[11px] font-black uppercase tracking-wider text-zinc-500">Empaque</th>
                                <th class="px-6 py-3.5 text-center text-[11px] font-black uppercase tracking-wider text-zinc-500">Stock actual</th>
                                <th class="px-6 py-3.5 text-center text-[11px] font-black uppercase tracking-wider text-zinc-500">Mínimo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 bg-white">
                            @foreach ($lowStockProducts as $product)
                                <tr class="transition-colors hover:bg-indigo-50/40">
                                    <td class="px-6 py-4 text-sm font-bold text-zinc-800">
                                        {{ $product->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs font-semibold uppercase text-zinc-500">
                                        {{ $product->packaging_type }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm">
                                        <span class="inline-flex min-w-10 justify-center rounded-lg bg-red-50 px-2.5 py-1 font-black text-red-700">{{ $product->current_stock }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-semibold text-zinc-500">
                                        {{ $product->minimum_stock_level }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
