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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PointOfSale extends Component
{
    public $customerSearch = '';
    public $selectedCustomer = null;
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
        $customer = Customer::find($id);
        if ($customer) {
            $this->selectedCustomer = $customer->toArray();
            $this->customerSearch = '';
        }
    }

    public function selectProduct($id)
    {
        $product = Product::find($id);
        if ($product) {
            $this->selectedProduct = $product;
            $this->unit_price = $product->price ?? 0;
            $this->productSearch = $product->name;
        }
    }

    public function addItem()
    {
        if (!$this->selectedProduct) {
            $this->dispatch('swal', ['message' => 'Seleccione un producto', 'type' => 'warning']);
            return;
        }

        if ($this->quantity > $this->selectedProduct->current_stock) {
            $this->dispatch('swal', ['message' => 'Stock insuficiente', 'type' => 'error']);
            return;
        }

        $unitPrice = floatval($this->unit_price);
        $qty = intval($this->quantity);
        $baseTotal = $qty * $unitPrice;

        $vatRateDecimal = $this->vat_rate / 100;
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
            'vat_rate'    => $this->vat_rate,
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
        if (!$this->selectedCustomer) {
            $this->dispatch('swal', ['message' => 'Debe seleccionar un cliente', 'type' => 'warning']);
            return;
        }

        if (empty($this->items)) {
            $this->dispatch('swal', ['message' => 'El carrito está vacío', 'type' => 'warning']);
            return;
        }

        $sale = null;

        try {
            DB::transaction(function () use (&$sale) {
                $subtotal15 = collect($this->items)->sum('subtotal');
                $ivaAmount = collect($this->items)->sum('vat_amount');
                $total = collect($this->items)->sum('total');

                $sale = Sale::create([
                    'company_id'         => Auth::user()->company_id,
                    'customer_id'        => $this->selectedCustomer['id'],
                    'user_id'            => Auth::id(),
                    'payment_method_sri' => $this->payment_method_sri,
                    'sale_date'          => now(),
                    'subtotal_15'        => $subtotal15,
                    'subtotal_0'         => 0,
                    'iva_amount'         => $ivaAmount,
                    'total'              => $total,
                    'status'             => 'completed',
                    'sri_status'         => 'PENDING',
                ]);

                foreach ($this->items as $item) {
                    SaleItem::create([
                        'company_id'  => Auth::user()->company_id,
                        'sale_id'     => $sale->id,
                        'product_id'  => $item['product_id'],
                        'quantity'    => $item['quantity'],
                        'unit_price'  => $item['unit_price'],
                        'total_price' => $item['total_price'],
                        'vat_code'    => $item['vat_code'],
                        'vat_rate'    => $item['vat_rate'],
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
                $estadoSri = (string) ($authorization->autorizaciones->autorizacion->estado ?? 'EN PROCESO');

                if ($estadoSri === 'AUTORIZADO') {
                    $fechaAuth = $authorization->autorizaciones->autorizacion->fechaAutorizacion ?? now();
                    $sale->update([
                        'sri_status'             => 'AUTORIZADO',
                        'sri_authorization_date' => $fechaAuth,
                        'sri_response'           => 'Comprobante Autorizado con éxito'
                    ]);

                    $this->dispatch('swal', [
                        'message' => '¡Factura Electrónica AUTORIZADA por el SRI!',
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

        $customers = [];
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
                ->where('current_stock', '>', 0)
                ->where('name', 'like', '%' . $this->productSearch . '%')
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
