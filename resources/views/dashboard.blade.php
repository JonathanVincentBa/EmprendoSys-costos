<x-layouts.app :title="config('app.name') . ' - Dashboard'">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xl">💰</span>
                    <h3 class="font-bold text-gray-800 dark:text-neutral-200 uppercase tracking-wider text-sm">Ventas de
                        Hoy</h3>
                </div>
                <p class="text-3xl font-black text-neutral-900 dark:text-white mt-4">
                    ${{ number_format($todaySales ?? 0, 2) }}
                </p>
                <p class="text-xs text-gray-500 mt-2 italic">* Solo ventas completadas</p>
            </div>

            <div
                class="relative aspect-video rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white/50 dark:bg-neutral-800/50 flex items-center justify-center">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/10" />
                <span class="text-gray-400 font-medium">Próxima Métrica</span>
            </div>

            <div
                class="relative aspect-video rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white/50 dark:bg-neutral-800/50 flex items-center justify-center">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/10" />
                <span class="text-gray-400 font-medium">Próxima Métrica</span>
            </div>
        </div>

        <div
            class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-lg">📢</span>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-neutral-200">Alertas de Inventario Bajo</h2>
                </div>
                @if ($lowStockProducts->isNotEmpty())
                    <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full uppercase">Acción
                        Requerida</span>
                @endif
            </div>

            @if ($lowStockProducts->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                    <span class="text-4xl mb-2">✅</span>
                    <p>Todo el inventario está en niveles óptimos.</p>
                </div>
            @else
                <div class="overflow-hidden rounded-lg border border-neutral-100 dark:border-neutral-700">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                        <thead class="bg-neutral-50 dark:bg-neutral-900/50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Producto</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Empaque</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Stock Actual</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Mínimo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700 bg-white dark:bg-transparent">
                            @foreach ($lowStockProducts as $product)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-900/20 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-800 dark:text-neutral-200">
                                        {{ $product->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-500 uppercase">
                                        {{ $product->packaging_type }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <span
                                            class="font-black text-red-600 dark:text-red-400">{{ $product->current_stock }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-400 font-medium">
                                        {{ $product->minimum_stock_level }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
