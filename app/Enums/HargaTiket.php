<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum HargaTiket: int implements HasLabel, HasDescription, HasColor
{
    case EARLYBIRD_8K = 200000;
    case EARLYBIRD_15K = 275000;
    case NORMAL_8K = 250000;
    case NORMAL_15K = 350000;

    public function getDescription(): ?string
    {
        return match ($this) {
            self::EARLYBIRD_8K => 'Pendaftaran Early Bird 8K - Only 100 Slot',
            self::EARLYBIRD_15K => 'Pendaftaran Early Bird 15K - Only 100 Slot',
            self::NORMAL_15K => 'Pendaftaran Normal 8K',
            self::NORMAL_8K => 'Pendaftaran Normal 15K',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::EARLYBIRD_8K => 'success',
            self::NORMAL_8K => 'indigo',
            self::EARLYBIRD_15K => 'primary',
            self::NORMAL_15K => 'green',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EARLYBIRD_8K => '200000',
            self::EARLYBIRD_15K => '275000',
            self::NORMAL_8K => '250000',
            self::NORMAL_15K => '350000',
        };
    }
}
