<?php

declare(strict_types=1);

namespace App\Webhooks\Resend;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;

class ResendProcessWebhookJob extends SpatieProcessWebhookJob
{
    public function handle(): void
    {
        http_response_code(200);
    }
}
