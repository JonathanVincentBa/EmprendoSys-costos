<?php

use App\Models\Company;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['super-admin', 'admin', 'vendedor'] as $role) {
        Role::findOrCreate($role, 'web');
    }
});

function createAuthorizedUser(string $role, ?int $companyId = null): User
{
    $user = User::factory()->create([
        'company_id' => $companyId,
        'email_verified_at' => now(),
    ]);
    $user->assignRole($role);

    return $user;
}

test('super admin cannot access operational company modules', function () {
    $user = createAuthorizedUser('super-admin');

    $this->actingAs($user)->get('/productos')->assertForbidden();
    $this->actingAs($user)->get('/ventas')->assertForbidden();
    $this->actingAs($user)->get('/clientes')->assertForbidden();
});

test('admin can access operational modules for their company', function () {
    $company = Company::create([
        'name' => 'Empresa de prueba',
        'razon_social' => 'Empresa de prueba S.A.',
        'ruc' => '1799999999001',
        'email' => 'empresa@example.com',
    ]);
    $user = createAuthorizedUser('admin', $company->id);

    $this->actingAs($user)->get('/productos')->assertOk();
    $this->actingAs($user)->get('/ventas')->assertOk();
    $this->actingAs($user)->get('/company-profile')->assertOk();
});

test('super admin can manage companies but not company profile route', function () {
    $user = createAuthorizedUser('super-admin');

    $this->actingAs($user)->get('/admin/companies')->assertOk();
    $this->actingAs($user)->get('/company-profile')->assertForbidden();
});
