<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\StatusBayar;
use App\Enums\StatusDaftar;
use App\Enums\StatusRegistrasi;
use App\Enums\TipeBayar;
use App\Models\Pembayaran;
use Exception;

use function hash;

use Illuminate\Support\Facades\Http;
use Midtrans\Snap;

class MidtransAPI
{
    public string $serverKey;
    public bool $isProduction;
    public bool $isSanitized = false;
    public bool $is3ds = true;
    public array $transaction_details = [];
    public array $customer_details = [];

    /**
     * Accept : application/json
     * Content-Type: application/json
     * response: token, redirect_url
     * */
    public static function getSnapTransactionsEndpoint(bool $isProduction = false): string
    {
        return $isProduction
            ? 'https://api.sandbox.midtrans.com/snap/v1/transactions'
            : 'https://api.midtrans.com/snap/v1/transactions/';
    }

    public static function setBillingAddress(array $billing): array
    {
        return [
            'billing_address' => static::transformToArray($billing),
        ];
    }

    public static function transformToArray(array $data): array
    {
        $collect = collect();
        foreach ($data as $key => $value) {
            $collect->put($key, $value);
        }
        return $collect->toArray();
    }

    public static function getBillingAddress(array $billing): array
    {
        return static::setCustomerDetails($billing);
    }

    public static function setCustomerDetails(array $customer_details): array
    {
        return [
            'customer_details' => static::transformToArray($customer_details),
        ];
    }

    public static function verifySignatureKey(
        string $signatureKey,
        string $orderId,
        string $statusCode,
        int $amount,
        string $serverKey,
    ): bool {
        $signature = $orderId . $statusCode . $amount . $serverKey;
        $hash = hash('sha512', $signature);
        return hash_equals($signatureKey, $hash);
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public static function getTransactionStatus($orderId): bool|string|int|null|array
    {
        $data = [];
        $data['responses'] = [];
        $data['sukses'] = false;

        if ($orderId) {
            $url = config('midtrans.is_production')
                ? 'https://api.midtrans.com/v2/' . $orderId . '/status'
                : 'https://api.sandbox.midtrans.com/v2/' . $orderId . '/status';

            $response = config('midtrans.is_production')
                ? Http::acceptJson()->withBasicAuth(config('midtrans.production.server_key'), '')->get($url)
                : Http::acceptJson()->withBasicAuth(config('midtrans.sb.server_key'), '')->get($url);

            $data['responses'] = $response->json();
            $data['sukses'] = true;
        }

        return $data;
    }

    public static function getStatusMessage($orderId): array
    {
        $url = self::buildMidtransUrl($orderId);
        $responseData = self::fetchMidtransResponse($url);

        if (empty($responseData)) {
            return [
                'status' => 'danger',
                'status_message' => 'Transaksi Dengan Order ID : ' . $orderId . ' tidak ditemukan',
            ];
        }

        $transactionStatus = $responseData['transaction_status'] ?? null;
        $paymentType = self::getPaymentType($responseData['payment_type'] ?? null);
        $order_id = $responseData['order_id'] ?? null;

        $details = collect($responseData)->except(['id'])->toArray();
        $pembayaran = Pembayaran::query()->where('order_id', $order_id)->first();
        $registrasi = $pembayaran?->pendaftaran;
        $peserta = $registrasi?->peserta;

        $responseCode = ['201', '200', '407', '202'];

        // Add transaction details
        if (in_array($details['status_code'], $responseCode)) {
            $pembayaran->detail_transaksi = $details;
            $pembayaran->tipe_pembayaran = $paymentType;
            $peserta->status_peserta = StatusDaftar::TERDAFTAR;
            $statusData = self::handleTransactionStatus(
                $transactionStatus,
                $responseData['payment_type'] ?? null,
                $pembayaran,
                $registrasi,
            );

            if (null === $statusData['status_message']) {
                $pembayaran->delete();
                $registrasi->delete();
                $peserta->delete();
            } else {
                $pembayaran->save();
                $registrasi->save();
                $peserta->save();
            }

            return [
                'status' => $statusData['status'],
                'status_message' => $statusData['status_message'],
            ];
        }

        return [
            'status' => $details['status_code'],
            'status_message' => $details['status_message'],
        ];
    }


    /**
     * @throws Exception
     */
    public static function getSnapTokenApi(?array $transaction, ?array $items, ?array $customer): string
    {
        midtrans_config();

        $params = array_merge(
            static::getTransactionDetails($transaction),
            static::getItemDetails($items),
            static::getCustomerDetails($customer),
        );

        return Snap::getSnapToken($params);
    }

    public static function getTransactionDetails(array $transaction_details): array
    {
        return static::setTransactionDetails($transaction_details);
    }

    public static function setTransactionDetails(array $transaction_details): array
    {
        return [
            'transaction_details' => static::transformToArray($transaction_details),
        ];
    }

    public static function getItemDetails(array $items): array
    {
        return static::setItemDetails($items);
    }

    public static function setItemDetails(array $items): array
    {
        return [
            'item_details' => [static::transformToArray($items)],
        ];
    }

    public static function getCustomerDetails(array $customer_details): array
    {
        return static::setCustomerDetails($customer_details);
    }


    private static function buildMidtransUrl($orderId): string
    {
        $baseUrl = config('midtrans.is_production')
            ? 'https://api.midtrans.com/v2/'
            : 'https://api.sandbox.midtrans.com/v2/';
        return $baseUrl . $orderId . '/status';
    }

    private static function fetchMidtransResponse($url): array
    {
        $isProduction = config('midtrans.is_production');
        $serverKey = $isProduction
            ? config('midtrans.production.server_key')
            : config('midtrans.sb.server_key');

        $response = Http::acceptJson()
            ->withBasicAuth($serverKey, '')
            ->get($url);

        return $response->collect()->toArray();
    }

    private static function handleTransactionStatus($transactionStatus, $type, $pembayaran, $registrasi): array
    {
        return match ($transactionStatus) {
            'capture' => self::handleSuccessStatus(
                PaymentStatus::CAPTURE,
                'Berhasil Direkam',
                $type,
                $pembayaran,
                $registrasi,
            ),
            'settlement' => self::handleSuccessStatus(
                PaymentStatus::SETTLEMENT,
                'Berhasil Melakukan Transaksi',
                $type,
                $pembayaran,
                $registrasi,
            ),
            'pending' => self::handlePendingOrFailedStatus(
                PaymentStatus::PENDING,
                StatusBayar::PENDING,
                StatusRegistrasi::TUNDA,
                'info',
                'Menunggu nasabah menyelesaikan transaksi',
                $type,
                $pembayaran,
                $registrasi,
            ),
            'deny' => self::handlePendingOrFailedStatus(
                PaymentStatus::DENY,
                StatusBayar::GAGAL,
                StatusRegistrasi::BATAL,
                'danger',
                'Transaksi Ditolak',
                $type,
                $pembayaran,
                $registrasi,
            ),
            'expire' => self::handlePendingOrFailedStatus(
                PaymentStatus::EXPIRE,
                StatusBayar::GAGAL,
                StatusRegistrasi::BATAL,
                'warning',
                'Transaksi Kedaluwarsa',
                $type,
                $pembayaran,
                $registrasi,
            ),
            'cancel' => self::handlePendingOrFailedStatus(
                PaymentStatus::CANCEL,
                StatusBayar::GAGAL,
                StatusRegistrasi::BATAL,
                'warning',
                'Transaksi Dibatalkan',
                $type,
                $pembayaran,
                $registrasi,
            ),
            default => ['status' => 'danger', 'status_message' => null],
        };
    }

    private static function handleSuccessStatus(
        $paymentStatus,
        $messageSuffix,
        $type,
        $pembayaran,
        $registrasi,
    ): array {
        $pembayaran->status_transaksi = $paymentStatus;
        $pembayaran->status_pembayaran = StatusBayar::SUDAH_BAYAR;
        $registrasi->status_registrasi = StatusRegistrasi::BERHASIL;

        return [
            'status' => 'success',
            'status_message' => 'Transaksi order_id: ' . $pembayaran->order_id . ' ' . $messageSuffix . ' menggunakan ' . $type,
        ];
    }

    private static function handlePendingOrFailedStatus(
        $paymentStatus,
        $paymentState,
        $registrationState,
        $status,
        $messageSuffix,
        $type,
        $pembayaran,
        $registrasi,
    ): array {
        $pembayaran->status_transaksi = $paymentStatus;
        $pembayaran->status_pembayaran = $paymentState;
        $registrasi->status_registrasi = $registrationState;

        return [
            'status' => $status,
            'status_message' => 'Pembayaran menggunakan ' . $type . ' untuk transaksi order_id: ' . $pembayaran->order_id . ' ' . $messageSuffix . '.',
        ];
    }

    private static function getPaymentType($paymentType): TipeBayar
    {
        return match ($paymentType) {
            'qris', 'gopay', 'shopeepay' => TipeBayar::QRIS,
            default => TipeBayar::TRANSFER,
        };
    }
}
