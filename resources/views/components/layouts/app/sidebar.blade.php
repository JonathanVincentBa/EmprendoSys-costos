<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    {{-- SweetAlert2 centralizado para toda la App --}}
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

        {{-- ===========================================================
             1. SIDEBAR (Escritorio y Tablet)
             =========================================================== --}}
        <flux:sidebar sticky collapsible="mobile"
            class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                {{-- BLOQUE: PLATAFORMA --}}
                <flux:sidebar.group :heading="__('Plataforma')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" wire:navigate>Dashboard
                    </flux:sidebar.item>
                </flux:sidebar.group>

                {{-- BLOQUE: ADMINISTRACIÓN (Control de Acceso) --}}
                @can('ver administracion')
                    <flux:sidebar.group :heading="__('Administración')" class="grid">
                        @can('ver empresas')
                            <flux:sidebar.item icon="building-office" :href="route('admin.companies')" wire:navigate>
                                {{ auth()->user()->hasRole('super-admin') ? 'Empresas' : 'Mi Empresa' }}
                            </flux:sidebar.item>
                        @endcan
                        @can('ver usuarios')
                            <flux:sidebar.item icon="users" :href="route('users.index')" wire:navigate>Usuarios
                            </flux:sidebar.item>
                        @endcan
                        @can('ver roles')
                            <flux:sidebar.item icon="shield-check" :href="route('admin.roles')" wire:navigate>Roles y Permisos
                            </flux:sidebar.item>
                        @endcan
                    </flux:sidebar.group>
                @endcan

                {{-- BLOQUE: PRODUCCIÓN (Materias Primas y Costos) --}}
                @can('ver catalogos')
                    <flux:sidebar.group :heading="__('Producción y Costos')" class="grid">
                        <flux:sidebar.item icon="beaker" :href="route('raw-materials.index')" wire:navigate>Materias Primas
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="archive-box" :href="route('packaging.index')" wire:navigate>
                            Insumos/Empaques</flux:sidebar.item>
                        <flux:sidebar.item icon="circle-stack" :href="route('supplies.index')" wire:navigate>Suministros
                        </flux:sidebar.item>
                        <flux:separator mode="horizontal" class="my-2" />
                        <flux:sidebar.item icon="clipboard-document-list" :href="route('products.index')" wire:navigate>
                            Productos/Recetas</flux:sidebar.item>
                        <flux:sidebar.item icon="wrench-screwdriver" :href="route('labor-costs.index')" wire:navigate>Mano
                            de Obra</flux:sidebar.item>
                        <flux:sidebar.item icon="presentation-chart-line" :href="route('overhead-config.index')"
                            wire:navigate>Gastos Indirectos</flux:sidebar.item>
                    </flux:sidebar.group>
                @endcan

                {{-- BLOQUE: COMERCIAL (Ventas y Clientes) --}}
                @can('ver ventas')
                    <flux:sidebar.group :heading="__('Comercial')" class="grid">
                        <flux:sidebar.item icon="shopping-cart" :href="route('sales.pos')" wire:navigate>Punto de Venta
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="user-group" :href="route('clients.index')" wire:navigate>Clientes
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="document-text" :href="route('invoices.index')" wire:navigate>Facturación
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endcan
            </flux:sidebar.nav>

            <flux:spacer />

            {{-- FOOTER DEL SIDEBAR (Perfil en Desktop) --}}
            <flux:dropdown position="top" align="start" class="max-lg:hidden">
                <flux:profile :name="auth()->user()->name" :initials="auth()->user()->name[0]"
                    class="cursor-pointer" />
                <flux:menu>
                    <flux:menu.item icon="arrow-right-start-on-rectangle"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="cursor-pointer">
                        Cerrar Sesión
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        {{-- ===========================================================
             2. NAVBAR MÓVIL (Solo visible en pantallas pequeñas)
             =========================================================== --}}
        <flux:header class="lg:hidden border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle icon="bars-2" />
            <flux:spacer />
            <flux:dropdown aling="end">
                <flux:profile :initials="auth()->user()->name[0]" class="cursor-pointer" />
                <flux:menu>
                    <flux:menu.item icon="arrow-right-start-on-rectangle"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Cerrar Sesión
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{-- ===========================================================
             3. CONTENIDO PRINCIPAL
             =========================================================== --}}
        <main class="main-content flex-1 p-4 lg:p-8">
            {{ $slot }}
        </main>

    </div>

    {{-- Formulario Logout --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

    @fluxScripts

    {{-- ===========================================================
         4. LÓGICA GLOBAL DE SWEETALERT2
         =========================================================== --}}
    <script>
        // Alertas Toast (Éxito, Error, Info)
        window.addEventListener('swal', event => {
            const data = event.detail[0] || event.detail;
            Swal.fire({
                title: data.message || 'Proceso exitoso',
                icon: data.type || 'success',
                timer: 3000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        });

        // Alertas de Confirmación (Eliminar)
        window.addEventListener('swal:confirm', event => {
            const data = event.detail[0] || event.detail;
            Swal.fire({
                title: data.title || '¿Estás seguro?',
                text: data.text || 'No podrás revertir esto',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch(data.nextAction, {
                        id: data.id
                    });
                }
            });
        });
    </script>
</body>

</html>
