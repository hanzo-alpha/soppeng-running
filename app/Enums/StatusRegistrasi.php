<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusRegistrasi: string implements HasLabel, HasColor
{
    case TUNDA = 'DITUNDA';
    case BERHASIL = 'BERHASIL';
    case BATAL = 'DIBATALKAN';
    case PROSES = 'DIPROSES';
    case BELUM_BAYAR = 'BELUM BAYAR';
    case PENGEMBALIAN = 'PENGEMBALIAN';

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
            self::BERHASIL => 'success',
            self::TUNDA => 'warning',
            self::BATAL, self::BELUM_BAYAR => 'danger',
            self::PROSES => 'info',
            self::PENGEMBALIAN => 'primary',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BERHASIL => 'Berhasil',
            self::TUNDA => 'Di Tunda',
            self::BATAL => 'Di Batalkan',
            self::PROSES => 'Sedang Diproses',
            self::BELUM_BAYAR => 'Belum Bayar',
            self::PENGEMBALIAN => 'Pengembalian',
        };
    }
}
