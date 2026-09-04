<?php

namespace App\Services\Sri;

use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Exception;

class SriSignatureService
{
    public function signXml(string $xmlContent, Company $company): string
    {
        if (empty($company->signature_path) || !Storage::disk('local')->exists($company->signature_path)) {
            throw new Exception("La empresa no tiene un archivo de firma (.p12) registrado.");
        }

        if (empty($company->signature_password)) {
            throw new Exception("No se ha configurado la contraseña de la firma.");
        }

        $p12AbsolutePath = Storage::disk('local')->path($company->signature_path);
        $password = $company->signature_password;

        return XadesBesSigner::sign($xmlContent, $p12AbsolutePath, $password);
    }
}
