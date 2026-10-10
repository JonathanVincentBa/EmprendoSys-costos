<?php

namespace App\Services;

use App\Mail\AuthorizedInvoiceMail;
use App\Models\Sale;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class AuthorizedInvoiceEmailService
{
    public function send(Sale $sale, string $signedXml): void
    {
        $sale->loadMissing(['company', 'customer', 'items.product']);
        $email = $sale->customer?->email;

        if (!$email) {
            throw new RuntimeException('El cliente no tiene un correo electrónico registrado.');
        }

        $company = $sale->company;
        if (!$company?->mail_host || !$company->mail_username || !$company->mail_password) {
            throw new RuntimeException('La empresa no tiene configurado su servidor SMTP completo.');
        }

        config([
            'mail.mailers.company_smtp' => [
                'transport' => 'smtp',
                'host' => $company->mail_host,
                'port' => $company->mail_port ?: 587,
                'encryption' => $company->mail_encryption ?: 'tls',
                'username' => $company->mail_username,
                'password' => $company->mail_password,
                'timeout' => 30,
            ],
            'mail.from.address' => $company->email ?: $company->mail_username,
            'mail.from.name' => $company->mail_from_name ?: $company->name,
        ]);

        $pdf = app(InvoicePdfService::class)->render($sale);

        Mail::purge('company_smtp');
        Mail::mailer('company_smtp')
            ->to($email)
            ->send(new AuthorizedInvoiceMail($sale, $signedXml, $pdf));
    }
}
