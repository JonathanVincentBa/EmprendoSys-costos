<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header 
            title="Crea tu cuenta" 
            description="Ingresa tus datos a continuación para registrarte en el sistema" 
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-4">
            @csrf
            <flux:input
                name="name"
                label="Nombre completo"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Ej. Juan Pérez"
            />

            <flux:input
                name="email"
                label="Correo electrónico"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="tu@negocio.com"
            />

            <flux:input
                name="password"
                label="Contraseña"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Crea una clave segura"
                viewable
            />

            <flux:input
                name="password_confirmation"
                label="Confirmar contraseña"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Repite tu clave"
                viewable
            />

            <div class="flex items-center justify-end mt-2">
                <flux:button type="submit" variant="primary" class="w-full bg-orange-600 hover:bg-orange-500 border-none" data-test="register-user-button">
                    Crear mi cuenta
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
            <span>¿Ya tienes una cuenta?</span>
            <flux:link :href="route('login')" wire:navigate>Inicia sesión aquí</flux:link>
        </div>
    </div>
</x-layouts.auth>