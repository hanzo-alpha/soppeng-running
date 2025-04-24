<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use ZerosDev\TriPay\Client as TripayClient;
use ZerosDev\TriPay\Support\Constant;
use ZerosDev\TriPay\Transaction;

class TripayClientService
{
    public static function getTransaction(): Transaction
    {
        return new Transaction(self::getClient());
    }

    public static function getVersion(): string
    {
        return config('tripay.version', '2025-04-17');
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public static function getPaymentChannel(): string
    {
        $response = Http::acceptJson()
            ->withToken(self::getApiKey())
            ->get(self::getPaymentChannelUrl());

        return $response->json();
    }

    public static function getPaymentInstruction(): string
    {
        return 'production' === config('tripay.mode') ? config('tripay.production.instruksi_pembayaran') : config('tripay.sandbox.instruksi_pembayaran');
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public static function getFeeCalculator($paymentCode, $amount): string
    {
        $response = Http::acceptJson()
            ->retry([100, 200])
            ->withToken(self::getApiKey())
            ->withQueryParameters([
                'amount' => $amount,
                'payment_code' => $paymentCode,
            ])
            ->get(self::getFeeCalculatorUrl());

        return $response->json();
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public static function getDaftarTransaksi(
        $page,
        $per_page,
        $sort,
        $reference,
        $merchant_ref,
        $method,
        $status,
    ): string {
        $response = Http::acceptJson()
            ->retry([100, 200])
            ->withToken(self::getApiKey())
            ->withQueryParameters([
                'page' => $page,
                'per_page' => $per_page,
                'sort' => $sort,
                'referency' => $reference,
                'merchant_ref' => $merchant_ref,
                'method' => $method,
                'status' => $status,
            ])
            ->get(self::getDaftarTransaksiUrl());

        return $response->json();
    }

    public function getClient(): TripayClient
    {
        return new TripayClient(
            $this->getMerchantCode(),
            $this->getApiKey(),
            $this->getPrivateKey(),
            $this->getMode(),
            $this->getGuzzleOptions(),
        );
    }

    public static function getApiKey(): string
    {
        return ('production' === self::getMode()) ? config('tripay.production.api_key') : config('tripay.sandbox.api_key');
    }

    public static function getPrivateKey(): string
    {
        return ('production' === self::getMode()) ? config('tripay.production.private_key') : config('tripay.sandbox.private_key');
    }

    private static function getMode(): string
    {
        return 'production' === config('tripay.mode') ? Constant::MODE_PRODUCTION : Constant::MODE_DEVELOPMENT;
    }

    private static function getMerchantCode(): string
    {
        return config('tripay.merchant_code');
    }

    private static function getPaymentChannelUrl(): string
    {
        return 'production' === config('tripay.mode')
            ? 'https://tripay.co.id/api/merchant/payment-channel'
            : 'https://tripay.co.id/api-sandbox/merchant/payment-channel';
    }

    private static function getFeeCalculatorUrl(): string
    {
        return 'production' === config('tripay.mode')
            ? 'https://tripay.co.id/api/merchant/fee-calculator'
            : 'https://tripay.co.id/api-sandbox/merchant/fee-calculator';
    }

    private static function getDaftarTransaksiUrl(): string
    {
        return 'production' === config('tripay.mode')
            ? 'https://tripay.co.id/api/merchant/transactions'
            : 'https://tripay.co.id/api-sandbox/merchant/transactions';
    }

    private static function getEndPoint(): string
    {
        return ('production' === self::getMode()) ? Constant::URL_PRODUCTION : Constant::URL_DEVELOPMENT;
    }

    private static function getGuzzleOptions(): array
    {
        return [
            'debug' => true,
        ];
    }
}
