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
        return view('dashboard', compact('lowStockProducts'));
    })->name('dashboard');

    // --- SECCIÓN: ADMINISTRACIÓN Y CATÁLOGOS (Solo Admin) ---
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/empresas', Company::class)->name('admin.companies');
        Route::get('/admin/usuarios', Users::class)->name('admin.users');

        // Módulo de Costos y Productos
        Route::prefix('productos')->group(function () {
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
    });

    // --- SECCIÓN: VENTAS Y CLIENTES (Admin o Vendedor) ---
    Route::middleware(['role:admin|vendedor'])->group(function () {
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
        Artisan::call('migrate:fresh --seed --force');
        return 'Base de datos reseteada';
    }
    abort(403);
});