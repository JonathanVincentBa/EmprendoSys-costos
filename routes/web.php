<?php

use App\Livewire\Administracion\Company;
use App\Livewire\Catalogos\LaborCosts;
use App\Livewire\Catalogos\OverheadConfigs;
use App\Livewire\Catalogos\PackagingMaterials;
use App\Livewire\Catalogos\RawMaterials;
use App\Livewire\Catalogos\Supplies;
use App\Livewire\CostoProduccion\Products;
use App\Livewire\CostoProduccion\RecipeManager;
use App\Livewire\CostoProduccion\ProductWizard; //
use App\Livewire\Sales\Customers;
use App\Livewire\Sales\PointOfSale;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('dashboard', function () {
        $companyId = Auth::user()->company_id;

        // 1. Productos con stock bajo
        $lowStockProducts = Product::where('company_id', $companyId)
            ->whereColumn('current_stock', '<=', 'minimum_stock_level')
            ->get();

        // 2. Suma de ventas de HOY
        $todaySales = Sale::where('company_id', $companyId)
            ->whereDate('sale_date', Carbon::today())
            ->where('status', 'completed')
            ->sum('total');

        return view('dashboard', [
            'lowStockProducts' => $lowStockProducts,
            'todaySales' => $todaySales
        ]);
    })->name('dashboard');

    // --- SECCIÓN: COSTOS DE PRODUCCIÓN ---
    // Ruta del nuevo Asistente Maestro
    Route::get('/asistente-maestro', ProductWizard::class)->name('product.wizard');

    Route::get('/productos', Products::class)->name('products.index');
    Route::get('/productos/{product}/receta', RecipeManager::class)->name('products.recipe');


    // --- SECCIÓN: VENTAS (Añadidas para completar la navegación del sidebar) ---
    /* Route::get('/ventas', function () {
        return '<h1 class="p-6 text-2xl">Órdenes de Venta</h1>';
    })->name('sales.index'); */
    Route::get('/ventas', PointOfSale::class)->name('sales.pos');

    Route::get('/clientes', Customers::class)->name('clients.index');

    Route::get('/facturacion', function () {
        return '<h1 class="p-6 text-2xl">Facturación</h1>';
    })->name('invoices.index');

    // --- OTRAS SECCIONES (Catálogos, etc.) ---
    Route::get('raw-materials', RawMaterials::class)->name('raw-materials.index');
    Route::get('packaging', PackagingMaterials::class)->name('packaging.index');
    Route::get('supplies', Supplies::class)->name('supplies.index');
    Route::get('overhead-config', OverheadConfigs::class)->name('overhead-config.index');
    Route::get('labor-costs', LaborCosts::class)->name('labor-costs.index');

    // Configuración
    Route::get('company/edit', Company::class)->name('company.edit');
});

require __DIR__ . '/settings.php';
