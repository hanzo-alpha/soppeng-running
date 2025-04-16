<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum GolonganDarah: string implements HasLabel, HasDescription
{
    case A = 'A';
    case A_PLUS = 'A+';
    case A_MINUS = 'A-';
    case B = 'B';
    case B_PLUS = 'B+';
    case B_MINUS = 'B-';
    case AB = 'AB';
    case AB_PLUS = 'AB+';
    case AB_MINUS = 'AB-';
    case O = 'O';
    case O_PLUS = 'O+';
    case O_MINUS = 'O-';

    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->name] = $case->value;
        }
        return $array;
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::A => 'Golongan Darah A',
            self::A_PLUS => 'Golongan Darah A+',
            self::A_MINUS => 'Golongan Darah A-',
            self::B => 'Golongan Darah B',
            self::B_PLUS => 'Golongan Darah B+',
            self::B_MINUS => 'Golongan Darah B-',
            self::AB => 'Golongan Darah AB',
            self::AB_PLUS => 'Golongan Darah AB+',
            self::AB_MINUS => 'Golongan Darah AB-',
            self::O => 'Golongan Darah O',
            self::O_PLUS => 'Golongan Darah O+',
            self::O_MINUS => 'Golongan Darah O-',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::A => 'A',
            self::A_PLUS => 'A+',
            self::A_MINUS => 'A-',
            self::B => 'B',
            self::B_PLUS => 'B+',
            self::B_MINUS => 'B-',
            self::AB => 'AB',
            self::AB_PLUS => 'AB+',
            self::AB_MINUS => 'AB-',
            self::O => 'O',
            self::O_PLUS => 'O+',
            self::O_MINUS => 'O-',
        };
    }
}
