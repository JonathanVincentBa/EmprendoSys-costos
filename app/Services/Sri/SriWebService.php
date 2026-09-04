<?php

namespace App\Services\Sri;

use SoapClient;
use Exception;

class SriWebService
{
    private const WSDL_TEST_RECEPCION = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';
    private const WSDL_TEST_AUTORIZACION = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';

    private const WSDL_PROD_RECEPCION = 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';
    private const WSDL_PROD_AUTORIZACION = 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';

    public function sendXml(string $xmlSignedContent, string $environment = '1')
    {
        $url = ($environment === '2')
            ? 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl'
            : 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';

        try {
            if (!class_exists(SoapClient::class)) {
                throw new Exception('La extensión SOAP no está habilitada en el PHP que ejecuta la aplicación.');
            }

            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'EmprendoSys SRI Client',
                ],
            ]);

            $client = $this->createClient($url, $context);
            
            // SoapClient codifica automáticamente el parámetro base64Binary.
            $soapResponse = $client->validarComprobante([
                'xml' => $xmlSignedContent
            ]);
            $response = $soapResponse->RespuestaRecepcionComprobante ?? $soapResponse;

            return [
                'status'   => $response->estado ?? 'DEVUELTA',
                'response' => $response
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'ERROR',
                'message' => $e->getMessage()
            ];
        }
    }

    private function createClient(string $wsdl, $context): SoapClient
    {
        $cachedWsdl = storage_path('app/sri-wsdl-' . sha1($wsdl) . '.xml');
        $contents = false;
        $error = '';

        for ($attempt = 1; $attempt <= 3 && $contents === false; $attempt++) {
            $curl = curl_init($wsdl);
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT => 'EmprendoSys SRI Client',
            ]);

            $contents = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            if ($contents === '') {
                $contents = false;
            }
        }

        if ($contents !== false) {
            @file_put_contents($cachedWsdl, $contents);
        } elseif (is_file($cachedWsdl)) {
            $contents = file_get_contents($cachedWsdl);
        }

        if ($contents === false || $contents === '') {
            throw new Exception('No se pudo descargar el WSDL del SRI: ' . ($error ?: 'respuesta vacía'));
        }

        $localWsdl = tempnam(sys_get_temp_dir(), 'sri-wsdl-');
        if ($localWsdl === false || file_put_contents($localWsdl, $contents) === false) {
            throw new Exception('No se pudo crear el archivo temporal del WSDL del SRI.');
        }

        try {
            return new SoapClient($localWsdl, [
                'trace'      => 1,
                'exceptions' => true,
                'encoding'   => 'UTF-8',
                'connection_timeout' => 30,
                'stream_context' => $context
            ]);
        } finally {
            @unlink($localWsdl);
        }
    }

    public function authorizeInvoice(string $accessKey, string $environment = '1'): array
    {
        $wsdl = ($environment === '2') ? self::WSDL_PROD_AUTORIZACION : self::WSDL_TEST_AUTORIZACION;

        try {
            if (!class_exists(SoapClient::class)) {
                throw new Exception('La extensión SOAP no está habilitada en el PHP que ejecuta la aplicación.');
            }

            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'EmprendoSys SRI Client',
                ],
            ]);

            $client = $this->createClient($wsdl, $context);
            
            $response = $client->autorizacionComprobante([
                'claveAccesoComprobante' => $accessKey
            ]);

            return [
                'status' => 'SUCCESS',
                'response' => $response->RespuestaAutorizacionComprobante
            ];
        } catch (Exception $e) {
            return [
                'status' => 'ERROR',
                'message' => 'Error de conexión SRI (Autorización): ' . $e->getMessage()
            ];
        }
    }
}
