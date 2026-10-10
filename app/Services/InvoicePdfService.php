<?php

namespace App\Services;

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class InvoicePdfService
{
    public function render(Sale $sale): string
    {
        $sale->loadMissing(['company', 'customer', 'items.product']);
        $logoDataUri = null;
        $logoPath = $sale->company?->logo;

        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $absolutePath = Storage::disk('public')->path($logoPath);
            $mimeType = mime_content_type($absolutePath);
            $logoContent = file_get_contents($absolutePath);

            if (!$mimeType || $logoContent === false) {
                throw new RuntimeException('No se pudo leer el logotipo de la empresa para generar la factura PDF.');
            }

            $logoDataUri = 'data:' . $mimeType . ';base64,' . base64_encode($logoContent);
        }

        return Pdf::loadView('pdf.invoice', [
            'sale' => $sale,
            'company' => $sale->company,
            'logoDataUri' => $logoDataUri,
        ])->setPaper('a4')->output();
    }
}
