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
        // Limpiar caché de roles y permisos (Evita errores de "permiso no encontrado")
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- DEFINICIÓN DE PERMISOS ---
        $permissions = [
            // Administración Global (Super-Admin)
            'ver empresas',
            'crear empresas',
            'editar empresas',
            'eliminar empresas',

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

        // Crear cada permiso en la tabla 'permissions'
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // --- DEFINICIÓN DE ROLES ---

        // 1. Rol: Super-Admin
        $roleSuperAdmin = Role::findOrCreate('super-admin');
        // El Super-Admin recibe automáticamente TODOS los permisos
        $roleSuperAdmin->givePermissionTo(Permission::all());

        // 2. Rol: Admin (Dueño de Empresa / Tenant)
        $roleAdmin = Role::findOrCreate('admin');
        $roleAdmin->givePermissionTo([
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
            'gestionar productos', // Para que pueda ver el stock
        ]);
    }
}