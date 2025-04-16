<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TipeBayar: string implements HasLabel, HasIcon, HasDescription, HasColor
{
    case TRANSFER = 'transfer';
    case QRIS = 'qris';

    public function getDescription(): ?string
    {
        return match ($this) {
            self::TRANSFER => 'Transfer Antar Bank atau Beda Bank',
            self::QRIS => 'Pembayaran menggunakan QRIS',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::TRANSFER => 'heroicon-o-credit-card',
            self::QRIS => 'heroicon-o-qr-code',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::TRANSFER => 'primary',
            self::QRIS => 'secondary',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TRANSFER => 'Transfer',
            self::QRIS => 'QRIS',
        };
    }
}
