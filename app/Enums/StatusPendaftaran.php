<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StatusPendaftaran: string implements HasLabel, HasIcon, HasDescription, HasColor
{
    case TIKET_KONSER = 'tiket_konser';
    case NIGHT_RUN = 'night_run';
    case TIKET_RUN = 'tiket_run';

    public function getDescription(): ?string
    {
        return match ($this) {
            self::TIKET_KONSER => 'Tiket Konser Saja',
            self::NIGHT_RUN => 'Night Run Saja',
            self::TIKET_RUN => 'Tiket Konser dan Night Run',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::TIKET_KONSER => 'heroicon-o-ticket',
            self::NIGHT_RUN => 'heroicon-o-sun',
            self::TIKET_RUN => 'heroicon-o-rocket-launch',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::TIKET_KONSER => 'secondary',
            self::NIGHT_RUN => 'primary',
            self::TIKET_RUN => 'info',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TIKET_KONSER => 'Tiket Konser Saja',
            self::NIGHT_RUN => 'Night Run Saja',
            self::TIKET_RUN => 'Tiket Konser dan Night Run',
        };
    }
}
