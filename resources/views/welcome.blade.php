<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>EmprendoSys - Potencia tu Producción</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-[#0A0A0A] text-white antialiased font-['Instrument_Sans']">
        
        <nav class="flex items-center justify-between p-6 lg:px-8 max-w-7xl mx-auto w-full" aria-label="Global">
            <div class="flex lg:flex-1">
                <a href="#" class="-m-1.5 p-1.5 text-2xl font-bold tracking-tight">
                    <span class="text-orange-500 underline decoration-white/20">Emprendo</span>Sys
                </a>
            </div>
            <div class="flex flex-1 justify-end gap-x-6">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-semibold leading-6 bg-white/10 px-4 py-2 rounded-lg hover:bg-white/20 transition">Ir al Panel</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold leading-6 self-center">Iniciar Sesión</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600 transition">Probar Gratis</a>
                    @endauth
                @endif
            </div>
        </nav>

        <div class="relative isolate px-6 pt-14 lg:px-8">
            <div class="mx-auto max-w-4xl py-20">
                <div class="hidden sm:mb-8 sm:flex sm:justify-center">
                    <div class="relative rounded-full px-3 py-1 text-sm leading-6 text-gray-400 ring-1 ring-white/10 hover:ring-white/20">
                        ¡Lanzamiento exclusivo para 10 emprendedores! <a href="{{ route('register') }}" class="font-semibold text-orange-500"><span class="absolute inset-0" aria-hidden="true"></span>Leer más &rarr;</a>
                    </div>
                </div>
                <div class="text-center">
                    <h1 class="text-5xl font-bold tracking-tight sm:text-7xl bg-linear-to-b from-white to-gray-500 bg-clip-text text-transparent">
                        Controla tu producción al centavo
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-400 max-w-2xl mx-auto">
                        Diseñado para emprendedores que transforman materia prima. Calcula costos exactos, gestiona inventarios y proyecta tus ganancias reales sin complicaciones.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <a href="{{ route('register') }}" class="rounded-xl bg-orange-600 px-8 py-4 text-lg font-bold text-white shadow-lg hover:bg-orange-500 hover:scale-105 transition-all focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600">
                            Obtener 2 meses GRATIS
                        </a>
                        <a href="#servicios" class="text-sm font-semibold leading-6 text-white border border-white/10 px-6 py-3 rounded-xl hover:bg-white/5 transition">
                            Ver funciones <span aria-hidden="true">↓</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section id="servicios" class="py-24 bg-zinc-900/30">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-orange-500 uppercase tracking-widest">Profesionaliza tu negocio</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl text-white">Todo lo que necesitas en un solo sistema web</p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                        <div class="flex flex-col p-8 bg-zinc-900 border border-white/5 rounded-2xl">
                            <dt class="flex items-center gap-x-3 text-lg font-bold leading-7 text-white">
                                <span class="text-3xl">🧮</span> Costeo Automático
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-400">
                                <p class="flex-auto">Calcula el costo real de cada salsa, pan o producto. Incluye materia prima, envases y mano de obra sin errores manuales.</p>
                            </dd>
                        </div>
                        <div class="flex flex-col p-8 bg-zinc-900 border border-white/5 rounded-2xl shadow-xl shadow-orange-900/10">
                            <dt class="flex items-center gap-x-3 text-lg font-bold leading-7 text-white">
                                <span class="text-3xl">📦</span> Stock Inteligente
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-400">
                                <p class="flex-auto">Recibe alertas cuando tu materia prima se esté agotando. Mantén tu inventario siempre actualizado automáticamente con cada venta.</p>
                            </dd>
                        </div>
                        <div class="flex flex-col p-8 bg-zinc-900 border border-white/5 rounded-2xl">
                            <dt class="flex items-center gap-x-3 text-lg font-bold leading-7 text-white">
                                <span class="text-3xl">📈</span> Ganancia Proyectada
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-400">
                                <p class="flex-auto">Visualiza cuánto dinero real te queda después de cada venta. Gráficos claros de tus ingresos y egresos diarios.</p>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        <div class="py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="relative isolate overflow-hidden bg-orange-600 px-6 py-24 shadow-2xl rounded-3xl sm:px-24 xl:py-32">
                    <h2 class="mx-auto max-w-2xl text-center text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        Únete hoy mismo por solo $10 al mes
                    </h2>
                    <p class="mx-auto mt-2 max-w-xl text-center text-lg leading-8 text-orange-100">
                        O aprovecha nuestra oferta de lanzamiento y úsalo <b>GRATIS por 60 días</b> a cambio de tu opinión para mejorar el sistema.
                    </p>
                    <div class="mt-10 flex justify-center">
                        <a href="{{ route('register') }}" class="rounded-lg bg-white px-8 py-3.5 text-sm font-bold text-orange-600 shadow-sm hover:bg-gray-100 transition">Empezar ahora</a>
                    </div>
                </div>
            </div>
        </div>

        <footer class="text-center py-10 border-t border-white/5 text-gray-500 text-sm">
            &copy; {{ date('Y') }} EmprendoSys. Desarrollado para emprendedores reales.
        </footer>
    </body>
</html>