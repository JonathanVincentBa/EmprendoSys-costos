<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Factura electrónica autorizada</title>
</head>
<body style="font-family: Arial, sans-serif; color: #27272a; line-height: 1.5;">
    <h2>{{ $sale->company?->name ?? 'Factura electrónica autorizada' }}</h2>
    <p>Hola {{ $sale->customer?->name ?? 'cliente' }},</p>
    <p>Adjuntamos tu factura electrónica autorizada por el SRI en formato PDF y su XML firmado.</p>
    <p><strong>Emisor:</strong> {{ $sale->company?->razon_social ?: $sale->company?->name }}<br>
        <strong>RUC:</strong> {{ $sale->company?->ruc }}<br>
        <strong>Número de factura:</strong> {{ $sale->company?->estab ?? '001' }}-{{ $sale->company?->pto_emi ?? '001' }}-{{ str_pad((string) $sale->id, 9, '0', STR_PAD_LEFT) }}<br>
        <strong>Clave de acceso:</strong> {{ $sale->sri_access_key }}<br>
        <strong>Total:</strong> ${{ number_format($sale->total, 2) }}</p>
    <p>Gracias por tu compra.</p>
</body>
</html>
