<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TipeKartuIdentitas: string implements HasLabel, HasColor
{
    case KTP = 'KTP';
    case PASSPORT = 'PASSPORT';
    case SIM = 'SIM';

    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->name] = $case->value;
        }
        return $array;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::KTP => 'success',
            self::PASSPORT => 'warning',
            self::SIM => 'info',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::KTP => 'KTP',
            self::PASSPORT => 'PASSPORT',
            self::SIM => 'SIM',
        };
    }
}
