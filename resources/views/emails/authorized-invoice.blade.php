<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Factura electrónica autorizada</title>
</head>
<body style="font-family: Arial, sans-serif; color: #27272a; line-height: 1.5;">
    <h2>Factura electrónica autorizada</h2>
    <p>Hola {{ $sale->customer?->name ?? 'cliente' }},</p>
    <p>Tu factura electrónica fue autorizada por el SRI.</p>
    <p><strong>Número de venta:</strong> #{{ $sale->id }}<br>
        <strong>Clave de acceso:</strong> {{ $sale->sri_access_key }}<br>
        <strong>Total:</strong> ${{ number_format($sale->total, 2) }}</p>
    <p>Adjuntamos el XML firmado del comprobante.</p>
    <p>Gracias por tu compra.</p>
</body>
</html>
