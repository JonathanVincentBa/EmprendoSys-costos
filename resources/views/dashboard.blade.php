<x-layouts.app :title="config('app.name') . ' - Dashboard'">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Tarjetas superiores (puedes mantenerlas o reemplazar una) -->
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-4">
                <h3 class="font-semibold text-gray-800 dark:text-neutral-200">🧮 Costo de Producción</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">Calcula el costo real de tus productos artesanales.</p>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>

        <!-- Sección principal: Calculadora de costos -->
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
            <h2 class="mb-4 text-lg font-bold text-gray-800 dark:text-neutral-200">📊 Detalle de Costo por Producto</h2>
            @livewire('product-cost-calculator')
        </div>
    </div>
</x-layouts.app>
