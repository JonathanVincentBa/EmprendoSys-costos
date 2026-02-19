<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>EmprendoSys - Gestión de Costos</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    {{-- Fondo Gris Piedra Opaco (#F1F1EF) - Bajo reflejo visual --}}
    <body class="bg-[#F1F1EF] text-zinc-700 antialiased font-['Instrument_Sans']">
        
        {{-- Navbar --}}
        <nav class="flex items-center justify-between p-6 lg:px-8 max-w-7xl mx-auto w-full sticky top-0 bg-[#F1F1EF]/90 backdrop-blur-sm z-50">
            <div class="flex lg:flex-1">
                <a href="{{ route('home') }}" class="-m-1.5 p-1.5 text-xl font-bold tracking-tight text-zinc-800">
                    <span class="text-orange-700">Emprendo</span>Sys
                </a>
            </div>
            <div class="flex flex-1 justify-end gap-x-4 items-center">
                @if (Route::has('login'))
                    <nav class="flex gap-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-lg px-4 py-2 text-sm font-bold text-zinc-600 hover:bg-zinc-200 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-bold text-zinc-600 hover:bg-zinc-200 transition">Entrar</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-lg bg-zinc-800 px-4 py-2 text-sm font-bold text-zinc-100 shadow-sm hover:bg-zinc-700 transition">Registrarse</a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </nav>

        <main class="isolate">
            {{-- Hero Section --}}
            <div class="relative pt-10 pb-20 lg:pt-24">
                <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
                    <div class="inline-flex items-center gap-2 rounded-full bg-zinc-200/80 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-zinc-500 mb-8 border border-zinc-300">
                        Sistemas de Producción
                    </div>
                    <h1 class="text-4xl font-bold tracking-tight text-zinc-800 sm:text-6xl">
                        Controla tus costos con<br>
                        <span class="text-zinc-500 font-medium font-serif">una interfaz mate.</span>
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-zinc-500 max-w-2xl mx-auto">
                        Diseñado para emprendedores que pasan horas frente a la pantalla. Una paleta opaca para mantener la mente clara y la vista descansada.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-xl bg-orange-700 px-8 py-4 text-sm font-bold text-zinc-100 shadow-md hover:bg-orange-800 transition-all">
                                Comenzar ahora
                            </a>
                        @endif
                        <a href="#features" class="text-sm font-bold text-zinc-400 hover:text-zinc-600 transition">Explorar funciones ↓</a>
                    </div>
                </div>
            </div>

            {{-- Sección de Funciones (ID: features) --}}
            <section id="features" class="py-24 bg-[#E8E8E6] border-y border-zinc-300/50">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl lg:text-center mb-16">
                        <h2 class="text-xs font-bold text-orange-700 uppercase tracking-[0.3em]">Beneficios</h2>
                        <p class="mt-2 text-3xl font-bold text-zinc-800">Todo en un solo lugar</p>
                    </div>

                    <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
                        {{-- Card 1 --}}
                        <div class="group">
                            <div class="mb-5 flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-300 text-zinc-600 group-hover:bg-orange-700 group-hover:text-white transition-all">
                                <span class="text-xl">🧮</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-800 uppercase tracking-wider">Costo por Receta</h3>
                            <p class="mt-3 text-sm leading-relaxed text-zinc-500">Calcula automáticamente el costo de cada ingrediente y empaque usado en tus productos.</p>
                        </div>

                        {{-- Card 2 --}}
                        <div class="group">
                            <div class="mb-5 flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-300 text-zinc-600 group-hover:bg-orange-700 group-hover:text-white transition-all">
                                <span class="text-xl">📦</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-800 uppercase tracking-wider">Stock Inteligente</h3>
                            <p class="mt-3 text-sm leading-relaxed text-zinc-500">Alertas automáticas de inventario bajo para que nunca detengas tu producción.</p>
                        </div>

                        {{-- Card 3 --}}
                        <div class="group">
                            <div class="mb-5 flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-300 text-zinc-600 group-hover:bg-orange-700 group-hover:text-white transition-all">
                                <span class="text-xl">📈</span>
                            </div>
                            <h3 class="text-sm font-bold text-zinc-800 uppercase tracking-wider">Análisis de Venta</h3>
                            <p class="mt-3 text-sm leading-relaxed text-zinc-500">Visualiza tu utilidad real y proyecciones de crecimiento basadas en datos históricos.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- CTA Section --}}
            <div class="py-24 px-6 text-center">
                <div class="mx-auto max-w-3xl rounded-2xl bg-zinc-800 p-12 shadow-sm relative overflow-hidden">
                    <h2 class="text-2xl font-bold text-zinc-100">Únete a la nueva era de emprendedores</h2>
                    <p class="mt-4 text-zinc-400">Prueba gratuita de 60 días. Sin compromisos, solo resultados.</p>
                    <div class="mt-8">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-block rounded-lg bg-orange-700 px-8 py-3 text-sm font-bold text-zinc-100 hover:bg-orange-600 transition">
                                Crear mi cuenta gratuita
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-12 text-center text-zinc-400 text-[10px] font-bold uppercase tracking-[0.4em]">
            &copy; {{ date('Y') }} EmprendoSys — Interfaz Optimizada
        </footer>
    </body>
</html>