{{-- dashboard-vendedor.blade.php --}}
<x-layouts.app :title="config('app.name') . ' - Dashboard'">
    <div class="p-6 space-y-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">¡Hola, {{ auth()->user()->name }}!</flux:heading>
                <flux:text class="text-zinc-500">Aquí tienes el resumen de tu actividad de hoy.</flux:text>
            </div>
            <flux:button :href="route('sales.pos')" variant="primary" icon="shopping-cart">
                Nuevo Pedido / POS
            </flux:button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Card: Ventas del Día --}}
            <flux:card class="flex flex-col justify-between p-6">
                <div class="flex justify-between items-start">
                    <flux:text class="text-zinc-500 font-medium">Ventas de hoy</flux:text>
                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <flux:icon.banknotes class="text-green-600" />
                    </div>
                </div>
                <div class="mt-4">
                    <flux:heading size="xl" class="font-bold text-2xl">$
                        {{ number_format($stats['ventas_hoy'], 2) }}</flux:heading>
                    <flux:badge color="green" size="sm" class="mt-2">
                        {{ $stats['cantidad_ventas'] }} ventas realizadas
                    </flux:badge>
                </div>
            </flux:card>

            {{-- Card: Clientes --}}
            <flux:card class="flex flex-col justify-between p-6">
                <div class="flex justify-between items-start">
                    <flux:text class="text-zinc-500 font-medium">Nuevos Clientes</flux:text>
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <flux:icon.users class="text-blue-600" />
                    </div>
                </div>
                <div class="mt-4">
                    <flux:heading size="xl" class="font-bold text-2xl">{{ $stats['clientes_nuevos'] }}
                    </flux:heading>
                    <flux:text size="sm" class="text-zinc-400">Registrados el día de hoy</flux:text>
                </div>
            </flux:card>

            {{-- Card: Acceso Rápido --}}
            <flux:card
                class="bg-zinc-50 dark:bg-zinc-900 border-dashed border-2 flex flex-col items-center justify-center p-6 text-center">
                <flux:text class="mb-4">¿Necesitas registrar una venta rápida?</flux:text>
                <flux:button :href="route('sales.pos')" variant="filled" class="w-full">
                    Abrir Punto de Venta
                </flux:button>
            </flux:card>
        </div>

        {{-- Tabla de Ventas Recientes --}}
        <flux:card>
            <div class="mb-4">
                <flux:heading size="lg">Tus últimas ventas</flux:heading>
                <flux:text size="sm">Listado de las transacciones más recientes.</flux:text>
            </div>

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Hora</flux:table.column>
                    <flux:table.column>Cliente</flux:table.column>
                    <flux:table.column align="end">Total</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($stats['ultimas_ventas'] as $venta)
                        <flux:table.row>
                            <flux:table.cell class="font-medium">{{ $venta->created_at->format('H:i') }}
                            </flux:table.cell>
                            <flux:table.cell>{{ $venta->customer->name ?? 'Consumidor Final' }}</flux:table.cell>
                            <flux:table.cell align="end" class="font-bold text-zinc-900 dark:text-white">
                                ${{ number_format($venta->total, 2) }}
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="3" class="text-center py-8 text-zinc-400">
                                Aún no has realizado ventas hoy.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</x-layouts.app>