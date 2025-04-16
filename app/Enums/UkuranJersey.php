<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UkuranJersey: string implements HasLabel
{
    case XXS_MEN = 'XXS_MEN';
    case XS_MEN = 'XS_MEN';
    case S_MEN = 'S_MEN';
    case M_MEN = 'M_MEN';
    case L_MEN = 'L_MEN';
    case XL_MEN = 'XL_MEN';
    case XXL_MEN = '2XL_MEN';
    case XXXL_MEN = '3XL_MEN';

    case XXS_WOMEN = 'XXS_WOMEN';
    case XS_WOMEN = 'XS_WOMEN';
    case S_WOMEN = 'S_WOMEN';
    case M_WOMEN = 'M_WOMEN';
    case L_WOMEN = 'L_WOMEN';
    case XL_WOMEN = 'XL_WOMEN';
    case XXL_WOMEN = '2XL_WOMEN';
    case XXXL_WOMEN = '3XL_WOMEN';

    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->name] = $case->value;
        }
        return $array;
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::XXS_MEN => __('XXS - MEN'),
            self::XS_MEN => __('XS - MEN'),
            self::S_MEN => __('S - MEN'),
            self::M_MEN => __('M - MEN'),
            self::L_MEN => __('L - MEN'),
            self::XL_MEN => __('XL - MEN'),
            self::XXL_MEN => __('2XL - MEN'),
            self::XXXL_MEN => __('3XL - MEN'),
            self::XXS_WOMEN => __('XXS - WOMEN'),
            self::XS_WOMEN => __('XS - WOMEN'),
            self::S_WOMEN => __('S - WOMEN'),
            self::M_WOMEN => __('M - WOMEN'),
            self::L_WOMEN => __('L - WOMEN'),
            self::XL_WOMEN => __('XL - WOMEN'),
            self::XXL_WOMEN => __('2XL - WOMEN'),
            self::XXXL_WOMEN => __('3XL - WOMEN'),
        };
    }

}
