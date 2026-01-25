<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group :heading="__('Costos de Producción')" class="grid">
                <flux:sidebar.item icon="sparkles" :href="route('product.wizard')"
                    :current="request()->routeIs('product.wizard')" wire:navigate>
                    {{ __('Asistente Maestro') }}
                </flux:sidebar.item>
                
                <flux:sidebar.item icon="document-text" :href="route('products.index')"
                    :current="request()->routeIs('products.*')" wire:navigate>
                    {{ __('Productos') }}
                </flux:sidebar.item>
                
            </flux:sidebar.group>

            <flux:sidebar.group :heading="__('Ventas')" class="grid">
                <flux:sidebar.item icon="shopping-bag" :href="route('sales.pos')"
                    :current="request()->routeIs('sales.*')" wire:navigate>
                    {{ __('Órdenes de Venta') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="user-group" :href="route('clients.index')"
                    :current="request()->routeIs('clients.*')" wire:navigate>
                    {{ __('Clientes') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="document-check" :href="route('invoices.index')"
                    :current="request()->routeIs('invoices.*')" wire:navigate>
                    {{ __('Facturación') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group :heading="__('Catálogos')" class="grid">
                <flux:sidebar.item icon="archive-box" :href="route('raw-materials.index')"
                    :current="request()->routeIs('raw-materials.*')" wire:navigate>
                    {{ __('Materias Primas') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="cube" :href="route('packaging.index')"
                    :current="request()->routeIs('packaging.*')" wire:navigate>
                    {{ __('Empaques') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="bolt" :href="route('supplies.index')"
                    :current="request()->routeIs('supplies.*')" wire:navigate>
                    {{ __('Suministros') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="cog-6-tooth" :href="route('overhead-config.index')"
                    :current="request()->routeIs('overhead-config.*')" wire:navigate>
                    {{ __('Gastos Indirectos') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="users" :href="route('labor-costs.index')"
                    :current="request()->routeIs('labor-costs.*')" wire:navigate>
                    {{ __('Mano de Obra') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group :heading="__('Configuración')" class="grid">
                <flux:sidebar.item icon="building-office" :href="route('company.edit')"
                    :current="request()->routeIs('company.edit')" wire:navigate>
                    {{ __('Mi Empresa') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="cog" :href="route('profile.edit')"
                    :current="request()->routeIs('profile.edit')" wire:navigate>
                    {{ __('Ajustes') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        <flux:sidebar.nav>
            <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit"
                target="_blank">
                {{ __('Repository') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire"
                target="_blank">
                {{ __('Documentation') }}
            </flux:sidebar.item>
        </flux:sidebar.nav>

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts

    <script>
        function noty(msg, icon = 'success') {
            Swal.fire({
                position: 'top-end',
                icon: icon,
                title: msg,
                showConfirmButton: false,
                timer: 2000,
                toast: true
            });
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('swal', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                noty(data.message, data.type || 'success');
            });
        });
    </script>
</body>

</html>