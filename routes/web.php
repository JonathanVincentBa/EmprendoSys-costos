<?php

use App\Livewire\Administracion\Company;
use App\Livewire\Administracion\RolesManager;
use App\Livewire\Administracion\Users;
use App\Livewire\Catalogos\LaborCosts;
use App\Livewire\Catalogos\OverheadConfigs;
use App\Livewire\Catalogos\PackagingMaterials;
use App\Livewire\Catalogos\RawMaterials;
use App\Livewire\Catalogos\Supplies;
use App\Livewire\CostoProduccion\Products;
use App\Livewire\CostoProduccion\RecipeManager;
use App\Livewire\CostoProduccion\ProductWizard;
use App\Livewire\Sales\Customers;
use App\Livewire\Sales\PointOfSale;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. RUTA PÚBLICA (Landing page)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 2. RUTAS PROTEGIDAS (Requieren Login y Verificación)
Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | DASHBOARD CENTRAL
    |----------------------------------------------------------------------
    | Redirección inteligente basada en el rol del usuario.
    */
    Route::get('dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.companies');
        }
        return view('dashboard');
    })->name('dashboard');


    /*
    |----------------------------------------------------------------------
    | ADMINISTRACIÓN GLOBAL (Solo Super-Admin)
    |----------------------------------------------------------------------
    | Gestión de empresas, roles del sistema y permisos globales.
    */
    Route::middleware(['role:super-admin'])->group(function () {
        Route::get('admin/companies', Company::class)->name('admin.companies');
        Route::get('admin/roles', RolesManager::class)->name('admin.roles');
    });


    /*
    |----------------------------------------------------------------------
    | GESTIÓN DE USUARIOS (Super-Admin y Admin)
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:super-admin|admin'])->group(function () {
        Route::get('users', Users::class)->name('users.index');
        Route::get('company-profile', Company::class)->name('admin.company.profile');
    });


    /*
    |----------------------------------------------------------------------
    | CATÁLOGOS Y COSTOS (Super-Admin y Admin)
    |----------------------------------------------------------------------
    | Módulos técnicos para configurar materias primas, empaques y costos
    | de producción. El vendedor NO tiene acceso aquí.
    */
    Route::middleware(['role:super-admin|admin'])->group(function () {
        // Producción y Recetas
        Route::get('/asistente-maestro', ProductWizard::class)->name('product.wizard');
        Route::get('/productos', Products::class)->name('products.index');
        Route::get('/productos/{product}/receta', RecipeManager::class)->name('products.recipe');

        // Inventarios Técnicos
        Route::get('raw-materials', RawMaterials::class)->name('raw-materials.index');
        Route::get('packaging', PackagingMaterials::class)->name('packaging.index');
        Route::get('supplies', Supplies::class)->name('supplies.index');

        // Configuraciones de Costeo
        Route::get('overhead-config', OverheadConfigs::class)->name('overhead-config.index');
        Route::get('labor-costs', LaborCosts::class)->name('labor-costs.index');
    });


    /*
    |----------------------------------------------------------------------
    | ÁREA COMERCIAL / VENTAS (Super-Admin, Admin y Vendedor)
    |----------------------------------------------------------------------
    | Módulos a los que el Vendedor tiene permiso para operar.
    */
    Route::middleware(['role:super-admin|admin|vendedor'])->group(function () {
        Route::get('/ventas', PointOfSale::class)->name('sales.pos');
        Route::get('/clientes', Customers::class)->name('clients.index');
        Route::get('/facturacion', function () {
            return '<h1 class="p-6 text-2xl">Módulo SRI (Próximamente)</h1>';
        })->name('invoices.index');
    });

});

/*
|--------------------------------------------------------------------------
| RUTAS DE MANTENIMIENTO (Solo Super-Admin)
|--------------------------------------------------------------------------
*/
Route::get('/reset-db-12345', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    if ($user?->hasRole('super-admin')) {
        Artisan::call('migrate:fresh --seed');
        return "Base de datos reseteada y sembrada con éxito.";
    }
    abort(403);
});