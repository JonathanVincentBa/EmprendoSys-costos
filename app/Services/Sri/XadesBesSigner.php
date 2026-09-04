<?php

namespace App\Services\Sri;

use DOMDocument;
use Exception;

class XadesBesSigner
{
    /**
     * Firma un XML en formato XAdES-BES usando un archivo PKCS12 (.p12/.pfx)
     */
    public static function sign(string $xmlContent, string $p12Path, string $p12Password): string
    {
        if (!file_exists($p12Path)) {
            throw new Exception("El archivo de firma .p12 no existe en la ruta especificada.");
        }

        $p12Content = file_get_contents($p12Path);
        $certs = [];

        if (!openssl_pkcs12_read($p12Content, $certs, $p12Password)) {
            throw new Exception("No se pudo leer la firma .p12. Verifica la contraseña ingresada.");
        }

        $privateKey = $certs['pkey'];
        $x509Cert = $certs['cert'];

        // Limpiar el certificado (extraer solo Base64 sin encabezados PEM)
        $certBase64 = str_replace(["-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----", "\r", "\n", " "], '', $x509Cert);

        // Obtener la información del certificado para el SignatureProperties/SigningCertificate
        $certData = openssl_x509_parse($x509Cert);
        if ($certData === false || !isset($certData['issuer']) || !is_array($certData['issuer'])) {
            throw new Exception("No se pudo analizar el certificado digital.");
        }

        $issuerX509 = [];
        foreach ($certData['issuer'] as $key => $val) {
            $issuerX509[] = "$key=$val";
        }
        $issuerName = implode(',', $issuerX509);
        $serialNumber = self::hexToDecimal((string) $certData['serialNumber']);

        // Cargar XML
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xmlContent);

        // Generar IDs aleatorios para los nodos de firma
        $signatureId = 'Signature' . rand(100000, 999999);
        $signedInfoId = 'Signature-SignedInfo' . rand(100000, 999999);
        $signedPropertiesId = 'SignedProperties' . rand(100000, 999999);
        $keyInfoId = 'Certificate' . rand(100000, 999999);

        // Calcular DigestValue del Documento Completo SHA1
        $canonicalXml = $dom->C14N();
        $documentDigest = base64_encode(sha1($canonicalXml, true));

        // Construir SignatureProperties (XAdES-BES)
        $signedPropertiesXml = '<etsi:SignedProperties xmlns:etsi="http://uri.etsi.org/01903/v1.3.2#" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" Id="' . $signedPropertiesId . '">' .
            '<etsi:SignedSignatureProperties>' .
            '<etsi:SigningTime>' . date('c') . '</etsi:SigningTime>' .
            '<etsi:SigningCertificate>' .
            '<etsi:Cert>' .
            '<etsi:CertDigest>' .
            '<ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/>' .
            '<ds:DigestValue>' . base64_encode(sha1(base64_decode($certBase64), true)) . '</ds:DigestValue>' .
            '</etsi:CertDigest>' .
            '<etsi:IssuerSerial>' .
            '<ds:X509IssuerName>' . htmlspecialchars($issuerName) . '</ds:X509IssuerName>' .
            '<ds:X509SerialNumber>' . $serialNumber . '</ds:X509SerialNumber>' .
            '</etsi:IssuerSerial>' .
            '</etsi:Cert>' .
            '</etsi:SigningCertificate>' .
            '</etsi:SignedSignatureProperties>' .
            '</etsi:SignedProperties>';

        $signedPropertiesDigest = base64_encode(sha1($signedPropertiesXml, true));

        // Construir SignedInfo
        $signedInfoXml = '<ds:SignedInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:etsi="http://uri.etsi.org/01903/v1.3.2#" Id="' . $signedInfoId . '">' .
            '<ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/>' .
            '<ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>' .
            '<ds:Reference Id="SignedDocumentReference" URI="#comprobante">' .
            '<ds:Transforms>' .
            '<ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/>' .
            '</ds:Transforms>' .
            '<ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/>' .
            '<ds:DigestValue>' . $documentDigest . '</ds:DigestValue>' .
            '</ds:Reference>' .
            '<ds:Reference URI="#' . $signedPropertiesId . '" Type="http://uri.etsi.org/01903#SignedProperties">' .
            '<ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/>' .
            '<ds:DigestValue>' . $signedPropertiesDigest . '</ds:DigestValue>' .
            '</ds:Reference>' .
            '</ds:SignedInfo>';

        // Calcular la Firma RSA-SHA1 sobre SignedInfo
        $signatureValue = '';
        openssl_sign($signedInfoXml, $signatureValue, $privateKey, OPENSSL_ALGO_SHA1);
        $signatureValueBase64 = base64_encode($signatureValue);

        // Estructura final del nodo ds:Signature
        $signatureNodeXml = '<ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:etsi="http://uri.etsi.org/01903/v1.3.2#" Id="' . $signatureId . '">' .
            $signedInfoXml .
            '<ds:SignatureValue>' . $signatureValueBase64 . '</ds:SignatureValue>' .
            '<ds:KeyInfo Id="' . $keyInfoId . '">' .
            '<ds:X509Data>' .
            '<ds:X509Certificate>' . $certBase64 . '</ds:X509Certificate>' .
            '</ds:X509Data>' .
            '</ds:KeyInfo>' .
            '<ds:Object>' .
            '<etsi:QualifyingProperties xmlns:etsi="http://uri.etsi.org/01903/v1.3.2#" Target="#' . $signatureId . '">' .
            $signedPropertiesXml .
            '</etsi:QualifyingProperties>' .
            '</ds:Object>' .
            '</ds:Signature>';

        // Insertar primero la firma para canonicalizar exactamente el XML final.
        $sigDom = new DOMDocument('1.0', 'UTF-8');
        $sigDom->loadXML($signatureNodeXml);

        $importedNode = $dom->importNode($sigDom->documentElement, true);
        $dom->documentElement->appendChild($importedNode);

        // El SRI valida los bytes canonicalizados del nodo final, no el texto XML original.
        $signedPropertiesNode = $dom->getElementsByTagName('SignedProperties')->item(0);
        $signedInfoNode = $dom->getElementsByTagName('SignedInfo')->item(0);
        $signatureValueNode = $dom->getElementsByTagName('SignatureValue')->item(0);

        if (!$signedPropertiesNode || !$signedInfoNode || !$signatureValueNode) {
            throw new Exception('No se pudo construir la estructura de firma XAdES.');
        }

        $canonicalSignedProperties = $signedPropertiesNode->C14N();
        $signedPropertiesDigestNode = null;
        foreach ($signedInfoNode->getElementsByTagName('Reference') as $reference) {
            if ($reference->getAttribute('Type') === 'http://uri.etsi.org/01903#SignedProperties') {
                $signedPropertiesDigestNode = $reference->getElementsByTagName('DigestValue')->item(0);
                break;
            }
        }

        if (!$signedPropertiesDigestNode) {
            throw new Exception('No se encontró la referencia a SignedProperties.');
        }

        $signedPropertiesDigestNode->nodeValue = base64_encode(sha1($canonicalSignedProperties, true));
        $canonicalSignedInfo = $signedInfoNode->C14N();
        if (!openssl_sign($canonicalSignedInfo, $signatureValue, $privateKey, OPENSSL_ALGO_SHA1)) {
            throw new Exception('No se pudo generar la firma digital.');
        }
        $signatureValueNode->nodeValue = base64_encode($signatureValue);

        return $dom->saveXML();
    }

    private static function hexToDecimal(string $hex): string
    {
        $hex = strtolower(ltrim($hex, '0x'));
        $decimal = '0';

        foreach (str_split($hex) as $digit) {
            $decimal = self::decimalMultiply($decimal, 16);
            $decimal = self::decimalAdd($decimal, hexdec($digit));
        }

        return ltrim($decimal, '0') ?: '0';
    }

    private static function decimalMultiply(string $number, int $factor): string
    {
        $carry = 0;
        $result = '';

        for ($index = strlen($number) - 1; $index >= 0; $index--) {
            $value = ((int) $number[$index] * $factor) + $carry;
            $result = ($value % 10) . $result;
            $carry = intdiv($value, 10);
        }

        while ($carry > 0) {
            $result = ($carry % 10) . $result;
            $carry = intdiv($carry, 10);
        }

        return $result;
    }

    private static function decimalAdd(string $number, int $value): string
    {
        $index = strlen($number) - 1;
        $carry = $value;

        while ($index >= 0 && $carry > 0) {
            $sum = (int) $number[$index] + $carry;
            $number[$index] = (string) ($sum % 10);
            $carry = intdiv($sum, 10);
            $index--;
        }

        while ($carry > 0) {
            $number = ($carry % 10) . $number;
            $carry = intdiv($carry, 10);
        }

        return $number;
    }
}
