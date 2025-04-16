<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CustomerStatus: string implements HasLabel, HasIcon, HasDescription, HasColor
{
    case SUDAH_BAYAR = 'sudah_bayar';
    case BELUM_BAYAR = 'belum_bayar';

    public function getDescription(): ?string
    {
        return match ($this) {
            self::SUDAH_BAYAR => 'Sudah melakukan pembayaran',
            self::BELUM_BAYAR => 'Belum melakukan pembayaran',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::SUDAH_BAYAR => 'heroicon-o-check',
            self::BELUM_BAYAR => 'heroicon-o-x-mark',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::SUDAH_BAYAR => 'success',
            self::BELUM_BAYAR => 'danger',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SUDAH_BAYAR => 'Sudah Bayar',
            self::BELUM_BAYAR => 'Belum Bayar',
        };
    }
}
