<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StatusPendaftaran: string implements HasLabel, HasIcon, HasDescription, HasColor
{
    case SUDAH = 'sudah';
    case BELUM = 'belum';

    public function getDescription(): ?string
    {
        return match ($this) {
            self::SUDAH => 'Sudah Mengambil Atribut',
            self::BELUM => 'Belum Mengambil Atribut',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::SUDAH => 'heroicon-o-check-circle',
            self::BELUM => 'heroicon-o-minus-circle',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::SUDAH => 'success',
            self::BELUM => 'danger',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SUDAH => 'Sudah',
            self::BELUM => 'Belum',
        };
    }
}
