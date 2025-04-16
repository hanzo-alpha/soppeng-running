<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasLabel, HasDescription, HasColor
{
    case AUTHORIZE = 'authorize';
    case CAPTURE = 'capture';
    case SETTLEMENT = 'settlement';
    case DENY = 'deny';
    case PENDING = 'pending';
    case CANCEL = 'cancel';
    case REFUND = 'refund';
    case PARTIAL_REFUND = 'partial_refund';
    case CHARGEBACK = 'chargeback';
    case PARTIAL_CHARGEBACK = 'partial_chargeback';
    case EXPIRE = 'expire';
    case FAILURE = 'failure';

    public function getDescription(): ?string
    {
        return match ($this) {
            self::AUTHORIZE => 'Mengotorisasi kartu pembayaran yang digunakan untuk transaksi',
            self::CAPTURE => 'Transaksi berhasil dan saldo kartu berhasil ditangkap',
            self::SETTLEMENT => 'Transaksi berhasil diselesaikan. Dana telah dikreditkan ke akun Anda.',
            self::DENY => 'Kredensial yang digunakan untuk pembayaran ditolak oleh penyedia pembayaran atau MidtransService Fraud Detection System (FDS)',
            self::PENDING => 'Transaksi dibuat dan menunggu untuk dibayar oleh pelanggan di penyedia pembayaran seperti debit langsung, Transfer Bank, E-wallet, dan sebagainya.',
            self::CANCEL => 'Transaksi dibatalkan. Hal ini dapat dipicu oleh MidtransService atau bank mitra. Catatan: Untuk pembayaran kartu, status pembatalan dipicu oleh MidtransService jika terjadi transaksi Pra-Otorisasi ketika transaksi Authorized melebihi batas waktu pengambilan',
            self::REFUND => 'Transaksi ditandai untuk dikembalikan. Status pengembalian dana dipicu oleh Anda.',
            self::PARTIAL_REFUND => 'Transaksi ditandai untuk dikembalikan sebagian.',
            self::CHARGEBACK => 'Transaksi ditandai untuk ditagih kembali. (Hanya berlaku untuk kartu pembayaran).',
            self::PARTIAL_CHARGEBACK => 'Transaksi ditandai untuk ditagih kembali sebagian.',
            self::EXPIRE => 'Transaksi tidak tersedia untuk diproses, karena pembayaran tertunda.',
            self::FAILURE => 'Kesalahan tak terduga terjadi selama pemrosesan transaksi.',
        };
    }

    //    public function getIcon(): ?string
    //    {
    //        return match ($this) {
    //            self::AUTHORIZE => 'heroicon-o-shield-check',
    //            self::CAPTURE => 'heroicon-o-camera',
    //            self::SETTLEMENT => 'heroicon-o-x-mark',
    //        };
    //    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::AUTHORIZE => 'indigo',
            self::SETTLEMENT => 'success',
            self::CAPTURE => 'blue',
            self::DENY => 'secondary',
            self::PENDING => 'violet',
            self::REFUND => 'purple',
            self::CANCEL => 'fuchsia',
            self::EXPIRE => 'danger',
            self::PARTIAL_REFUND => 'info',
            self::PARTIAL_CHARGEBACK => 'warning',
            self::FAILURE => 'red',
            self::CHARGEBACK => 'yellow',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::AUTHORIZE => 'Authorize',
            self::CAPTURE => 'Capture',
            self::SETTLEMENT => 'Settlement',
            self::DENY => 'Deny',
            self::PENDING => 'Pending',
            self::CANCEL => 'Cancel',
            self::REFUND => 'Refund',
            self::PARTIAL_REFUND => 'Partial refund',
            self::CHARGEBACK => 'Chargeback',
            self::PARTIAL_CHARGEBACK => 'Partial chargeback',
            self::EXPIRE => 'Expire',
            self::FAILURE => 'Failure',
        };
    }
}
