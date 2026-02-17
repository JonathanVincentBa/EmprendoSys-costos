<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header 
            title="¿Olvidaste tu contraseña?" 
            description="No te preocupes. Ingresa tu correo y te enviaremos un enlace para que puedas crear una nueva." 
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4">
            @csrf

            <flux:input
                name="email"
                label="Correo electrónico"
                type="email"
                required
                autofocus
                placeholder="tu@negocio.com"
            />

            <div class="mt-2">
                <flux:button variant="primary" type="submit" class="w-full bg-orange-600 hover:bg-orange-500 border-none" data-test="email-password-reset-link-button">
                    Enviar enlace de recuperación
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
            <span>O también puedes</span>
            <flux:link :href="route('login')" wire:navigate>volver al inicio de sesión</flux:link>
        </div>
    </div>
</x-layouts.auth>