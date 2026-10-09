<?php

use App\Livewire\CostoProduccion\ProductWizard;
use App\Models\Company;
use App\Models\LaborCost;
use App\Models\OverheadConfig;
use App\Models\PackagingMaterial;
use App\Models\Product;
use App\Models\ProductionProcess;
use App\Models\RawMaterial;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\Supply;
use App\Models\User;
use App\Services\Sri\SriWebService;
use App\Services\CostCalculatorService;
use App\Services\Sri\SriXmlService;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

function makeProductionCompany(): Company
{
    return Company::create([
        'name' => 'Empresa de producción',
        'ruc' => '1799999999002',
        'email' => 'produccion@example.com',
    ]);
}

test('unit cost uses batch yield, process hours, overhead and excludes profit from production cost', function () {
    $company = makeProductionCompany();
    $material = RawMaterial::create([
        'company_id' => $company->id,
        'code' => 'MAT-1',
        'name' => 'Materia prima',
        'unit_cost' => 2,
        'unit' => 'kg',
    ]);
    $packaging = PackagingMaterial::create([
        'company_id' => $company->id,
        'code' => 'PKG-COST-1',
        'name' => 'Empaque',
        'unit_cost' => 0.50,
    ]);
    $product = Product::create([
        'company_id' => $company->id,
        'name' => 'Producto calculado',
        'presentation_ml' => 500,
        'packaging_type' => 'frasco',
    ]);
    $recipe = Recipe::create([
        'company_id' => $company->id,
        'product_id' => $product->id,
        'batch_size_ml' => 1000,
    ]);
    RecipeItem::create([
        'company_id' => $company->id,
        'recipe_id' => $recipe->id,
        'raw_material_id' => $material->id,
        'quantity_kg' => 2,
    ]);
    $process = ProductionProcess::create([
        'company_id' => $company->id,
        'name' => 'Mezclado',
        'hours_per_batch' => 1,
    ]);
    $supply = Supply::create([
        'company_id' => $company->id,
        'code' => 'SUP-COST-1',
        'name' => 'Suministro',
        'unit_cost' => 2,
    ]);
    DB::table('recipe_processes')->insert([
        'company_id' => $company->id,
        'recipe_id' => $recipe->id,
        'process_id' => $process->id,
        'hours_per_batch' => 2,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('recipe_packaging')->insert([
        'company_id' => $company->id,
        'recipe_id' => $recipe->id,
        'packaging_material_id' => $packaging->id,
        'units_per_batch' => 2,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('supply_usages')->insert([
        'company_id' => $company->id,
        'recipe_id' => $recipe->id,
        'supply_id' => $supply->id,
        'quantity' => 2,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    LaborCost::create([
        'company_id' => $company->id,
        'role' => 'Operario',
        'monthly_salary' => 1600,
    ]);
    OverheadConfig::create([
        'company_id' => $company->id,
        'name' => 'Indirectos',
        'percentage' => 10,
        'is_profit_margin' => false,
    ]);
    OverheadConfig::create([
        'company_id' => $company->id,
        'name' => 'Utilidad',
        'percentage' => 30,
        'is_profit_margin' => true,
    ]);

    $result = app(CostCalculatorService::class)->calculateUnitCost($product->id);

    expect($result['units_per_batch'])->toBe(2)
        ->and($result['direct_materials'])->toBe(4.0)
        ->and($result['packaging'])->toBe(1.0)
        ->and($result['supplies'])->toBe(4.0)
        ->and($result['labor'])->toBe(20.0)
        ->and($result['total_cost'])->toBe(31.9)
        ->and($result['profit_margin'])->toBe(9.57)
        ->and($result['unit_cost'])->toBe(15.95)
        ->and($result['suggested_price'])->toBe(20.74)
        ->and($recipe->labor_cost)->toBe(20.0);
});

test('production wizard persists calculated costs and sale price', function () {
    $company = makeProductionCompany();
    $user = User::factory()->create(['company_id' => $company->id]);
    $user->assignRole(Role::findOrCreate('admin', 'web'));
    $material = RawMaterial::create([
        'company_id' => $company->id,
        'code' => 'MAT-WIZ-1',
        'name' => 'Materia prima',
        'unit_cost' => 2,
        'unit' => 'kg',
    ]);
    $packaging = PackagingMaterial::create([
        'company_id' => $company->id,
        'code' => 'PKG-WIZ-1',
        'name' => 'Empaque',
        'unit_cost' => 0.50,
    ]);
    $process = ProductionProcess::create([
        'company_id' => $company->id,
        'name' => 'Mezclado',
        'hours_per_batch' => 2,
    ]);
    $supply = Supply::create([
        'company_id' => $company->id,
        'code' => 'SUP-WIZ-1',
        'name' => 'Suministro',
        'unit_cost' => 2,
    ]);
    LaborCost::create([
        'company_id' => $company->id,
        'role' => 'Operario',
        'monthly_salary' => 1600,
    ]);
    OverheadConfig::create([
        'company_id' => $company->id,
        'name' => 'Indirectos',
        'percentage' => 10,
        'is_profit_margin' => false,
    ]);
    $this->actingAs($user);

    Livewire::test(ProductWizard::class)
        ->set('name', 'Producto nuevo')
        ->set('presentation_ml', 500)
        ->set('batch_size_ml', 1000)
        ->set('packaging_material_id', $packaging->id)
        ->set('ingredients', [['id' => $material->id, 'qty' => 2]])
        ->set('selected_processes', [[
            'process_id' => $process->id,
            'hours' => 2,
        ]])
        ->set('selected_supplies', [[
            'id' => $supply->id,
            'quantity' => 2,
        ]])
        ->set('margin', 30)
        ->call('saveAll')
        ->assertHasNoErrors()
        ->assertRedirect(route('products.index'));

    $product = Product::query()->where('name', 'Producto nuevo')->firstOrFail();
    $recipeId = $product->recipes()->firstOrFail()->id;

    expect((float) $product->unit_cost)->toBe(15.95)
        ->and((float) $product->price)->toBe(20.74)
        ->and(DB::table('supply_usages')->where('recipe_id', $recipeId)->count())->toBe(1)
        ->and($product->sku)->not->toBeNull();
});

test('production wizard summary renders all its cost categories', function () {
    $company = makeProductionCompany();
    $user = User::factory()->create(['company_id' => $company->id]);
    $user->assignRole(Role::findOrCreate('admin', 'web'));
    $this->actingAs($user);

    Livewire::test(ProductWizard::class)
        ->set('step', 4)
        ->assertSee('Materia Prima:')
        ->assertSee('Mano de Obra:')
        ->assertSee('Empaque del lote:')
        ->assertSee('Suministros:')
        ->assertSee('COSTO TOTAL:');
});

test('electronic invoice date and environment match the sale used for its access key', function () {
    $company = makeProductionCompany();
    $sale = \App\Models\Sale::create([
        'company_id' => $company->id,
        'sale_date' => '2026-06-15 12:00:00',
        'total' => 10,
        'sri_environment' => '2',
    ]);

    $accessKey = SriXmlService::generateAccessKey($sale, $company);
    $xml = new SimpleXMLElement(SriXmlService::buildInvoiceXml($sale, $company, $accessKey));

    expect(substr($accessKey, 0, 8))->toBe('15062026')
        ->and((string) $xml->infoTributaria->ambiente)->toBe('2')
        ->and((string) $xml->infoFactura->fechaEmision)->toBe('15/06/2026');
});

test('SRI authorization parsing accepts one response or a list of authorizations', function () {
    $single = (object) ['estado' => 'AUTORIZADO'];
    $multiple = [
        (object) ['estado' => 'AUTORIZADO'],
        (object) ['estado' => 'NO AUTORIZADO'],
    ];

    expect(SriWebService::firstAuthorization((object) [
        'autorizaciones' => (object) ['autorizacion' => $single],
    ])?->estado)->toBe('AUTORIZADO')
        ->and(SriWebService::firstAuthorization((object) [
            'autorizaciones' => (object) ['autorizacion' => $multiple],
        ])?->estado)->toBe('AUTORIZADO')
        ->and(SriWebService::firstAuthorization(null))->toBeNull();
});

test('company cost seeder is repeatable without duplicate cost records', function () {
    $company = makeProductionCompany();
    $seeder = app(Database\Seeders\CompanyCostsSeeder::class);

    $seeder->run($company->id);
    $seeder->run($company->id);

    expect(RawMaterial::where('company_id', $company->id)->count())->toBe(3)
        ->and(PackagingMaterial::where('company_id', $company->id)->count())->toBe(3)
        ->and(ProductionProcess::where('company_id', $company->id)->count())->toBe(1)
        ->and(OverheadConfig::where('company_id', $company->id)->count())->toBe(2);
});

test('company and product cost seeders do not duplicate overhead or profit rates', function () {
    $company = makeProductionCompany();

    app(Database\Seeders\CompanyCostsSeeder::class)->run($company->id);
    app(Database\Seeders\ProductCostSeeder::class)->run($company->id);
    app(Database\Seeders\ProductCostSeeder::class)->run($company->id);

    expect(OverheadConfig::where('company_id', $company->id)->count())->toBe(2)
        ->and(OverheadConfig::where('company_id', $company->id)->where('is_profit_margin', false)->count())->toBe(1)
        ->and(OverheadConfig::where('company_id', $company->id)->where('is_profit_margin', true)->count())->toBe(1);
});
