<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum JenisBayar: string implements HasLabel, HasIcon, HasDescription, HasColor
{
    case OTOMATIS = 'otomatis';
    case MANUAL = 'manual';

    public function getDescription(): ?string
    {
        return match ($this) {
            self::OTOMATIS => 'Transfer VA atau QRIS',
            self::MANUAL => 'Pembayaran menggunakan MANUAL',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::OTOMATIS => 'heroicon-o-arrow-path',
            self::MANUAL => 'heroicon-o-bolt',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::OTOMATIS => 'primary',
            self::MANUAL => 'secondary',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::OTOMATIS => 'Transfer',
            self::MANUAL => 'Manual',
        };
    }
}
