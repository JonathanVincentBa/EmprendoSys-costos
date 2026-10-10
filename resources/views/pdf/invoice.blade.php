<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Factura {{ $sale->sri_access_key }}</title>
    <style>
        @page { margin: 28px 32px; }
        body { color: #202938; font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        .header { border-bottom: 2px solid #263b63; padding-bottom: 16px; width: 100%; }
        .header td { vertical-align: top; }
        .logo { max-height: 72px; max-width: 150px; }
        .company-name { color: #263b63; font-size: 19px; font-weight: bold; margin: 0 0 5px; }
        .company-details { font-size: 9px; line-height: 1.6; }
        .invoice-box { border: 1px solid #263b63; border-radius: 5px; padding: 12px; }
        .invoice-title { color: #263b63; font-size: 15px; font-weight: bold; margin: 0 0 8px; }
        .section { background: #eef2f8; color: #263b63; font-weight: bold; margin: 18px 0 8px; padding: 7px; }
        .details { width: 100%; border-collapse: collapse; }
        .details td { padding: 4px 2px; vertical-align: top; }
        .items { border-collapse: collapse; margin-top: 5px; width: 100%; }
        .items th { background: #263b63; color: #fff; font-weight: bold; padding: 8px 5px; text-align: left; }
        .items td { border-bottom: 1px solid #d9dee7; padding: 7px 5px; }
        .right { text-align: right !important; }
        .center { text-align: center !important; }
        .totals { border-collapse: collapse; margin: 12px 0 0 auto; width: 42%; }
        .totals td { border-bottom: 1px solid #e1e5eb; padding: 6px; }
        .grand-total { background: #eef2f8; color: #263b63; font-size: 12px; font-weight: bold; }
        .access-key { border: 1px solid #d9dee7; font-family: DejaVu Sans, sans-serif; font-size: 8px; margin-top: 18px; padding: 8px; word-wrap: break-word; }
        .footer { color: #657084; font-size: 8px; margin-top: 18px; text-align: center; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="width: 34%;">
                @if($logoDataUri)
                    <img class="logo" src="{{ $logoDataUri }}" alt="Logo {{ $company->name }}">
                @endif
            </td>
            <td style="width: 66%;">
                <div class="invoice-box">
                    <p class="invoice-title">FACTURA</p>
                    <div><strong>RUC:</strong> {{ $company->ruc }}</div>
                    <div><strong>No.:</strong> {{ $company->estab ?? '001' }}-{{ $company->pto_emi ?? '001' }}-{{ str_pad((string) $sale->id, 9, '0', STR_PAD_LEFT) }}</div>
                    <div><strong>Fecha de emisión:</strong> {{ optional($sale->sale_date)->timezone('America/Guayaquil')->format('d/m/Y') }}</div>
                    <div><strong>Ambiente:</strong> {{ $sale->sri_environment === '2' ? 'Producción' : 'Pruebas' }}</div>
                    @if($sale->sri_authorization_date)
                        <div><strong>Autorización:</strong> {{ $sale->sri_authorization_date->timezone('America/Guayaquil')->format('d/m/Y H:i:s') }}</div>
                    @endif
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top: 12px;">
                <div class="company-name">{{ $company->name }}</div>
                <div class="company-details">
                    <strong>Razón social:</strong> {{ $company->razon_social ?: $company->name }}<br>
                    <strong>Matriz:</strong> {{ $company->address ?: 'No registrada' }}<br>
                    <strong>Establecimiento:</strong> {{ $company->establishment_address ?: ($company->address ?: 'No registrado') }}<br>
                    @if($company->phone)<strong>Teléfono:</strong> {{ $company->phone }}@endif
                    @if($company->email) &nbsp; <strong>Correo:</strong> {{ $company->email }}@endif
                    @if($company->contribuyente_especial)<br><strong>Contribuyente especial No.:</strong> {{ $company->contribuyente_especial }}@endif
                    <br><strong>Obligado a llevar contabilidad:</strong> {{ $company->obligado_contabilidad ?? 'NO' }}
                    @if($company->contribuyente_rimpe)<br><strong>{{ $company->contribuyente_rimpe }}</strong>@endif
                </div>
            </td>
        </tr>
    </table>

    <div class="section">INFORMACIÓN DEL CLIENTE</div>
    <table class="details">
        <tr>
            <td style="width: 58%;"><strong>Cliente / Razón social:</strong> {{ $sale->customer?->name ?? 'Consumidor Final' }}</td>
            <td><strong>Identificación:</strong> {{ $sale->customer?->identification ?: '9999999999999' }}</td>
        </tr>
        <tr>
            <td><strong>Dirección:</strong> {{ $sale->customer?->address ?: 'No registrada' }}</td>
            <td><strong>Correo:</strong> {{ $sale->customer?->email ?: 'No registrado' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Forma de pago SRI:</strong> {{ $sale->payment_method_sri ?? '01' }}</td>
        </tr>
    </table>

    <div class="section">DETALLE</div>
    <table class="items">
        <thead>
            <tr>
                <th style="width: 8%;" class="center">Cant.</th>
                <th style="width: 42%;">Descripción</th>
                <th style="width: 15%;" class="right">P. unitario</th>
                <th style="width: 15%;" class="right">Subtotal</th>
                <th style="width: 10%;" class="right">IVA</th>
                <th style="width: 10%;" class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                @php
                    $lineTotal = (float) $item->total_price;
                    $lineTax = (float) $item->vat_amount;
                    $lineSubtotal = $lineTotal - $lineTax;
                @endphp
                <tr>
                    <td class="center">{{ $item->quantity }}</td>
                    <td>{{ $item->product?->name ?? 'Producto' }}</td>
                    <td class="right">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="right">${{ number_format($lineSubtotal, 2) }}</td>
                    <td class="right">${{ number_format($lineTax, 2) }}</td>
                    <td class="right">${{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal IVA 15%</td><td class="right">${{ number_format($sale->subtotal_15, 2) }}</td></tr>
        <tr><td>Subtotal IVA 0%</td><td class="right">${{ number_format($sale->subtotal_0, 2) }}</td></tr>
        <tr><td>Descuento</td><td class="right">${{ number_format($sale->discount_amount, 2) }}</td></tr>
        <tr><td>IVA</td><td class="right">${{ number_format($sale->iva_amount, 2) }}</td></tr>
        <tr class="grand-total"><td>TOTAL</td><td class="right">${{ number_format($sale->total, 2) }}</td></tr>
    </table>

    <div class="access-key">
        <strong>CLAVE DE ACCESO</strong><br>
        {{ $sale->sri_access_key }}<br>
        <strong>Estado:</strong> {{ $sale->sri_status }}
    </div>

    <div class="footer">Comprobante electrónico autorizado por el Servicio de Rentas Internas del Ecuador.</div>
</body>
</html>
