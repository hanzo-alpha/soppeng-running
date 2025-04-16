<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum StatusBayar: string implements HasLabel, HasDescription, HasColor
{
    case SUDAH_BAYAR = 'sudah_bayar';
    case BELUM_BAYAR = 'belum_bayar';
    case PENDING = 'pending';
    case GAGAL = 'gagal';

    public function getDescription(): ?string
    {
        return match ($this) {
            self::SUDAH_BAYAR => 'Pembayaran sudah berhasil dilakukan',
            self::BELUM_BAYAR => 'Pembayaran belum dilakukan',
            self::GAGAL => 'Pembayaran gagal dilakukan',
            self::PENDING => 'Pembayaran sedang menunggu dilakukan oleh customer',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::SUDAH_BAYAR => 'success',
            self::PENDING => 'indigo',
            self::BELUM_BAYAR => 'red',
            self::GAGAL => 'danger',
        };
    }

    public
    function getLabel(): ?string
    {
        return match ($this) {
            self::SUDAH_BAYAR => 'Sudah Bayar',
            self::BELUM_BAYAR => 'Belum Bayar',
            self::PENDING => 'Pembayaran Di Tunda',
            self::GAGAL => 'Gagal Bayar',
        };
    }
}
