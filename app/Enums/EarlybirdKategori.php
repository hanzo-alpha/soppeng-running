<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum EarlybirdKategori: string implements HasLabel, HasIcon, HasColor
{
    case EARLY_DELAPAN_K = 'early_8K';
    case EARLY_LIMABELAS_K = 'early_15K';

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
            self::EARLY_DELAPAN_K => 'success',
            self::EARLY_LIMABELAS_K => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::EARLY_DELAPAN_K, self::EARLY_LIMABELAS_K => 'heroicon-o-map-pin',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EARLY_DELAPAN_K => 'EARLY 8K - IDR 225K',
            self::EARLY_LIMABELAS_K => 'EARLY 15K - IDR 275K',
        };
    }
}
