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
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. RUTA PÚBLICA
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 2. RUTAS PROTEGIDAS (Requieren Login)
Route::middleware(['auth', 'verified'])->group(function () {

    // --- DASHBOARD ---
    Route::get('dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Redirección pura: Si es super-admin, mándalo a empresas
        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.companies');
        }

        $lowStockProducts = Product::whereColumn('current_stock', '<=', 'minimum_stock_level')->get();
        $todaySales = Sale::whereDate('sale_date', Carbon::today())
            ->where('status', 'completed')
            ->sum('total');

        return view('dashboard', compact('lowStockProducts', 'todaySales'));
    })->name('dashboard');

    // --- ADMINISTRACIÓN GLOBAL (Solo Super-Admin) ---
    Route::middleware(['role:super-admin'])->prefix('administracion')->group(function () {
        Route::get('/roles', RolesManager::class)->name('admin.roles');
    });

    // --- ADMINISTRACIÓN DE EMPRESA (Admin y Super-Admin) ---
    Route::middleware(['role:super-admin|admin'])->prefix('administracion')->group(function () {
        Route::get('/mi-empresa', Company::class)->name('my.company');
        Route::get('/empresas', Company::class)->name('admin.companies');
        Route::get('/usuarios', Users::class)->name('admin.users');
    });

    // --- PRODUCCIÓN Y CATÁLOGOS ---
    Route::middleware(['role:super-admin|admin'])->group(function () {
        Route::get('/asistente-maestro', ProductWizard::class)->name('product.wizard');
        Route::get('/productos', Products::class)->name('products.index');
        Route::get('/productos/{product}/receta', RecipeManager::class)->name('products.recipe');

        Route::get('raw-materials', RawMaterials::class)->name('raw-materials.index');
        Route::get('packaging', PackagingMaterials::class)->name('packaging.index');
        Route::get('supplies', Supplies::class)->name('supplies.index');
        Route::get('overhead-config', OverheadConfigs::class)->name('overhead-config.index');
        Route::get('labor-costs', LaborCosts::class)->name('labor-costs.index');
    });

    // --- VENTAS (Permite acceso a Vendedor) ---
    Route::middleware(['role:super-admin|admin|vendedor'])->group(function () {
        Route::get('/ventas', PointOfSale::class)->name('sales.pos');
        Route::get('/clientes', Customers::class)->name('clients.index');
        Route::get('/facturacion', function () {
            return '<h1 class="p-6 text-2xl">Módulo SRI (Próximamente)</h1>';
        })->name('invoices.index');
    });
});

// 3. RUTA DE MANTENIMIENTO
Route::get('/reset-db-12345', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    if ($user?->hasRole('super-admin')) {
        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        return "Base de datos reseteada con éxito.";
    }
    return "No autorizado.";
});
