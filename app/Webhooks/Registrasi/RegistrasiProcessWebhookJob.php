<?php

declare(strict_types=1);

namespace App\Webhooks\Registrasi;

use App\Models\HistoriPembayaran;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;

class RegistrasiProcessWebhookJob extends SpatieProcessWebhookJob
{
    public function handle(): void
    {
        //        $data = collect($this->webhookCall->payload)->toArray();
        //        HistoriPembayaran::create($data);
        http_response_code(200);
    }
}
