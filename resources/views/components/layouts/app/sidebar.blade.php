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

                    @can('ver empresas')
                        <flux:sidebar.item icon="building-office" :href="route('admin.companies')" wire:navigate>Gestión
                            Empresas</flux:sidebar.item>
                    @endcan

                    @if (auth()->user()->hasRole('super-admin') || auth()->user()->can('editar mi empresa'))
                        <flux:sidebar.item icon="cog-6-tooth" :href="route('my.company')" wire:navigate>Mi Empresa
                        </flux:sidebar.item>
                    @endif

                    @can('ver usuarios')
                        <flux:sidebar.item icon="users" :href="route('admin.users')" wire:navigate>Gestionar Usuarios
                        </flux:sidebar.item>
                    @endcan

                    {{-- NUEVO: Opción exclusiva para Super-Admin --}}
                    @if (auth()->user()->hasRole('super-admin'))
                        <flux:sidebar.item icon="shield-check" :href="route('admin.roles')" wire:navigate>Roles y
                            Permisos</flux:sidebar.item>
                    @endif
                </flux:sidebar.group>

                {{-- Costos de Producción --}}
                <flux:sidebar.group :heading="__('Costos de Producción')" class="grid">
                    <flux:sidebar.item icon="sparkles" :href="route('product.wizard')" wire:navigate>Asistente Maestro
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="cube" :href="route('products.index')" wire:navigate>Productos
                    </flux:sidebar.item>
                </flux:sidebar.group>

                {{-- Ventas --}}
                <flux:sidebar.group :heading="__('Ventas')" class="grid">
                    <flux:sidebar.item icon="shopping-cart" :href="route('sales.pos')" wire:navigate>Punto de Venta
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-group" :href="route('clients.index')" wire:navigate>Clientes
                    </flux:sidebar.item>
                </flux:sidebar.group>

                {{-- Configuración --}}
                <flux:sidebar.group :heading="__('Configuración')" class="grid">
                    <flux:sidebar.item icon="beaker" :href="route('raw-materials.index')" wire:navigate>Materias
                        Primas</flux:sidebar.item>
                    <flux:sidebar.item icon="archive-box" :href="route('packaging.index')" wire:navigate>Empaques
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="bolt" :href="route('supplies.index')" wire:navigate>Suministros
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="calculator" :href="route('overhead-config.index')" wire:navigate>Costos
                        Indirectos</flux:sidebar.item>
                    <flux:sidebar.item icon="identification" :href="route('labor-costs.index')" wire:navigate>Mano de
                        Obra</flux:sidebar.item>
                </flux:sidebar.group>

                {{-- BOTÓN CERRAR SESIÓN --}}
                <flux:sidebar.group class="mt-4">
                    <flux:sidebar.item icon="arrow-right-start-on-rectangle" variant="danger" class="cursor-pointer"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Cerrar Sesión
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()" class="w-full" />
            </div>
        </flux:sidebar>

        <div class="flex-1 flex flex-col min-w-0">
            <flux:header
                class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 px-4 flex items-center h-16 sticky top-0 z-10">
                <flux:sidebar.toggle class="-ms-2" icon="bars-3" />
                <flux:spacer />
                <flux:profile :initials="auth()->user()->initials()" />
            </flux:header>

            <main class="flex-1 p-6 lg:p-10 w-full mx-auto max-w-7xl">
                {{ $slot }}
            </main>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    @fluxScripts

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 1. Alerta informativa (éxito, error, info)
        window.addEventListener('swal', event => {
            const data = event.detail[0] || event.detail; // Manejo de compatibilidad
            Swal.fire({
                title: data.message || 'Proceso exitoso',
                icon: data.type || 'success',
                timer: 3000,
                showConfirmButton: false
            });
        });

        // 2. Confirmación antes de eliminar
        window.addEventListener('swal:confirm', event => {
            const data = event.detail[0] || event.detail;
            Swal.fire({
                title: data.title || '¿Estás seguro?',
                text: data.text || 'No podrás revertir esto',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Llama al método del componente Livewire
                    Livewire.dispatch(data.nextAction, {
                        id: data.id
                    });
                }
            });
        });
    </script>
</body>

</html>
