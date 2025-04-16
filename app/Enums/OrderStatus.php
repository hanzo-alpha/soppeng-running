<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasDescription, HasLabel, HasIcon, HasColor
{
    case ORDER_CREATED = 'order_created';
    case ORDER_PAID = 'order_paid';
    case ORDER_PENDING = 'order_pending';
    case ORDER_SEND = 'order_send';
    case ORDER_PROCESSING = 'order_processing';
    case ORDER_PROCESSED = 'order_processed';
    case ORDER_CANCEL = 'order_cancel';
    case ORDER_FAILED = 'order_failed';
    case ORDER_COMPLETED = 'order_completed';
    case ORDER_SHIPPING = 'order_shipping';

    public function getDescription(): ?string
    {
        return match ($this) {
            self::ORDER_CREATED => __('Pesanan sudah dibuat'),
            self::ORDER_PAID => __('Pesanan sudah dibayar'),
            self::ORDER_PENDING => __('Pesanan sedang ditunda'),
            self::ORDER_SEND => __('Pesanan telah dikirim'),
            self::ORDER_PROCESSING => __('Pesanan sedang diproses'),
            self::ORDER_PROCESSED => __('Pesanan sudah diproses'),
            self::ORDER_CANCEL => __('Pesanan sudah dibatalkan oleh pembeli'),
            self::ORDER_FAILED => __('Pesanan gagal diproses'),
            self::ORDER_COMPLETED => __('Pesanan selesai'),
            self::ORDER_SHIPPING => __('Pesanan sedang dikemas'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ORDER_CREATED => 'heroicon-o-plus',
            self::ORDER_PAID => 'heroicon-o-calculator',
            self::ORDER_PENDING => 'heroicon-o-clock',
            self::ORDER_SEND => 'heroicon-o-truck',
            self::ORDER_PROCESSING => 'heroicon-o-rocket-launch',
            self::ORDER_PROCESSED => 'heroicon-o-credit-card',
            self::ORDER_CANCEL => 'heroicon-o-document-minus',
            self::ORDER_FAILED => 'heroicon-o-x-mark',
            self::ORDER_COMPLETED => 'heroicon-o-arrow-down',
            self::ORDER_SHIPPING => 'heroicon-o-lifebuoy',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ORDER_CREATED => __('Pesanan Dibuat'),
            self::ORDER_PAID => __('Pesanan Sudah Dibayar'),
            self::ORDER_PENDING => __('Pesanan ditunda'),
            self::ORDER_SEND => __('Pesanan Dikirim'),
            self::ORDER_PROCESSING => __('Pesanan sedang diproses'),
            self::ORDER_PROCESSED => __('Pesanan sudah diproses'),
            self::ORDER_CANCEL => __('Pesanan gagal'),
            self::ORDER_FAILED => __('Pesanan gagal dibatalkan'),
            self::ORDER_COMPLETED => __('Pesanan selesai'),
            self::ORDER_SHIPPING => __('Pesanan dikemas'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::ORDER_CREATED => 'indigo',
            self::ORDER_PAID => 'success',
            self::ORDER_PENDING => 'blue',
            self::ORDER_SEND => 'secondary',
            self::ORDER_PROCESSING => 'violet',
            self::ORDER_PROCESSED => 'purple',
            self::ORDER_CANCEL => 'red',
            self::ORDER_FAILED => 'danger',
            self::ORDER_COMPLETED => 'info',
            self::ORDER_SHIPPING => 'warning',
        };
    }
}
