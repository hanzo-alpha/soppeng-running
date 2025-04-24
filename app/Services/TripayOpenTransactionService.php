<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TripayOpenTransactionService
{
    public static function getSignature($merchantCode, $merchantRef, $amount, $privateKey): string
    {
        return hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey);
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public static function postRequestTransaction(
        $method,
        $merchant_ref,
        $amount,
        $customer_name,
        $customer_email,
        $customer_phone,
        $order_items,
        $callback_url,
        $return_url,
        $expired_time,
        $signature,
    ) {
        $response = Http::acceptJson()
            ->retry([100, 200])
            ->withToken(TripayClientService::getApiKey())
            ->post(self::getRequestTransactionUrl(), [
                'method' => $method,
                'merchant_ref' => $merchant_ref,
                'amount' => $amount,
                'customer_name' => $customer_name,
                'customer_email' => $customer_email,
                'customer_phone' => $customer_phone,
                'order_items' => $order_items,
                'callback_url' => $callback_url,
                'return_url' => $return_url,
                'expired_time' => $expired_time,
                'signature' => $signature,
            ]);

        return $response->json();
    }

    public static function getDetailTransaction($merchant_ref)
    {
        $response = Http::acceptJson()
            ->retry([100, 200])
            ->withToken(TripayClientService::getApiKey())
            ->get(self::getRequestTransactionUrl(), [
                'merchant_ref' => $merchant_ref,
            ]);

        return $response->json();
    }

    public static function getTransactionStatus($merchant_ref)
    {
        $response = Http::acceptJson()
            ->retry([100, 200])
            ->withToken(TripayClientService::getApiKey())
            ->get(self::getStatusTransactionUrl(), [
                'merchant_ref' => $merchant_ref,
            ]);

        return $response->json();
    }

    private static function getRequestTransactionUrl(): string
    {
        return 'production' === config('tripay.mode')
            ? 'https://tripay.co.id/api/transaction/create'
            : 'https://tripay.co.id/api-sandbox/transaction/create';
    }

    private static function getDetailTransactionUrl(): string
    {
        return 'production' === config('tripay.mode')
            ? 'https://tripay.co.id/api/transaction/detail'
            : 'https://tripay.co.id/api-sandbox/transaction/detail';
    }

    private static function getStatusTransactionUrl(): string
    {
        return 'production' === config('tripay.mode')
            ? 'https://tripay.co.id/api/transaction/detail'
            : 'https://tripay.co.id/api-sandbox/transaction/check-status';
    }
}
