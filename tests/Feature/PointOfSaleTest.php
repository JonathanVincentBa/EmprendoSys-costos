<?php

use App\Livewire\ElectronicInvoicing\InvoiceIndex;
use App\Livewire\Sales\PointOfSale;
use App\Mail\AuthorizedInvoiceMail;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Services\Sri\SriSignatureService;
use App\Services\Sri\SriWebService;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->company = Company::create([
        'name' => 'Empresa POS',
        'ruc' => '1799999999001',
        'email' => 'pos@example.com',
        'sri_environment' => '2',
    ]);
    $this->user = User::factory()->create(['company_id' => $this->company->id]);
    $this->user->assignRole(Role::findOrCreate('admin', 'web'));
    $this->customer = Customer::create([
        'company_id' => $this->company->id,
        'name' => 'Cliente POS',
        'identification' => '0999999999',
        'identification_type' => '05',
        'email' => 'cliente@example.com',
    ]);
    $this->product = Product::create([
        'company_id' => $this->company->id,
        'name' => 'Producto POS',
        'presentation_ml' => 500,
        'packaging_type' => 'frasco',
        'price' => 11.50,
        'current_stock' => 10,
        'is_active' => true,
    ]);
});

test('the cart uses the database price instead of a client supplied price', function () {
    $this->actingAs($this->user);

    Livewire::test(PointOfSale::class)
        ->call('selectProduct', $this->product->id)
        ->set('unit_price', 0.01)
        ->set('quantity', 1)
        ->call('addItem')
        ->assertSet('items.0.unit_price', 11.5)
        ->assertSet('items.0.total', 11.5);
});

test('product search finds products by SKU after the search value changes', function () {
    $this->actingAs($this->user);
    $this->product->update(['sku' => 'SKU-CAFE-01']);

    Livewire::test(PointOfSale::class)
        ->set('productSearch', 'SKU-CAFE-01')
        ->assertSee('Producto POS')
        ->assertSee('SKU-CAFE-01');
});

test('changing the product search clears the previous selection', function () {
    $this->actingAs($this->user);

    Livewire::test(PointOfSale::class)
        ->call('selectProduct', $this->product->id)
        ->set('productSearch', 'Otro producto')
        ->assertSet('selectedProduct', null)
        ->call('addItem')
        ->assertHasErrors('selectedProduct')
        ->assertSet('items', []);
});

test('checkout recalculates totals and stock from authoritative records', function () {
    $this->actingAs($this->user);

    Livewire::test(PointOfSale::class)
        ->set('selectedCustomer', [
            'id' => $this->customer->id,
            'name' => 'Manipulado',
            'identification' => '0000000000',
        ])
        ->set('items', [[
            'product_id' => $this->product->id,
            'name' => 'Manipulado',
            'quantity' => 2,
            'unit_price' => 0.01,
            'subtotal' => 0.01,
            'vat_amount' => 0,
            'total_price' => 0.02,
            'total' => 0.02,
            'vat_rate' => 0,
            'vat_code' => '0',
        ]])
        ->call('store');

    $sale = Sale::query()->latest('id')->firstOrFail();
    $saleItem = SaleItem::query()->where('sale_id', $sale->id)->firstOrFail();

    expect((float) $sale->total)->toBe(23.00)
        ->and((float) $sale->subtotal_15)->toBe(20.00)
        ->and((float) $sale->iva_amount)->toBe(3.00)
        ->and((float) $saleItem->unit_price)->toBe(11.50)
        ->and($sale->sri_environment)->toBe('2')
        ->and($this->product->fresh()->current_stock)->toBe(8);
});

test('an authorized invoice is emailed to the customer with its signed XML', function () {
    $this->actingAs($this->user);
    $this->company->update([
        'mail_host' => 'smtp.example.com',
        'mail_port' => 587,
        'mail_username' => 'billing@example.com',
        'mail_password' => 'smtp-secret',
        'mail_encryption' => 'tls',
        'mail_from_name' => 'Empresa POS',
    ]);
    Mail::fake();
    $this->mock(SriSignatureService::class)
        ->shouldReceive('signXml')
        ->once()
        ->andReturn('<signed-invoice/>');
    $webService = $this->mock(SriWebService::class);
    $webService
        ->shouldReceive('sendXml')
        ->once()
        ->andReturn(['status' => 'RECIBIDA', 'response' => (object) []]);
    $webService
        ->shouldReceive('authorizeInvoice')
        ->once()
        ->andReturn([
            'status' => 'SUCCESS',
            'response' => (object) [
                'autorizaciones' => (object) [
                    'autorizacion' => [
                        (object) [
                            'estado' => 'AUTORIZADO',
                            'fechaAutorizacion' => '2026-10-08T12:00:00-05:00',
                        ],
                    ],
                ],
            ],
        ]);

    Livewire::test(PointOfSale::class)
        ->set('selectedCustomer', $this->customer->toArray())
        ->set('items', [[
            'product_id' => $this->product->id,
            'name' => $this->product->name,
            'quantity' => 1,
            'unit_price' => 11.5,
            'subtotal' => 10,
            'vat_amount' => 1.5,
            'total_price' => 11.5,
            'total' => 11.5,
            'vat_rate' => 15,
            'vat_code' => '4',
        ]])
        ->call('store');

    expect(Sale::query()->latest('id')->value('sri_status'))->toBe('AUTORIZADO');
    Mail::assertSent(AuthorizedInvoiceMail::class, function ($mail) {
        expect($mail->hasTo('cliente@example.com'))->toBeTrue()
            ->and(str_starts_with($mail->pdfContent, '%PDF-'))->toBeTrue()
            ->and(count($mail->attachments()))->toBe(2);

        $mail->assertHasAttachment($mail->attachments()[0]);
        $mail->assertHasAttachment($mail->attachments()[1]);

        return true;
    });
});

test('an authorized invoice can be resent by email without resubmitting it to SRI', function () {
    $this->actingAs($this->user);
    $this->company->update([
        'mail_host' => 'smtp.example.com',
        'mail_port' => 587,
        'mail_username' => 'billing@example.com',
        'mail_password' => 'smtp-secret',
        'mail_encryption' => 'tls',
    ]);
    $sale = Sale::create([
        'company_id' => $this->company->id,
        'customer_id' => $this->customer->id,
        'user_id' => $this->user->id,
        'sale_date' => now(),
        'total' => 11.50,
        'subtotal_15' => 10,
        'iva_amount' => 1.50,
        'status' => 'completed',
        'sri_access_key' => str_repeat('1', 49),
        'sri_environment' => '2',
        'sri_status' => 'AUTORIZADO',
    ]);
    SaleItem::create([
        'company_id' => $this->company->id,
        'sale_id' => $sale->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 11.50,
        'total_price' => 11.50,
        'vat_code' => '4',
        'vat_rate' => 15,
        'vat_amount' => 1.50,
    ]);
    Mail::fake();
    $this->mock(SriSignatureService::class)
        ->shouldReceive('signXml')
        ->once()
        ->andReturn('<signed-invoice/>');

    Livewire::test(InvoiceIndex::class)
        ->assertSee('Reenviar correo')
        ->call('reenviarCorreo', $sale->id)
        ->assertDispatched('swal');

    expect($sale->fresh()->sri_status)->toBe('AUTORIZADO');
    Mail::assertSent(AuthorizedInvoiceMail::class, function ($mail) {
        expect($mail->hasTo('cliente@example.com'))->toBeTrue()
            ->and(str_starts_with($mail->pdfContent, '%PDF-'))->toBeTrue()
            ->and(count($mail->attachments()))->toBe(2);

        return true;
    });
});

test('authorized invoice cannot be sent to the SRI again from the retry action', function () {
    $this->actingAs($this->user);
    $sale = Sale::create([
        'company_id' => $this->company->id,
        'customer_id' => $this->customer->id,
        'user_id' => $this->user->id,
        'sale_date' => now(),
        'total' => 11.50,
        'status' => 'completed',
        'sri_access_key' => str_repeat('2', 49),
        'sri_environment' => '2',
        'sri_status' => 'AUTORIZADO',
    ]);
    $webService = $this->mock(SriWebService::class);
    $webService->shouldNotReceive('sendXml');

    Livewire::test(InvoiceIndex::class)
        ->assertSee('Reenviar correo')
        ->assertDontSee('Reintentar SRI')
        ->call('reemitirSri', $sale->id)
        ->assertDispatched('swal');

    expect($sale->fresh()->sri_status)->toBe('AUTORIZADO');
});
