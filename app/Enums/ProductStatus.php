<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ProductStatus: string implements HasDescription, HasLabel, HasIcon
{
    case IN_STOCK = 'in_stock';
    case OUT_STOCK = 'out_stock';

    public function getDescription(): ?string
    {
        return match ($this) {
            self::IN_STOCK => __('Persediaan Stok Masih Ada'),
            self::OUT_STOCK => __('Persediaan Stok Habis'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::IN_STOCK => 'heroicon-o-arrow-up',
            self::OUT_STOCK => 'heroicon-o-arrow-down',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::IN_STOCK => __('In Stock'),
            self::OUT_STOCK => __('Out Stock'),
        };
    }
}
