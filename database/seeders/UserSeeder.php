<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $company = Company::firstOrCreate([
            'ruc' => '9999999999001'
        ], [
            'name' => 'Miski Sabores',
            'address' => 'Quito, Ecuador',
            'email' => 'info@miski.com',
            'phone' => '0999999999',
        ]);

        User::updateOrCreate(
            ['email' => 'jonathanvincent@outlook.com'],
            [
                'name' => 'Jonathan Vincent',
                'email' => 'jonathanvincent@outlook.com',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
            ]
        );
    }
}