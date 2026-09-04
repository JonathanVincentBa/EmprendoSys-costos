<?php

namespace App\Services\Sri;

use App\Models\Sale;
use App\Models\Company;
use SimpleXMLElement;
use Exception;

class SriXmlService
{
    public static function generateAccessKey(Sale $sale, Company $company): string
    {
        $date = now('America/Guayaquil')->format('dmY');
        $type = '01'; // Factura
        $ruc = str_pad($company->ruc, 13, '0', STR_PAD_LEFT);
        $environment = $company->sri_environment ?? '1';

        $estab = str_pad($company->estab ?? '001', 3, '0', STR_PAD_LEFT);
        $ptoEmi = str_pad($company->pto_emi ?? '001', 3, '0', STR_PAD_LEFT);
        $secuencial = str_pad($sale->id, 9, '0', STR_PAD_LEFT);

        $numericCode = str_pad($sale->id, 8, '0', STR_PAD_LEFT);
        $emissionType = '1';

        $baseKey = $date . $type . $ruc . $environment . $estab . $ptoEmi . $secuencial . $numericCode . $emissionType;
        $verifierDigit = self::calculateModule11($baseKey);

        return $baseKey . $verifierDigit;
    }

    private static function calculateModule11(string $key): int
    {
        $factor = 2;
        $sum = 0;

        for ($i = strlen($key) - 1; $i >= 0; $i--) {
            $sum += (int)$key[$i] * $factor;
            $factor = ($factor == 7) ? 2 : $factor + 1;
        }

        $remainder = $sum % 11;
        $digit = 11 - $remainder;

        if ($digit == 11) return 0;
        if ($digit == 10) return 1;

        return $digit;
    }

    public static function buildInvoiceXml(Sale $sale, Company $company, string $accessKey): string
    {
        $customer = $sale->customer;

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><factura id="comprobante" version="1.1.0"></factura>');

        // 1. infoTributaria
        $infoTributaria = $xml->addChild('infoTributaria');
        $infoTributaria->addChild('ambiente', $company->sri_environment ?? '1');
        $infoTributaria->addChild('tipoEmision', '1');
        $infoTributaria->addChild('razonSocial', $company->razon_social ?? $company->name);
        $infoTributaria->addChild('nombreComercial', $company->name);
        $infoTributaria->addChild('ruc', $company->ruc);
        $infoTributaria->addChild('claveAcceso', $accessKey);
        $infoTributaria->addChild('codDoc', '01');
        $infoTributaria->addChild('estab', str_pad($company->estab ?? '001', 3, '0', STR_PAD_LEFT));
        $infoTributaria->addChild('ptoEmi', str_pad($company->pto_emi ?? '001', 3, '0', STR_PAD_LEFT));
        $infoTributaria->addChild('secuencial', str_pad($sale->id, 9, '0', STR_PAD_LEFT));
        $infoTributaria->addChild('dirMatriz', $company->address ?? 'N/A');

        if (!empty($company->contribuyente_rimpe)) {
            $infoTributaria->addChild('contribuyenteRimpe', $company->contribuyente_rimpe);
        }

        // 2. infoFactura
        $infoFactura = $xml->addChild('infoFactura');
        $emissionDate = now('America/Guayaquil');
        $infoFactura->addChild('fechaEmision', $emissionDate->format('d/m/Y'));
        $infoFactura->addChild('dirEstablecimiento', $company->establishment_address ?? $company->address ?? 'N/A');

        if (!empty($company->contribuyente_especial)) {
            $infoFactura->addChild('contribuyenteEspecial', $company->contribuyente_especial);
        }

        $infoFactura->addChild('obligadoContabilidad', $company->obligado_contabilidad ?? 'NO');

        $tipoIdentificacion = match (strtoupper((string) ($customer->identification_type ?? '07'))) {
            '04', 'RUC' => '04',
            '05', 'CEDULA', 'CÉDULA' => '05',
            '06', 'PASAPORTE' => '06',
            '07', 'CONSUMIDOR FINAL' => '07',
            default => '07',
        };

        $infoFactura->addChild('tipoIdentificacionComprador', $tipoIdentificacion);
        $infoFactura->addChild('razonSocialComprador', $customer->name ?? 'CONSUMIDOR FINAL');
        $infoFactura->addChild('identificacionComprador', $customer->identification ?? '9999999999999');
        $infoFactura->addChild('direccionComprador', $customer->address ?? 'N/A');
        $subtotal15 = (float) ($sale->subtotal_15 ?? 0);
        $subtotal0 = (float) ($sale->subtotal_0 ?? 0);
        $iva = (float) ($sale->iva_amount ?? 0);
        $infoFactura->addChild('totalSinImpuestos', number_format($subtotal15 + $subtotal0, 2, '.', ''));
        $infoFactura->addChild('totalDescuento', number_format($sale->discount_amount ?? 0, 2, '.', ''));

        // Agrupación de Totales por Impuesto (IVA)
        $totalConImpuestos = $infoFactura->addChild('totalConImpuestos');

        $taxCodePercentage = $iva > 0 ? '4' : '0';
        $taxRate = $iva > 0 ? 15 : 0;

        $totalImpuesto = $totalConImpuestos->addChild('totalImpuesto');
        $totalImpuesto->addChild('codigo', '2'); // Código 2 = IVA
        $totalImpuesto->addChild('codigoPorcentaje', $taxCodePercentage);
        $totalImpuesto->addChild('baseImponible', number_format($subtotal15, 2, '.', ''));
        $totalImpuesto->addChild('valor', number_format($iva, 2, '.', ''));

        $infoFactura->addChild('propina', '0.00');
        $infoFactura->addChild('importeTotal', number_format($sale->total, 2, '.', ''));
        $infoFactura->addChild('moneda', 'DOLAR');

        // Formas de Pago
        $pagos = $infoFactura->addChild('pagos');
        $pago = $pagos->addChild('pago');
        $pago->addChild('formaPago', $sale->payment_method_sri ?? '01');
        $pago->addChild('total', number_format($sale->total, 2, '.', ''));

        // 3. Detalles
        $detalles = $xml->addChild('detalles');
        $items = $sale->items ?? [];

        foreach ($items as $item) {
            $detalle = $detalles->addChild('detalle');
            $detalle->addChild('codigoPrincipal', 'PROD-' . $item->product_id);
            $detalle->addChild('descripcion', $item->product->name ?? 'Producto');
            $detalle->addChild('cantidad', number_format($item->quantity, 2, '.', ''));
            $itemTaxRate = (float) ($item->vat_rate ?? $taxRate);
            $itemTaxValue = (float) ($item->vat_amount ?? 0);
            $itemSubtotal = (float) $item->total_price - $itemTaxValue;
            $unitPriceWithoutTax = (float) $item->quantity > 0
                ? $itemSubtotal / (float) $item->quantity
                : 0;
            $detalle->addChild('precioUnitario', number_format($unitPriceWithoutTax, 4, '.', ''));
            $detalle->addChild('descuento', number_format($item->discount ?? 0, 2, '.', ''));
            $detalle->addChild('precioTotalSinImpuesto', number_format($itemSubtotal, 2, '.', ''));

            $impuestos = $detalle->addChild('impuestos');
            $impuesto = $impuestos->addChild('impuesto');
            $impuesto->addChild('codigo', '2');
            $itemTaxCode = (string) ($item->vat_code ?? $taxCodePercentage);
            $impuesto->addChild('codigoPorcentaje', $itemTaxCode);
            $impuesto->addChild('tarifa', number_format($itemTaxRate, 2, '.', ''));
            $impuesto->addChild('baseImponible', number_format($itemSubtotal, 2, '.', ''));
            $impuesto->addChild('valor', number_format($itemTaxValue, 2, '.', ''));
        }

        // 4. Información Adicional
        if ($customer && $customer->email) {
            $infoAdicional = $xml->addChild('infoAdicional');
            $campoAdicional = $infoAdicional->addChild('campoAdicional', $customer->email);
            $campoAdicional->addAttribute('nombre', 'Email');
        }

        return $xml->asXML();
    }
}
