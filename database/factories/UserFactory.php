<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            // Si no hay empresas, ponemos null, pero el seeder se encargará de pasarle una
            'company_id' => Company::first()?->id ?? null, 
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => Crypt::encryptString('JBSWY3DPEHPK3PXP'),
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode(['code1', 'code2'])),
            'two_factor_confirmed_at' => now(),
        ]);
    }
}