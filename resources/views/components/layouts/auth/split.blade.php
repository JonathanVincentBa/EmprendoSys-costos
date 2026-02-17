<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-white antialiased font-['Instrument_Sans']">
        <div class="relative grid min-h-screen flex-col items-center justify-center lg:max-w-none lg:grid-cols-2 lg:px-0">
            
            <div class="relative hidden h-full flex-col text-white lg:flex min-h-[600px]">
                <div class="absolute inset-0 bg-cover bg-center" 
                     style="background-image: url('https://images.unsplash.com/photo-1550989460-0adf9ea622e2?q=80&w=1974&auto=format&fit=crop');">
                </div>
                
                <div class="absolute inset-0 bg-black/60"></div> 
                
                <a href="{{ route('home') }}" class="relative z-20 flex items-center p-10 text-2xl font-bold tracking-tight" wire:navigate>
                    <span class="text-orange-500 underline decoration-white/20">Emprendo</span>Sys
                </a>

                <div class="relative z-20 mt-auto p-10 pb-20"> <blockquote class="space-y-4">
                        <p class="text-4xl font-bold leading-tight text-white drop-shadow-lg">
                            "La precisión en tus costos es el <span class="text-yellow-400">ingrediente secreto</span> de tu éxito."
                        </p>
                        <footer class="text-xl text-orange-400 font-semibold uppercase tracking-wider">
                            Gestión de Producción
                        </footer>
                    </blockquote>
                </div>
            </div>

            <div class="w-full h-full py-12 lg:p-8 bg-gray-50 flex items-center justify-center">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[400px] px-6">
                    
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden mb-4" wire:navigate>
                         <span class="text-3xl font-bold tracking-tight text-black">
                            <span class="text-orange-600">Emprendo</span>Sys
                        </span>
                    </a>

                    <div class="text-center lg:text-left">
                        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Bienvenido</h2>
                        <p class="text-gray-500 mt-2 text-base">Ingresa tus datos para continuar.</p>
                    </div>

                    <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-xl">
                        {{ $slot }}
                    </div>

                    <p class="text-center text-xs text-gray-400">
                        © {{ date('Y') }} EmprendoSys. <br> 
                        <span class="font-medium text-gray-600 italic">Optimizando emprendimientos.</span>
                    </p>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>