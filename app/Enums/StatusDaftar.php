<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StatusDaftar: int implements HasLabel, HasIcon, HasDescription, HasColor
{
    case TERDAFTAR = 1;
    case BELUM_TERDAFTAR = 0;

    public function getDescription(): ?string
    {
        return match ($this) {
            self::TERDAFTAR => 'Sudah Terdaftar di Sistem',
            self::BELUM_TERDAFTAR => 'Belum Terdaftar di Sistem',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::TERDAFTAR => 'heroicon-o-check-circle',
            self::BELUM_TERDAFTAR => 'heroicon-o-minus-circle',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::TERDAFTAR => 'success',
            self::BELUM_TERDAFTAR => 'danger',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TERDAFTAR => 'Terdaftar',
            self::BELUM_TERDAFTAR => 'Belum Terdaftar',
        };
    }
}
