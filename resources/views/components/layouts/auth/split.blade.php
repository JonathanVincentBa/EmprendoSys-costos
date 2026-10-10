<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <meta name="theme-color" content="#1b2a34">
    </head>
    <body class="min-h-screen bg-[#edf0f2] font-sans antialiased">
        <div class="grid min-h-screen lg:grid-cols-[minmax(0,1.05fr)_minmax(30rem,0.95fr)]">
            <aside class="relative hidden overflow-hidden bg-indigo-950 text-white lg:flex lg:flex-col lg:justify-between">
                <div aria-hidden="true" class="absolute inset-0">
                    <div class="absolute -right-28 -top-32 h-[34rem] w-[34rem] rounded-full bg-indigo-700/50 blur-3xl"></div>
                    <div class="absolute -bottom-40 -left-20 h-[30rem] w-[30rem] rounded-full bg-emerald-500/20 blur-3xl"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.08)_1px,transparent_0)] bg-[size:28px_28px]"></div>
                </div>

                <a href="{{ route('home') }}" class="relative z-10 flex items-center gap-3 px-12 pt-10" wire:navigate>
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-400 text-lg font-black text-indigo-950 shadow-lg shadow-black/20">E</span>
                    <span class="text-2xl font-black tracking-tight">Emprendo<span class="text-emerald-300">Sys</span></span>
                </a>

                <div class="relative z-10 max-w-2xl px-12 pb-14 xl:px-16">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-300/25 bg-white/10 px-3.5 py-1.5 text-xs font-bold uppercase tracking-[0.16em] text-indigo-100">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        Producción · ventas · facturación
                    </div>
                    <h1 class="text-4xl font-black leading-tight tracking-tight xl:text-5xl">
                        Bienvenido de vuelta.
                        <span class="mt-2 block text-emerald-300">Tu negocio, bajo control.</span>
                    </h1>
                    <p class="mt-5 max-w-lg text-base leading-7 text-indigo-100/80">
                        Administra costos de producción, inventario, punto de venta y comprobantes electrónicos desde EmprendoSys.
                    </p>

                    <div class="mt-9 grid max-w-lg grid-cols-3 gap-3">
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-400/15 text-indigo-200">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M6 16V8m4 8V5m4 11v-5m4 5V7"/></svg>
                            </span>
                            <p class="mt-3 text-xs font-bold text-white">Costos</p>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-400/15 text-emerald-200">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9ZM3.5 7.8 12 12l8.5-4.2M12 12v9"/></svg>
                            </span>
                            <p class="mt-3 text-xs font-bold text-white">Inventario</p>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-400/15 text-amber-200">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M9 13h6m-6 4h6"/></svg>
                            </span>
                            <p class="mt-3 text-xs font-bold text-white">Facturación</p>
                        </div>
                    </div>
                    <p class="mt-10 text-xs font-medium text-indigo-200/60">© {{ date('Y') }} EmprendoSys · Gestión para producir, vender y crecer.</p>
                </div>
            </aside>

            <main class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-8 lg:px-12">
                <div class="w-full max-w-lg">
                    <a href="{{ route('home') }}" class="mb-8 inline-flex items-center gap-2.5 text-xl font-black tracking-tight text-indigo-950 lg:hidden" wire:navigate>
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-700 text-sm font-black text-white">E</span>
                        <span>Emprendo<span class="text-emerald-600">Sys</span></span>
                    </a>

                    <div class="mb-7">
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-emerald-700">Acceso seguro</p>
                        <h2 class="mt-2 text-3xl font-black tracking-tight text-indigo-950 sm:text-4xl">Bienvenido</h2>
                        <p class="mt-2 text-sm leading-6 text-zinc-600">Ingresa tus datos para continuar con la gestión de tu negocio.</p>
                    </div>

                    <div class="rounded-2xl border border-indigo-100 bg-white p-6 shadow-xl shadow-indigo-950/5 sm:p-8">
                        {{ $slot }}
                    </div>

                    <p class="mt-6 text-center text-xs font-medium text-zinc-500">
                        ¿Necesitas ayuda para acceder?
                        <a href="https://wa.me/593995789977?text=Hola%2C%20necesito%20ayuda%20para%20acceder%20a%20EmprendoSys"
                           target="_blank" rel="noopener noreferrer" class="font-bold text-indigo-700 transition hover:text-emerald-700">
                            Contáctanos por WhatsApp
                        </a>
                    </p>
                </div>
            </main>
        </div>
        @fluxScripts
    </body>
</html>
