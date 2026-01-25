<div class="p-6 space-y-6">
    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-bold text-zinc-800 dark:text-white flex items-center gap-2">
                <span class="bg-blue-600 p-1 rounded text-white text-xs">ID</span> 
                Información del Cliente
            </h2>
            @if($selectedCustomer)
                <button wire:click="$set('selectedCustomer', null)" class="text-[10px] font-black text-red-500 uppercase hover:underline">✕ Cambiar Cliente</button>
            @endif
        </div>

        @if(!$selectedCustomer)
            <div class="relative">
                <input wire:model.live="customerSearch" type="text" placeholder="Buscar cliente..." class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 p-2 text-sm dark:text-white">
                @if(!empty($customers))
                    <div class="absolute z-50 w-full bg-white dark:bg-zinc-800 border dark:border-zinc-700 rounded-lg shadow-xl mt-1">
                        @foreach($customers as $c)
                            <button wire:click="selectCustomer({{ $c->id }})" class="w-full text-left p-3 hover:bg-zinc-100 dark:hover:bg-zinc-700 border-b last:border-0 dark:text-white">
                                <p class="font-bold text-sm">{{ $c->name }}</p>
                                <p class="text-xs opacity-60">{{ $c->identification }}</p>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-zinc-50 dark:bg-zinc-800/50 p-3 rounded-lg border border-zinc-100 dark:border-zinc-700">
                <div><label class="text-[9px] uppercase text-zinc-400 font-bold">Nombre</label><p class="text-sm font-bold dark:text-white">{{ $selectedCustomer['name'] }}</p></div>
                <div><label class="text-[9px] uppercase text-zinc-400 font-bold">Identificación</label><p class="text-sm font-mono dark:text-blue-400">{{ $selectedCustomer['identification'] }}</p></div>
                <div><label class="text-[9px] uppercase text-zinc-400 font-bold">Email</label><p class="text-sm dark:text-zinc-300">{{ $selectedCustomer['email'] ?? '-' }}</p></div>
                <div><label class="text-[9px] uppercase text-zinc-400 font-bold">Teléfono</label><p class="text-sm dark:text-zinc-300">{{ $selectedCustomer['phone'] ?? '-' }}</p></div>
            </div>
        @endif
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 shadow-sm">
        <div class="flex items-end gap-3 w-full">
            <div class="flex-1 relative">
                <label class="text-[10px] font-bold text-zinc-400 uppercase block mb-1 ml-1">Producto</label>
                <input wire:model.live="productSearch" type="text" placeholder="Escriba el nombre..." class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 p-2.5 dark:text-white">
                
                @if(!empty($products))
                    <div class="absolute z-50 w-full bg-white dark:bg-zinc-800 border dark:border-zinc-700 rounded-lg shadow-2xl mt-1">
                        @foreach($products as $p)
                            <button wire:click="selectProduct({{ $p->id }})" class="w-full text-left p-3 hover:bg-indigo-600 hover:text-white border-b last:border-0 flex justify-between items-center dark:text-white transition-colors">
                                <span class="font-bold">{{ $p->name }}</span>
                                <span class="text-[10px] px-2 py-0.5 bg-green-100 text-green-800 rounded font-black italic">STOCK: {{ $p->current_stock }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="w-24">
                <label class="text-[10px] font-bold text-zinc-400 uppercase block mb-1 text-center">Cant.</label>
                <input type="number" wire:model="quantity" wire:keydown.enter="addItem" min="1" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 p-2.5 text-center font-bold dark:text-white">
            </div>

            <div class="w-32">
                <button wire:click="addItem" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-2.5 rounded-lg shadow-md uppercase text-[11px] transition-all active:scale-95">
                    + Añadir
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-sm">
        <table class="w-full text-left">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-[10px] font-black text-zinc-400 uppercase">
                <tr>
                    <th class="px-6 py-4">Descripción del Producto</th>
                    <th class="px-6 py-4 text-center">Cant.</th>
                    <th class="px-6 py-4 text-center">Total</th>
                    <th class="px-6 py-4 text-right">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-zinc-800">
                @forelse($items as $index => $item)
                <tr class="text-sm dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/20">
                    <td class="px-6 py-4 font-medium">{{ $item['name'] }}</td>
                    <td class="px-6 py-4 text-center">{{ $item['quantity'] }}</td>
                    <td class="px-6 py-4 text-center font-bold text-blue-600 dark:text-blue-400">${{ number_format($item['subtotal'], 2) }}</td>
                    <td class="px-6 py-4 text-right">
                        <button wire:click="removeItem({{ $index }})" class="text-red-500 font-bold text-xs uppercase hover:underline">Quitar</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-zinc-400 italic font-medium">No hay productos en el detalle de la venta.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-6 bg-zinc-900 dark:bg-black border-t border-zinc-800 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-4">
                <span class="text-zinc-400 uppercase font-black text-xs tracking-widest">Total a pagar:</span>
                <span class="text-4xl font-black text-white font-mono">${{ number_format(collect($items)->sum('subtotal'), 2) }}</span>
            </div>
            
            <button wire:click="saveInvoice" wire:loading.attr="disabled" 
                    class="w-full md:w-auto px-10 py-4 bg-emerald-500 hover:bg-emerald-600 text-zinc-950 font-black rounded-xl shadow-[0_0_20px_rgba(16,185,129,0.3)] uppercase text-sm flex items-center justify-center gap-3 transition-all hover:-translate-y-1 active:translate-y-0">
                <span wire:loading.remove>✅ CONFIRMAR Y GUARDAR VENTA</span>
                <span wire:loading>⌛ PROCESANDO...</span>
            </button>
        </div>
    </div>
</div>