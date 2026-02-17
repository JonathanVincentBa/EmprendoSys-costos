<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header 
            title="Inicia sesión en tu cuenta" 
            description="Ingresa tu correo y contraseña para acceder al sistema" 
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-4"> @csrf

            <flux:input
                name="email"
                label="Correo electrónico"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="tu@negocio.com"
            />

            <div class="relative">
                <flux:input
                    name="password"
                    label="Contraseña"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Tu clave secreta"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        ¿Olvidaste tu contraseña?
                    </flux:link>
                @endif
            </div>

            <flux:checkbox name="remember" label="Recordarme" :checked="old('remember')" />

            <div class="flex items-center justify-end mt-2">
                <flux:button variant="primary" type="submit" class="w-full bg-orange-600 hover:bg-orange-500 border-none" data-test="login-button">
                    Entrar al Sistema
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>¿No tienes una cuenta?</span>
                <flux:link :href="route('register')" wire:navigate>Regístrate aquí</flux:link>
            </div>
        @endif
    </div>
</x-layouts.auth>