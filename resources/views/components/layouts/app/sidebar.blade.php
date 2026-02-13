<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Ajuste para evitar que el contenido se pegue al sidebar en pantallas grandes */
        @media (min-width: 1024px) {
            .main-content {
                margin-left: 0;
                /* Flux ya maneja el espacio si el contenedor es flex */
            }
        }
    </style>
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">

    {{-- Contenedor Principal Flex --}}
    <div class="flex min-h-screen w-full">

        {{-- Sidebar --}}
        <flux:sidebar sticky collapsible="mobile"
            class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Plataforma')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" wire:navigate>Dashboard
                    </flux:sidebar.item>

                    {{-- Solo Super Admin ve Gestión de Empresas --}}
                    @can('ver empresas')
                        <flux:sidebar.item icon="building-office" :href="route('admin.companies')" wire:navigate>
                            Gestión Empresas
                        </flux:sidebar.item>
                    @endcan

                    {{-- Mi Empresa: Visible para Super Admin (bypass) o para quien tenga el permiso --}}
                    @if (auth()->user()->hasRole('super-admin') || auth()->user()->can('editar mi empresa'))
                        <flux:sidebar.item icon="cog-6-tooth" :href="route('my.company')" wire:navigate>
                            Mi Empresa
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Costos de Producción')" class="grid">
                    <flux:sidebar.item icon="sparkles" :href="route('product.wizard')"
                        :current="request()->routeIs('product.wizard')" wire:navigate>
                        Asistente Maestro
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="cube" :href="route('products.index')"
                        :current="request()->routeIs('products.index')" wire:navigate>
                        Productos
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Ventas')" class="grid">
                    <flux:sidebar.item icon="shopping-cart" :href="route('sales.pos')"
                        :current="request()->routeIs('sales.pos')" wire:navigate>
                        Punto de Venta
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('clients.index')"
                        :current="request()->routeIs('clients.index')" wire:navigate>
                        Clientes
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Configuración')" class="grid">
                    <flux:sidebar.item icon="cog-6-tooth" :href="route('my.company')"
                        :current="request()->routeIs('my.company')" wire:navigate>
                        Mi Empresa
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="beaker" :href="route('raw-materials.index')">Materias Primas
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="archive-box" :href="route('packaging.index')">Empaques</flux:sidebar.item>
                    <flux:sidebar.item icon="bolt" :href="route('supplies.index')">Suministros</flux:sidebar.item>
                    <flux:sidebar.item icon="calculator" :href="route('overhead-config.index')">Costos Indirectos
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-group" :href="route('labor-costs.index')">Mano de Obra
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:dropdown position="top" align="start" class="max-lg:hidden">
                <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()" />
                <flux:menu>
                    <flux:menu.item icon="arrow-right-start-on-rectangle" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Cerrar Sesión
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        {{-- Lado derecho del Sidebar --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Header Móvil --}}
            <flux:header
                class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 px-4 flex items-center h-16 sticky top-0 z-10">
                <flux:sidebar.toggle class="-ms-2" icon="bars-3" />
                <flux:spacer />
                <flux:profile :initials="auth()->user()->initials()" />
            </flux:header>

            {{-- CONTENIDO PRINCIPAL: Con padding suficiente para no montarse --}}
            <main class="flex-1 p-6 lg:p-10 w-full mx-auto max-w-7xl">
                {{ $slot }}
            </main>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

    @fluxScripts

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('swal', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                Swal.fire({
                    position: 'top-end',
                    icon: data.type || 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2000,
                    toast: true
                });
            });
        });
    </script>
</body>

</html>
