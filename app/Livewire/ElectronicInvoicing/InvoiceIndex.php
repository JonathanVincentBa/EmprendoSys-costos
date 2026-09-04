<?php

namespace App\Livewire\ElectronicInvoicing;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sale;
use App\Services\Sri\SriXmlService;
use App\Services\Sri\SriSignatureService;
use App\Services\Sri\SriWebService;
use Illuminate\Support\Facades\Auth;
use Exception;

class InvoiceIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function reemitirSri($saleId)
    {
        $sale = Sale::with(['customer', 'company', 'items.product'])->find($saleId);

        if (!$sale) {
            $this->dispatch('swal', ['message' => 'Comprobante no encontrado', 'type' => 'error']);
            return;
        }

        try {
            $signatureService = app(SriSignatureService::class);
            $webService = app(SriWebService::class);

            $company = $sale->company;
            $environment = (string) ($company->sri_environment ?? '1');

            if (empty($sale->sri_access_key)) {
                $accessKey = SriXmlService::generateAccessKey($sale, $company);
                $sale->update([
                    'sri_access_key'  => $accessKey,
                    'sri_environment' => $environment,
                ]);
            } else {
                $accessKey = $sale->sri_access_key;
            }

            $xmlContent = SriXmlService::buildInvoiceXml($sale, $company, $accessKey);
            $xmlSigned = $signatureService->signXml($xmlContent, $company);
            $receptionResult = $webService->sendXml($xmlSigned, $environment);

            if (($receptionResult['status'] ?? '') === 'RECIBIDA') {
                $authResult = $webService->authorizeInvoice($accessKey, $environment);
                $authorization = $authResult['response'] ?? null;
                $estadoSri = (string) ($authorization->autorizaciones->autorizacion->estado ?? 'EN PROCESO');

                if ($estadoSri === 'AUTORIZADO') {
                    $fechaAuth = $authResult['response']->autorizaciones->autorizacion->fechaAutorizacion ?? now();
                    $sale->update([
                        'sri_status'             => 'AUTORIZADO',
                        'sri_authorization_date' => $fechaAuth,
                        'sri_response'           => 'Comprobante Autorizado con éxito'
                    ]);

                    $this->dispatch('swal', [
                        'message' => '¡Factura N° ' . $sale->id . ' AUTORIZADA exitosamente!',
                        'type'    => 'success'
                    ]);
                } else {
                    $sale->update([
                        'sri_status'   => $estadoSri === 'NO AUTORIZADO' ? 'DEVUELTA' : 'EN PROCESO',
                        'sri_response' => json_encode($authorization ?? 'Autorización pendiente')
                    ]);

                    $this->dispatch('swal', [
                        'message' => $estadoSri === 'NO AUTORIZADO'
                            ? 'Comprobante no autorizado por el SRI: revise el detalle de la respuesta.'
                            : 'Comprobante recibido por el SRI, en estado ' . $estadoSri,
                        'type'    => $estadoSri === 'NO AUTORIZADO' ? 'error' : 'warning'
                    ]);
                }
            } else {
                $responseObj = $receptionResult['response'] ?? null;
                $errorMessage = 'Comprobante devuelto por el SRI';

                if ($responseObj && isset($responseObj->comprobantes->comprobante->mensajes->mensaje)) {
                    $mensajes = $responseObj->comprobantes->comprobante->mensajes->mensaje;
                    if (!is_array($mensajes)) {
                        $mensajes = [$mensajes];
                    }
                    $detalles = [];
                    foreach ($mensajes as $msg) {
                        $txt = $msg->mensaje ?? '';
                        if (!empty($msg->informacionAdicional)) {
                            $txt .= ' (' . $msg->informacionAdicional . ')';
                        }
                        $detalles[] = $txt;
                    }
                    if (!empty($detalles)) {
                        $errorMessage .= ': ' . implode(' | ', $detalles);
                    }
                }

                $sale->update([
                    'sri_status'   => $receptionResult['status'] ?? 'DEVUELTA',
                    'sri_response' => json_encode($responseObj)
                ]);

                $this->dispatch('swal', [
                    'message' => $errorMessage,
                    'type'    => 'error'
                ]);
            }
        } catch (Exception $e) {
            $sale->update([
                'sri_status'   => 'ERROR',
                'sri_response' => $e->getMessage()
            ]);

            $this->dispatch('swal', [
                'message' => 'Error al procesar: ' . $e->getMessage(),
                'type'    => 'error'
            ]);
        }
    }

    public function render()
    {
        $sales = Sale::with(['customer'])
            ->where('company_id', Auth::user()->company_id)
            ->when($this->statusFilter, function ($query) {
                $query->where('sri_status', $this->statusFilter);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('id', 'like', '%' . $this->search . '%')
                        ->orWhere('sri_access_key', 'like', '%' . $this->search . '%')
                        ->orWhereHas('customer', function ($c) {
                            $c->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('identification', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.electronic-invoicing.invoice-index', [
            'sales' => $sales
        ]);
    }
}
