<?php

declare(strict_types=1);

namespace App\Webhooks\Registrasi;

use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class RegistrasiSignatureValidator implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $midtransSignatureKey = $request->get('signature_key');
        $orderId = $request->get('order_id');
        $statusCode = $request->get('status_code');
        $grossAmount = $request->get('gross_amount');
        $serverKey = config('midtrans.sb.server_key');

        if (config('midtrans.is_production')) {
            $serverKey = config('midtrans.production.server_key');
        }

        $input = $orderId . $statusCode . $grossAmount . $serverKey;
        $computedSignature = hash('sha512', $input);

        return hash_equals($midtransSignatureKey, $computedSignature);
    }
}
