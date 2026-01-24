<div class="p-6">
    <div class="mb-6">
        <flux:heading size="xl">Mi Empresa</flux:heading>
        <flux:subheading>Configura la información legal y de contacto de tu negocio.</flux:subheading>
    </div>

    <div class="max-w-3xl">
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm p-6">
            <form wire:submit="save" class="space-y-6">
                
                <div class="flex flex-col sm:flex-row items-center gap-6 p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <div class="relative shrink-0">
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" class="h-24 w-24 object-cover rounded-lg border-2 border-white dark:border-zinc-800 shadow-sm">
                        @elseif($current_logo)
                            <img src="{{ asset('storage/' . $current_logo) }}" class="h-24 w-24 object-cover rounded-lg border-2 border-white dark:border-zinc-800 shadow-sm">
                        @else
                            <div class="h-24 w-24 bg-zinc-200 dark:bg-zinc-800 rounded-lg flex flex-col items-center justify-center text-zinc-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mb-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <span class="text-[10px] uppercase font-bold text-center">Sin Logo</span>
                            </div>
                        @endif
                        
                        <div wire:loading wire:target="logo" class="absolute inset-0 bg-white/70 dark:bg-zinc-900/70 flex items-center justify-center rounded-lg">
                            <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="flex-1 space-y-3 w-full sm:w-auto">
                        <flux:label>Logo de la empresa</flux:label>
                        <input type="file" wire:model="logo" id="logo_input" class="hidden" accept="image/*" />
                        <div class="flex gap-2">
                            <label for="logo_input" class="inline-flex items-center px-4 py-2 bg-white dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 rounded-md font-semibold text-xs text-zinc-700 dark:text-zinc-200 uppercase tracking-widest shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                                Subir nueva imagen
                            </label>
                        </div>
                        <flux:error name="logo" />
                        <p class="text-xs text-zinc-500">Formatos: PNG, JPG (Máx. 1MB)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input label="Nombre / Razón Social" wire:model="name" placeholder="Nombre de tu empresa" />
                    
                    <flux:input label="RUC / Identificación" wire:model="ruc" placeholder="Ej: 1790000000001" />

                    <flux:input type="email" label="Correo Electrónico" wire:model="email" placeholder="correo@empresa.com" />

                    <flux:input label="Teléfono de contacto" wire:model="phone" placeholder="Ej: 0999999999" />

                    <div class="md:col-span-2">
                        <flux:textarea label="Dirección Física" wire:model="address" placeholder="Calle principal, número y ciudad..." rows="3" />
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">Guardar cambios</span>
                        <span wire:loading wire:target="save">Procesando...</span>
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>