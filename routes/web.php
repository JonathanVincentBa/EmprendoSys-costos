<?php

use App\Livewire\Administracion\Company;
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
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Ruta de bienvenida
Route::get('/', function () {
    return view('welcome');
})->name('home');

// --- RUTAS PROTEGIDAS POR AUTENTICACIÓN ---
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard General con redirección para Super-Admin
    Route::get('dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Redirección basada en rol
        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.companies');
        }

        $lowStockProducts = Product::whereColumn('current_stock', '<=', 'minimum_stock_level')->get();
        $todaySales = Sale::whereDate('sale_date', Carbon::today())
            ->where('status', 'completed')
            ->sum('total');

        return view('dashboard', compact('lowStockProducts', 'todaySales'));
    })->name('dashboard');

    // --- SECCIÓN ADMINISTRACIÓN GLOBAL ---
    Route::middleware(['can:ver empresas'])->prefix('administracion')->group(function () {
        Route::get('/empresas', Company::class)->name('admin.companies');
        Route::get('/administracion/usuarios', Users::class)->name('users.index');
    });

    // --- SECCIÓN MI EMPRESA (Tenant) ---
    // Cambiamos 'can' por 'role_or_permission' para dar acceso total al Super-Admin
    Route::get('/mi-empresa', Company::class)
        ->name('my.company')
        ->middleware('role_or_permission:super-admin|editar mi empresa');

    // --- SECCIÓN: PRODUCCIÓN Y CATÁLOGOS ---
    Route::middleware(['can:gestionar productos'])->group(function () {
        Route::get('/asistente-maestro', ProductWizard::class)->name('product.wizard');
        Route::get('/productos', Products::class)->name('products.index');
        Route::get('/productos/{product}/receta', RecipeManager::class)->name('products.recipe');

        // Catálogos
        Route::get('raw-materials', RawMaterials::class)->name('raw-materials.index');
        Route::get('packaging', PackagingMaterials::class)->name('packaging.index');
        Route::get('supplies', Supplies::class)->name('supplies.index');
        Route::get('overhead-config', OverheadConfigs::class)->name('overhead-config.index');
        Route::get('labor-costs', LaborCosts::class)->name('labor-costs.index');
    });

    // --- SECCIÓN: VENTAS Y CLIENTES ---
    Route::middleware(['can:realizar ventas'])->group(function () {
        Route::get('/ventas', PointOfSale::class)->name('sales.pos');
        Route::get('/clientes', Customers::class)->name('clients.index');
        Route::get('/facturacion', function () {
            return '<h1 class="p-6 text-2xl">Módulo SRI (Próximamente)</h1>';
        })->name('invoices.index');
    });
});

Route::get('/reset-db-12345', function () {
    /** @var \App\Models\User $user */
        $user = Auth::user();
    if ($user?->hasRole('super-admin')) {
        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        return "Base de datos reseteada con éxito.";
    }
    return "No autorizado.";
});