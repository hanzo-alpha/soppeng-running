<?php

declare(strict_types=1);

namespace App\Webhooks\Registrasi;

use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

class RegistrasiProfileWebhook implements WebhookProfile
{
    public function shouldProcess(Request $request): bool
    {
        return true;
    }
}
