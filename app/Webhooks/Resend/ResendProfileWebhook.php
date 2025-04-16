<?php

declare(strict_types=1);

namespace App\Webhooks\Resend;

use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

class ResendProfileWebhook implements WebhookProfile
{
    public function shouldProcess(Request $request): bool
    {
        return true;
    }
}
