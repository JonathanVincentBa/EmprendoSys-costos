<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @media (min-width: 1024px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">

    <div class="flex min-h-screen w-full">

        {{-- Sidebar --}}
        <flux:sidebar sticky collapsible="mobile"
            class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                {{-- Plataforma --}}
                <flux:sidebar.group :heading="__('Plataforma')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" wire:navigate>Dashboard
                    </flux:sidebar.item>

                    @role('admin')
                        <flux:sidebar.item icon="building-office" :href="route('admin.companies')" wire:navigate>Gestión
                            Empresas</flux:sidebar.item>
                        <flux:sidebar.item icon="users" :href="route('admin.users')" wire:navigate>Usuarios
                        </flux:sidebar.item>
                    @endrole

                    {{-- Acceso a Ventas para Admin y Vendedor --}}
                    @role('admin|vendedor')
                        <flux:sidebar.item icon="shopping-cart" :href="route('sales.pos')" wire:navigate>Punto de Venta
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="user-group" :href="route('clients.index')" wire:navigate>Clientes
                        </flux:sidebar.item>
                    @endrole
                </flux:sidebar.group>

                {{-- Catálogos (Solo Admin) --}}
                @role('admin')
                    <flux:sidebar.group heading="Catálogos">
                        <flux:sidebar.item icon="cube" :href="route('products.index')" wire:navigate>Productos
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="document-text" :href="route('raw-materials.index')" wire:navigate>Materias
                            Primas</flux:sidebar.item>
                        <flux:sidebar.item icon="shopping-bag" :href="route('packaging.index')" wire:navigate>Empaques
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="beaker" :href="route('supplies.index')" wire:navigate>Insumos
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="cog" :href="route('overhead-config.index')" wire:navigate>Gastos
                            Indirectos</flux:sidebar.item>
                        <flux:sidebar.item icon="currency-dollar" :href="route('labor-costs.index')" wire:navigate>Costos
                            Mano de Obra</flux:sidebar.item>
                    </flux:sidebar.group>
                @endrole
            </flux:sidebar.nav>

            <flux:spacer />

            {{-- Info del usuario al fondo --}}
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()" class="w-full" />
            </div>
        </flux:sidebar>

        <div class="flex-1 flex flex-col min-w-0">
            {{-- Header Móvil --}}
            <flux:header
                class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 px-4 flex items-center h-16 sticky top-0 z-10">
                <flux:sidebar.toggle class="-ms-2" icon="bars-3" />
                <flux:spacer />
                <flux:profile :initials="auth()->user()->initials() ?? 'U'" />
            </flux:header>

            <main class="flex-1 p-6 lg:p-10 w-full mx-auto max-w-7xl">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Formulario de Logout (Indispensable) --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>

</html>
