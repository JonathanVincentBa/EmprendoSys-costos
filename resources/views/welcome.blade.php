<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="EmprendoSys ayuda a controlar costos de producción, inventario, ventas y facturación electrónica en un solo sistema.">
        <meta name="theme-color" content="#1b2a34">

        <title>EmprendoSys | Costos, ventas y facturación en un solo lugar</title>

        @vite('resources/css/app.css')
    </head>
    <body class="bg-[#edf0f2] font-sans text-zinc-800 antialiased">
        @php
            $whatsappUrl = 'https://wa.me/593995789977?text=' . rawurlencode('Hola, quiero conocer EmprendoSys y solicitar una demostración.');
        @endphp

        <header class="sticky top-0 z-50 border-b border-indigo-100/80 bg-white/95 backdrop-blur">
            <nav aria-label="Navegación principal" class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 text-xl font-black tracking-tight text-indigo-950">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-700 text-sm font-black text-white shadow-lg shadow-indigo-900/20">E</span>
                    <span>Emprendo<span class="text-emerald-600">Sys</span></span>
                </a>

                <div class="hidden items-center gap-8 text-sm font-semibold text-zinc-600 md:flex">
                    <a href="#beneficios" class="transition hover:text-indigo-700">Beneficios</a>
                    <a href="#como-funciona" class="transition hover:text-indigo-700">Cómo funciona</a>
                    <a href="#contacto" class="transition hover:text-indigo-700">Contacto</a>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-xl px-3 py-2 text-sm font-bold text-indigo-800 transition hover:bg-indigo-50">Ir al sistema</a>
                    @else
                        <a href="{{ route('login') }}" class="hidden rounded-xl px-3 py-2 text-sm font-bold text-indigo-800 transition hover:bg-indigo-50 sm:inline-flex">Ingresar</a>
                    @endauth
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-emerald-700/20 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                        Solicitar demo
                    </a>
                </div>
            </nav>
        </header>

        <main>
            <section class="relative isolate overflow-hidden bg-[#1b2a34] text-white">
                <div aria-hidden="true" class="absolute inset-0 -z-10">
                    <div class="absolute -right-28 -top-40 h-[34rem] w-[34rem] rounded-full bg-emerald-500/15 blur-3xl"></div>
                    <div class="absolute -bottom-48 left-1/4 h-[28rem] w-[28rem] rounded-full bg-emerald-700/15 blur-3xl"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.07)_1px,transparent_0)] bg-[size:28px_28px]"></div>
                </div>

                <div class="mx-auto grid max-w-7xl items-center gap-14 px-5 py-20 sm:px-8 sm:py-24 lg:grid-cols-2 lg:gap-16 lg:py-28">
                    <div class="max-w-2xl">
                        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-1.5 text-xs font-bold uppercase tracking-[0.16em] text-emerald-200">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Producción · ventas · facturación
                        </div>
                        <h1 class="text-4xl font-black leading-[1.08] tracking-tight text-white sm:text-5xl lg:text-6xl">
                            Tu negocio bajo control.
                            <span class="mt-2 block text-emerald-300">Tus decisiones, con números claros.</span>
                        </h1>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-slate-200">
                            Calcula el costo real de tus productos, administra el inventario y vende desde un punto de venta integrado con facturación electrónica del SRI.
                        </p>
                        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-emerald-900/15 transition hover:-translate-y-0.5 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200/50">
                                Solicitar una demostración
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.69L10.22 5.03a.75.75 0 1 1 1.06-1.06l5.5 5.5a.75.75 0 0 1 0 1.06l-5.5 5.5a.75.75 0 1 1-1.06-1.06l4.22-4.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
                            </a>
                            <a href="#beneficios" class="inline-flex items-center justify-center rounded-xl border border-white/25 bg-white/5 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-white/10">
                                Conocer el sistema
                            </a>
                        </div>
                        <div class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm font-semibold text-slate-200">
                            <span class="inline-flex items-center gap-2"><span class="text-emerald-300">✓</span> Costos y recetas</span>
                            <span class="inline-flex items-center gap-2"><span class="text-emerald-300">✓</span> Inventario conectado</span>
                            <span class="inline-flex items-center gap-2"><span class="text-emerald-300">✓</span> Facturas PDF y XML</span>
                        </div>
                    </div>

                    <div id="como-funciona" class="relative mx-auto w-full max-w-xl">
                        <div aria-hidden="true" class="absolute -inset-4 rounded-[2rem] bg-gradient-to-br from-emerald-400/30 to-indigo-400/20 blur-2xl"></div>
                        <div class="relative overflow-hidden rounded-2xl border border-white/15 bg-white p-4 text-zinc-800 shadow-2xl shadow-black/30 sm:p-5">
                            <div class="flex items-center justify-between border-b border-zinc-100 pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M8 15l4-4 4 3 5-7"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-extrabold text-zinc-900">Resumen del negocio</p>
                                        <p class="text-xs text-zinc-500">Información en un solo lugar</p>
                                    </div>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">Control integrado</span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 py-4">
                                <div class="rounded-xl bg-indigo-50 p-4">
                                    <p class="text-xs font-semibold text-indigo-700">Costos de producción</p>
                                    <p class="mt-2 text-lg font-black text-indigo-950">Por receta</p>
                                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-indigo-100"><div class="h-full w-3/4 rounded-full bg-indigo-600"></div></div>
                                </div>
                                <div class="rounded-xl bg-emerald-50 p-4">
                                    <p class="text-xs font-semibold text-emerald-700">Inventario</p>
                                    <p class="mt-2 text-lg font-black text-emerald-950">En control</p>
                                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-emerald-100"><div class="h-full w-4/5 rounded-full bg-emerald-500"></div></div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-zinc-100">
                                <div class="flex items-center justify-between border-b border-zinc-100 px-4 py-3">
                                    <p class="text-xs font-bold uppercase tracking-wider text-zinc-500">Un flujo conectado</p>
                                    <span class="text-xs font-semibold text-indigo-600">EmprendoSys</span>
                                </div>
                                <div class="grid grid-cols-3 divide-x divide-zinc-100 px-2 py-4 text-center">
                                    <div class="px-1">
                                        <span class="mx-auto flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5A1.5 1.5 0 0 1 5.5 4h13A1.5 1.5 0 0 1 20 5.5v13a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 18.5v-13ZM8 8h8M8 12h3m-3 4h8"/></svg>
                                        </span>
                                        <p class="mt-2 text-[11px] font-bold text-zinc-700">Recetas</p>
                                    </div>
                                    <div class="px-1">
                                        <span class="mx-auto flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9ZM3.5 7.8 12 12l8.5-4.2M12 12v9"/></svg>
                                        </span>
                                        <p class="mt-2 text-[11px] font-bold text-zinc-700">Ventas y stock</p>
                                    </div>
                                    <div class="px-1">
                                        <span class="mx-auto flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M9 13h6m-6 4h6"/></svg>
                                        </span>
                                        <p class="mt-2 text-[11px] font-bold text-zinc-700">Factura SRI</p>
                                    </div>
                                </div>
                            </div>
                            <p class="pt-3 text-center text-[11px] font-medium text-zinc-500">Menos tareas dispersas. Más claridad para decidir.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="beneficios" class="bg-[#edf0f2] py-20 sm:py-24">
                <div class="mx-auto max-w-7xl px-5 sm:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-emerald-700">Herramientas para crecer</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tight text-indigo-950 sm:text-4xl">De la producción a la venta, todo conectado</h2>
                        <p class="mt-4 text-base leading-7 text-zinc-600">Organiza la operación diaria con información centralizada y procesos más claros.</p>
                    </div>

                    <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                        <article class="rounded-2xl border border-indigo-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg hover:shadow-indigo-950/5">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M6 16V8m4 8V5m4 11v-5m4 5V7"/></svg>
                            </span>
                            <h3 class="mt-5 text-base font-extrabold text-indigo-950">Costos de producción</h3>
                            <p class="mt-2 text-sm leading-6 text-zinc-600">Calcula costos por receta considerando insumos, procesos y gastos de producción.</p>
                        </article>
                        <article class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg hover:shadow-emerald-950/5">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9ZM3.5 7.8 12 12l8.5-4.2M12 12v9"/></svg>
                            </span>
                            <h3 class="mt-5 text-base font-extrabold text-indigo-950">Inventario y stock</h3>
                            <p class="mt-2 text-sm leading-6 text-zinc-600">Sigue existencias y movimientos para planificar reposiciones y evitar faltantes.</p>
                        </article>
                        <article class="rounded-2xl border border-amber-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg hover:shadow-amber-950/5">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.2 11.1A2 2 0 0 0 9.2 17h8.6a2 2 0 0 0 2-1.6L21 9H6m4 12h.01M17 21h.01"/></svg>
                            </span>
                            <h3 class="mt-5 text-base font-extrabold text-indigo-950">Punto de venta</h3>
                            <p class="mt-2 text-sm leading-6 text-zinc-600">Registra ventas, clientes y productos en un flujo pensado para atender con agilidad.</p>
                        </article>
                        <article class="rounded-2xl border border-indigo-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg hover:shadow-indigo-950/5">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M9 13h6m-6 4h6"/></svg>
                            </span>
                            <h3 class="mt-5 text-base font-extrabold text-indigo-950">Facturación electrónica</h3>
                            <p class="mt-2 text-sm leading-6 text-zinc-600">Gestiona comprobantes electrónicos y entrega al cliente los archivos PDF y XML.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="border-y border-indigo-100 bg-white py-14">
                <div class="mx-auto grid max-w-7xl gap-8 px-5 sm:px-8 md:grid-cols-3">
                    <div class="flex gap-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 font-black text-emerald-700">1</span>
                        <div><h3 class="font-extrabold text-indigo-950">Conoce tus costos</h3><p class="mt-1 text-sm leading-6 text-zinc-600">Revisa el costo de producción y define precios con más información.</p></div>
                    </div>
                    <div class="flex gap-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-black text-indigo-700">2</span>
                        <div><h3 class="font-extrabold text-indigo-950">Organiza tu operación</h3><p class="mt-1 text-sm leading-6 text-zinc-600">Conecta productos, inventario, clientes y ventas en el mismo sistema.</p></div>
                    </div>
                    <div class="flex gap-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 font-black text-amber-700">3</span>
                        <div><h3 class="font-extrabold text-indigo-950">Atiende y factura</h3><p class="mt-1 text-sm leading-6 text-zinc-600">Emite comprobantes electrónicos y comparte los documentos con tus clientes.</p></div>
                    </div>
                </div>
            </section>

            <section id="contacto" class="bg-indigo-950 px-5 py-16 text-center text-white sm:px-8 sm:py-20">
                <div class="mx-auto max-w-3xl">
                    <p class="text-xs font-black uppercase tracking-[0.22em] text-emerald-300">Da el siguiente paso</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Haz que cada parte de tu negocio trabaje en conjunto</h2>
                    <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-indigo-100/80">Conversemos sobre tu operación y conoce cómo EmprendoSys puede ayudarte a llevarla con más control.</p>
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="mt-8 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-7 py-3.5 text-sm font-extrabold text-indigo-950 shadow-lg shadow-black/20 transition hover:-translate-y-0.5 hover:bg-emerald-400 focus:outline-none focus:ring-4 focus:ring-emerald-200/50">
                        Solicitar información por WhatsApp
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.69L10.22 5.03a.75.75 0 1 1 1.06-1.06l5.5 5.5a.75.75 0 0 1 0 1.06l-5.5 5.5a.75.75 0 1 1-1.06-1.06l4.22-4.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
                    </a>
                </div>
            </section>
        </main>

        <footer class="bg-indigo-950 px-5 pb-7 text-center text-xs font-medium text-indigo-200/70 sm:px-8">
            &copy; {{ date('Y') }} EmprendoSys. Gestión para producir, vender y crecer.
        </footer>
    </body>
</html>
