<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de roles y permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- DEFINICIÓN DE PERMISOS ---
        $permissions = [
            // Administración General
            'ver administracion',

            // Gestión de Usuarios / Colaboradores
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',

            // Administración Global (Super-Admin)
            'ver empresas',
            'crear empresas',
            'editar empresas',
            'eliminar empresas',
            'ver roles',

            // Configuración de Empresa (Tenant)
            'editar mi empresa',

            // Catálogos y Producción
            'gestionar productos',
            'gestionar materias primas',
            'gestionar empaques',
            'gestionar suministros',
            'gestionar mano de obra',
            'gestionar costos indirectos',

            // Ventas
            'realizar ventas',
            'gestionar clientes',
            'ver facturacion',
        ];

        // Crear cada permiso en la base de datos
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // --- DEFINICIÓN DE ROLES ---

        // 1. Rol: Super-Admin
        $roleSuperAdmin = Role::findOrCreate('super-admin');
        $roleSuperAdmin->givePermissionTo(Permission::all());

        // 2. Rol: Admin (Dueño / Administrador de Empresa)
        $roleAdmin = Role::findOrCreate('admin');
        $roleAdmin->givePermissionTo([
            'ver administracion',
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'editar mi empresa',
            'gestionar productos',
            'gestionar materias primas',
            'gestionar empaques',
            'gestionar suministros',
            'gestionar mano de obra',
            'gestionar costos indirectos',
            'realizar ventas',
            'gestionar clientes',
            'ver facturacion',
        ]);

        // 3. Rol: Vendedor
        $roleVendedor = Role::findOrCreate('vendedor');
        $roleVendedor->givePermissionTo([
            'realizar ventas',
            'gestionar clientes',
            'gestionar productos',
        ]);
    }
}
