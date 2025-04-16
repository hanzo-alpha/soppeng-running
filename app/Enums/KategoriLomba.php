<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum KategoriLomba: string implements HasLabel, HasIcon, HasColor
{
    case DELAPAN_K = '8K';
    case LIMABELAS_K = '15K';

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
            self::DELAPAN_K => 'primary',
            self::LIMABELAS_K => 'secondary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::DELAPAN_K, self::LIMABELAS_K, => 'heroicon-o-map-pin',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DELAPAN_K => '8K - IDR 250K',
            self::LIMABELAS_K => '15K - IDR 350K',
        };
    }
}
