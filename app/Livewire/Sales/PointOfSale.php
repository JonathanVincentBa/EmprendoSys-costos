<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Services\Sri\SriXmlService;
use App\Services\Sri\SriSignatureService;
use App\Services\Sri\SriWebService;
use App\Services\AuthorizedInvoiceEmailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PointOfSale extends Component
{
    public $customerSearch = '';
    public $selectedCustomer = null;
    public $isCustomerModalOpen = false;
    public $newCustomerName = '';
    public $newCustomerIdentificationType = '05';
    public $newCustomerIdentification = '';
    public $newCustomerEmail = '';
    public $newCustomerPhone = '';
    public $newCustomerAddress = '';
    public $productSearch = '';
    public $selectedProduct = null;
    public $items = [];
    public $quantity = 1;
    public $unit_price = 0;

    // SRI y Pagos
    public $payment_method_sri = '01'; // 01: Sin utilización del sistema financiero
    public $vat_rate = 15; // IVA Ecuador 15%

    public function selectCustomer($id)
    {
        $customer = Customer::where('company_id', Auth::user()->company_id)->find($id);
        if ($customer) {
            $this->selectedCustomer = $customer->toArray();
            $this->customerSearch = '';
        }
    }

    public function openCustomerModal()
    {
        $this->resetCustomerForm();
        $this->newCustomerName = trim($this->customerSearch);
        $this->isCustomerModalOpen = true;
    }

    public function closeCustomerModal()
    {
        $this->isCustomerModalOpen = false;
        $this->resetCustomerForm();
    }

    public function saveCustomer()
    {
        $this->validate([
            'newCustomerName' => 'required|string|min:3|max:255',
            'newCustomerIdentificationType' => 'required|in:04,05,06,07',
            'newCustomerIdentification' => 'required|string|max:13',
            'newCustomerEmail' => 'nullable|email|max:255',
            'newCustomerPhone' => 'nullable|string|max:30',
            'newCustomerAddress' => 'nullable|string|max:500',
        ]);

        $customer = Customer::create([
            'company_id' => Auth::user()->company_id,
            'name' => $this->newCustomerName,
            'identification_type' => $this->newCustomerIdentificationType,
            'identification' => $this->newCustomerIdentification,
            'email' => $this->newCustomerEmail ?: null,
            'phone' => $this->newCustomerPhone ?: null,
            'address' => $this->newCustomerAddress ?: null,
            'type' => 'minorista',
        ]);

        $this->selectedCustomer = $customer->toArray();
        $this->customerSearch = '';
        $this->closeCustomerModal();

        $this->dispatch('swal', [
            'message' => 'Cliente registrado y seleccionado en la factura.',
            'type' => 'success',
        ]);
    }

    private function resetCustomerForm()
    {
        $this->reset([
            'newCustomerName',
            'newCustomerIdentificationType',
            'newCustomerIdentification',
            'newCustomerEmail',
            'newCustomerPhone',
            'newCustomerAddress',
        ]);
        $this->newCustomerIdentificationType = '05';
        $this->resetValidation();
    }

    public function selectProduct($id)
    {
        $product = Product::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->find($id);

        if (!$product) {
            $this->dispatch('swal', ['message' => 'Producto no disponible', 'type' => 'error']);
            return;
        }

        $this->selectedProduct = $product;
        $this->unit_price = $product->price;
        $this->productSearch = $product->name;
        $this->resetErrorBag('selectedProduct');
    }

    public function updatedProductSearch($value): void
    {
        if (!$this->selectedProduct || $value !== $this->selectedProduct->name) {
            $this->selectedProduct = null;
            $this->unit_price = 0;
        }
    }

    public function addItem()
    {
        $productId = data_get($this->selectedProduct, 'id');
        if (!is_numeric($productId) || $this->productSearch !== $this->selectedProduct->name) {
            $this->addError('selectedProduct', 'Seleccione un producto.');
            return;
        }

        $this->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->findOrFail($productId);

        if ($this->quantity > $product->current_stock) {
            $this->dispatch('swal', ['message' => 'Stock insuficiente', 'type' => 'error']);
            return;
        }

        if ($product->price <= 0) {
            $this->dispatch('swal', [
                'message' => 'El producto no tiene un precio de venta válido.',
                'type' => 'error',
            ]);
            return;
        }

        $this->selectedProduct = $product;
        $unitPrice = round((float) $product->price, 2);
        $qty = (int) $this->quantity;
        $baseTotal = $qty * $unitPrice;

        $vatRate = 15;
        $vatRateDecimal = $vatRate / 100;
        $subtotal = $baseTotal / (1 + $vatRateDecimal);
        $vatAmount = $baseTotal - $subtotal;

        $this->items[] = [
            'product_id'  => $this->selectedProduct->id,
            'name'        => $this->selectedProduct->name,
            'quantity'    => $qty,
            'unit_price'  => $unitPrice,
            'subtotal'    => round($subtotal, 2),
            'vat_amount'  => round($vatAmount, 2),
            'total_price' => round($baseTotal, 2),
            'total'       => round($baseTotal, 2),
            'vat_rate'    => $vatRate,
            'vat_code'    => '4', // Código SRI para tarifa 15%
        ];

        $this->reset(['selectedProduct', 'quantity', 'unit_price', 'productSearch']);
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function store()
    {
        $this->validate([
            'selectedCustomer.id' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method_sri' => 'required|in:01,19,20',
        ]);

        $sale = null;

        try {
            DB::transaction(function () use (&$sale) {
                $companyId = Auth::user()->company_id;
                $customer = Customer::where('company_id', $companyId)
                    ->findOrFail($this->selectedCustomer['id']);
                $quantities = collect($this->items)
                    ->groupBy('product_id')
                    ->map(fn ($items) => $items->sum('quantity'));
                $products = Product::where('company_id', $companyId)
                    ->where('is_active', true)
                    ->whereIn('id', $quantities->keys())
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($products->count() !== $quantities->count()) {
                    throw new Exception('Uno o más productos ya no están disponibles.');
                }

                foreach ($quantities as $productId => $quantity) {
                    $product = $products->get($productId);
                    if ($product->price <= 0 || $quantity > $product->current_stock) {
                        throw new Exception(
                            $product->price <= 0
                                ? "El producto {$product->name} no tiene un precio válido."
                                : "Stock insuficiente para {$product->name}."
                        );
                    }
                }

                $saleItems = collect($this->items)->map(function ($item) use ($products) {
                    $product = $products->get($item['product_id']);
                    $quantity = (int) $item['quantity'];
                    $unitPrice = round((float) $product->price, 2);
                    $totalPrice = round($quantity * $unitPrice, 2);
                    $subtotal = round($totalPrice / 1.15, 2);
                    $vatAmount = round($totalPrice - $subtotal, 2);

                    return [
                        'product' => $product,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'subtotal' => $subtotal,
                        'vat_amount' => $vatAmount,
                    ];
                });

                $subtotal15 = $saleItems->sum('subtotal');
                $ivaAmount = $saleItems->sum('vat_amount');
                $total = $saleItems->sum('total_price');

                $sale = Sale::create([
                    'company_id'         => $companyId,
                    'customer_id'        => $customer->id,
                    'user_id'            => Auth::id(),
                    'payment_method_sri' => $this->payment_method_sri,
                    'sale_date'          => now(),
                    'sri_environment'    => Auth::user()->company->sri_environment ?? '1',
                    'subtotal_15'        => $subtotal15,
                    'subtotal_0'         => 0,
                    'iva_amount'         => $ivaAmount,
                    'total'              => $total,
                    'status'             => 'completed',
                    'sri_status'         => 'PENDING',
                ]);

                foreach ($saleItems as $item) {
                    SaleItem::create([
                        'company_id'  => $companyId,
                        'sale_id'     => $sale->id,
                        'product_id'  => $item['product']->id,
                        'quantity'    => $item['quantity'],
                        'unit_price'  => $item['unit_price'],
                        'total_price' => $item['total_price'],
                        'vat_code'    => '4',
                        'vat_rate'    => 15,
                        'vat_amount'  => $item['vat_amount'],
                    ]);

                }
            });

            $this->reset(['items', 'selectedCustomer', 'customerSearch', 'productSearch', 'selectedProduct']);

            // Procesar Emisión SRI
            if (!$sale) {
                throw new Exception('No se pudo crear la venta.');
            }

            $this->emitirSri($sale->id);
        } catch (Exception $e) {
            $this->dispatch('swal', [
                'message' => 'Error al procesar la venta: ' . $e->getMessage(),
                'type'    => 'error'
            ]);
        }
    }

    public function emitirSri($saleId)
    {
        $sale = Sale::with(['customer', 'company', 'items.product'])->find($saleId);

        if (!$sale) {
            $this->dispatch('swal', ['message' => 'Venta no encontrada', 'type' => 'error']);
            return;
        }

        try {
            $signatureService = app(SriSignatureService::class);
            $webService = app(SriWebService::class);

            $company = $sale->company;

            // Definir ambiente con fallback estricto ('1' = Pruebas, '2' = Producción)
            $environment = (string) ($company->sri_environment ?? '1');

            // 1. Generar Clave de Acceso
            $accessKey = SriXmlService::generateAccessKey($sale, $company);

            $sale->update([
                'sri_access_key'  => $accessKey,
                'sri_environment' => $environment,
            ]);

            // 2. Crear XML
            $xmlContent = SriXmlService::buildInvoiceXml($sale, $company, $accessKey);

            // 3. Firmar XML
            $xmlSigned = $signatureService->signXml($xmlContent, $company);

            // 4. Enviar a Recepción del SRI usando la variable $environment asegurada
            $receptionResult = $webService->sendXml($xmlSigned, $environment);

            if (($receptionResult['status'] ?? '') === 'RECIBIDA') {
                // 5. Consultar Autorización
                $authResult = $webService->authorizeInvoice($accessKey, $environment);
                $authorization = $authResult['response'] ?? null;
                $authorizationDetails = SriWebService::firstAuthorization($authorization);
                $estadoSri = (string) ($authorizationDetails->estado ?? 'EN PROCESO');

                if ($estadoSri === 'AUTORIZADO') {
                    $fechaAuth = $authorizationDetails->fechaAutorizacion ?? now();
                    $sale->update([
                        'sri_status'             => 'AUTORIZADO',
                        'sri_authorization_date' => $fechaAuth,
                        'sri_response'           => 'Comprobante Autorizado con éxito'
                    ]);

                    $emailMessage = $this->sendAuthorizedInvoiceEmail($sale, $xmlSigned);

                    $this->dispatch('swal', [
                        'message' => '¡Factura Electrónica AUTORIZADA por el SRI!' . $emailMessage,
                        'type'    => 'success'
                    ]);
                } elseif (($authResult['status'] ?? '') === 'ERROR') {
                    $sale->update([
                        'sri_status' => 'ERROR',
                        'sri_response' => $authResult['message'] ?? 'Error consultando autorización SRI'
                    ]);

                    $this->dispatch('swal', [
                        'message' => 'La factura fue recibida, pero no se pudo consultar su autorización: '
                            . ($authResult['message'] ?? 'Error desconocido'),
                        'type' => 'error'
                    ]);
                } else {
                    $sale->update([
                        'sri_status'   => $estadoSri ?: 'EN PROCESO',
                        'sri_response' => json_encode($authorization ?? 'Autorización pendiente')
                    ]);

                    $this->dispatch('swal', [
                        'message' => 'Comprobante recibido por el SRI. Autorización en proceso.',
                        'type'    => 'info'
                    ]);
                }
            } else {
                $sale->update([
                    'sri_status'   => $receptionResult['status'] ?? 'DEVUELTA',
                    'sri_response' => json_encode($receptionResult['response'] ?? $receptionResult['message'] ?? 'Error de Recepción')
                ]);

                $sriMessage = $this->formatSriResponse($receptionResult);

                $this->dispatch('swal', [
                    'message' => 'Comprobante devuelto por el SRI' . $sriMessage,
                    'type'    => 'error'
                ]);
            }
        } catch (Exception $e) {
            $sale->update([
                'sri_status'   => 'ERROR',
                'sri_response' => $e->getMessage()
            ]);

            $this->dispatch('swal', [
                'message' => 'Error en proceso SRI: ' . $e->getMessage(),
                'type'    => 'error'
            ]);
        }
    }

    private function sendAuthorizedInvoiceEmail(Sale $sale, string $signedXml): string
    {
        try {
            app(AuthorizedInvoiceEmailService::class)->send($sale, $signedXml);

            return ' PDF y XML enviados al correo del cliente.';
        } catch (Exception $exception) {
            report($exception);

            return ' La factura está autorizada, pero no se pudo enviar el correo: ' . $exception->getMessage();
        }
    }

    private function formatSriResponse(array $result): string
    {
        if (($result['status'] ?? '') === 'ERROR') {
            return ' | ' . ($result['message'] ?? 'Error de conexión con el SRI');
        }

        $payload = $result['response'] ?? null;
        if (is_object($payload)) {
            $payload = $payload->RespuestaRecepcionComprobante ?? $payload->respuestaSolicitud ?? $payload;
        } elseif (is_array($payload)) {
            $payload = $payload['RespuestaRecepcionComprobante']
                ?? $payload['respuestaSolicitud']
                ?? $payload;
        }

        $payload = json_decode(json_encode($payload), true) ?: [];
        $message = $this->findSriMessage($payload);

        if (!$message) {
            return ' | Respuesta SRI: ' . json_encode($payload, JSON_UNESCAPED_UNICODE);
        }

        if (isset($message[0])) {
            $message = $message[0];
        }

        $code = trim((string) ($message['identificador'] ?? ''));
        $description = trim((string) ($message['mensaje'] ?? ''));
        $additional = trim((string) ($message['informacionAdicional'] ?? ''));

        return ' | Código ' . ($code ?: 'N/D') . ': ' . ($description ?: 'Rechazo sin descripción')
            . ($additional ? ' | ' . $additional : '');
    }

    private function findSriMessage(array $payload): ?array
    {
        if (isset($payload['mensaje']) && is_string($payload['mensaje'])) {
            return $payload;
        }

        foreach ($payload as $value) {
            if (is_array($value)) {
                $message = $this->findSriMessage($value);
                if ($message) {
                    return $message;
                }
            }
        }

        return null;
    }

    public function render()
    {
        $userCompanyId = Auth::user()->company_id;

        $customers = collect();
        if (strlen($this->customerSearch) > 1) {
            $customers = Customer::query()
                ->where('company_id', $userCompanyId)
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->customerSearch . '%')
                        ->orWhere('identification', 'like', '%' . $this->customerSearch . '%');
                })
                ->limit(5)
                ->get();
        }

        $products = [];
        if (strlen($this->productSearch) > 1 && (!$this->selectedProduct || $this->productSearch !== $this->selectedProduct->name)) {
            $products = Product::query()
                ->where('company_id', $userCompanyId)
                ->where('is_active', true)
                ->where('current_stock', '>', 0)
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->productSearch . '%')
                        ->orWhere('sku', 'like', '%' . $this->productSearch . '%');
                })
                ->limit(5)
                ->get();
        }

        $subtotal = collect($this->items)->sum('subtotal');
        $iva = collect($this->items)->sum('vat_amount');
        $total = collect($this->items)->sum('total');

        return view('livewire.sales.point-of-sale', [
            'customers' => $customers,
            'products'  => $products,
            'subtotal'  => $subtotal,
            'iva'       => $iva,
            'total'     => $total,
        ]);
    }
}
