<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UserStatus: string implements HasDescription, HasLabel, HasIcon
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';


    public function getDescription(): ?string
    {
        return match ($this) {
            self::ACTIVE => __('Status Pengguna Aktif'),
            self::INACTIVE => __('Status Pengguna Tidak Aktif'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'heroicon-o-user-plus',
            self::INACTIVE => 'heroicon-o-user-minus',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => __('Aktif'),
            self::INACTIVE => __('Non Aktif'),
        };
    }
}
