<?php

declare(strict_types=1);

namespace App\Services;

use AllowDynamicProperties;
use Exception;
use Midtrans\Config;
use Midtrans\Snap;

#[AllowDynamicProperties] class MidtransService
{
    public string $serverKey;
    public bool $isProduction;
    public bool $isSanitized = false;
    public bool $is3ds = true;
    public array $transaction_details = [];
    public array $customer_details = [];

    public function __construct()
    {
        $this->setConfig();
    }

    /**
     * @throws Exception
     */
    public function getTransactionToken(array $transaction, array $customer): string
    {
        $params = [
            $this->getTransactionDetails($transaction),
            $this->getCustomerDetails($customer),
        ];

        return Snap::getSnapToken($params);
    }

    public function getTransactionDetails(array $transaction_details): array
    {
        return $this->setTransactionDetails($transaction_details);
    }

    public function setTransactionDetails(array $transaction_details): array
    {
        $this->transaction_details = [
            'transaction_details' => $this->transformToArray($transaction_details),
        ];

        return $this->transaction_details;
    }

    public function getCustomerDetails(array $customer_details): array
    {
        return $this->setCustomerDetails($customer_details);
    }

    public function setCustomerDetails(array $customer_details): array
    {
        $this->customer_details = [
            'customer_details' => $this->transformToArray($customer_details),
        ];

        return $this->customer_details;
    }

    public function getSnapToken(array $params): string
    {
        $params = [
            'transaction_details' => [
                'order_id' => rand(),
                'gross_amount' => 10000,
            ],
            'customer_details' => [
                'first_name' => 'budi',
                'last_name' => 'pratama',
                'email' => 'budi.pra@example.com',
                'phone' => '08111222333',
            ],
        ];

        return Snap::getSnapToken($params);
    }

    public function transformToArray(array $data): array
    {
        $collect = collect();
        foreach ($data as $key => $value) {
            $collect->put($key, $value);
        }
        return $collect->toArray();
    }

    private function setConfig(): void
    {
        Config::$serverKey = $this->serverKey;
        Config::$isProduction = $this->isProduction;
        Config::$isSanitized = $this->isSanitized;
        Config::$is3ds = $this->is3ds;
    }
}
