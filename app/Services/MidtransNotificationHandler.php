<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\StatusBayar;
use App\Enums\TipeBayar;
use App\Models\Pembayaran;
use Midtrans\Notification;

class MidtransNotificationHandler
{
    public static function handle(): ?string
    {
        $statusMessage = '';

        midtrans_config();
        $notif = new Notification();

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;

        //        $status = match ($transaction) {
        //            PaymentStatus::SETTLEMENT->value, PaymentStatus::CAPTURE->value => StatusBayar::SUDAH_BAYAR,
        //            PaymentStatus::FAILURE->value => StatusBayar::GAGAL,
        //            PaymentStatus::PENDING->value => StatusBayar::PENDING,
        //            default => StatusBayar::BELUM_BAYAR,
        //        };
        //
        //        $paymentTipe = match ($type) {
        //            'qris', 'gopay', 'shopeepay' => TipeBayar::QRIS,
        //            default => TipeBayar::TRANSFER,
        //        };
        //
        //        $pembayaran = Pembayaran::query()->where('order_id', $order_id)->first();

        if ('capture' === $transaction) {
            if ('credit_card' === $type) {
                if ('accept' === $fraud) {
                    // TODO set payment status in merchant's database to 'Success'
                    //                    $pembayaran->status_transaksi = PaymentStatus::CAPTURE;
                    //                    $pembayaran->status_pembayaran = StatusBayar::SUDAH_BAYAR;
                    $statusMessage = 'Transaksi order_id: ' . $order_id . ' berhasil ditangkap menggunakan ' . $type;
                }
            }
        } else {
            if ('settlement' === $transaction) {
                // TODO set payment status in merchant's database to 'Settlement'
                //                $pembayaran->status_transaksi = PaymentStatus::SETTLEMENT;
                $statusMessage = 'Transaksi order_id: ' . $order_id . ' berhasil ditransfer menggunakan ' . $type;
            } else {
                if ('pending' === $transaction) {
                    // TODO set payment status in merchant's database to 'Pending'
                    //                    $pembayaran->status_transaksi = PaymentStatus::PENDING;
                    $statusMessage = 'Menunggu nasabah menyelesaikan transaksi order_id: ' . $order_id . ' menggunakan '
                        . $type;
                } else {
                    if ('deny' === $transaction) {
                        // TODO set payment status in merchant's database to 'Denied'
                        //                        $pembayaran->status_transaksi = PaymentStatus::DENY;
                        $statusMessage = 'Pembayaran menggunakan ' . $type . ' untuk transaksi order_id: ' . $order_id . ' ditolak.';
                    } else {
                        if ('expire' === $transaction) {
                            // TODO set payment status in merchant's database to 'expire'
                            //                            $pembayaran->status_transaksi = PaymentStatus::EXPIRE;
                            $statusMessage = 'Pembayaran menggunakan ' . $type . ' untuk transaksi order_id: ' . $order_id . ' kedaluwarsa.';
                        } else {
                            if ('cancel' === $transaction) {
                                // TODO set payment status in merchant's database to 'Denied'
                                //                                $pembayaran->status_transaksi = PaymentStatus::CANCEL;
                                $statusMessage = 'Pembayaran menggunakan ' . $type . ' untuk transaksi order_id: ' . $order_id . ' dibatalkan.';
                            }
                        }
                    }
                }
            }
        }

        //        $pembayaran->status_pembayaran = $status;
        //        $pembayaran->tipe_pembayaran = $paymentTipe;
        //        $pembayaran->detail_transaksi = $notif;
        //        $pembayaran->save();

        //        return \Filament\Notifications\Notification::make('pembayaran')
        //            ->title('Notifikasi Pembayaran')
        //            ->body($statusMessage)
        //            ->success();

        return $statusMessage;
    }
}
