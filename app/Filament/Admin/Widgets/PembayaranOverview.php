<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\StatusBayar;
use App\Filament\Admin\Resources\PembayaranResource\Pages\ListPembayarans;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Number;

class PembayaranOverview extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListPembayarans::class;
    }

    protected function getStats(): array
    {
        $totalBayarLunas = $this->getPageTableQuery()
            ->where('status_pembayaran', StatusBayar::SUDAH_BAYAR)
            ->sum('total_harga');

        $totalEarlyBird = $this->getPageTableQuery()
            ->whereHas(
                'pendaftaran',
                fn($query) => $query->whereHas('kategori', fn($query) => $query->where('kategori', 'early_bird')),
            )
            ->where('status_pembayaran', StatusBayar::SUDAH_BAYAR)
            ->sum('total_harga');

        $totalNormal = $this->getPageTableQuery()
            ->whereHas(
                'pendaftaran',
                fn($query) => $query->whereHas('kategori', fn($query) => $query->where('kategori', 'normal')),
            )
            ->where('status_pembayaran', StatusBayar::SUDAH_BAYAR)
            ->sum('total_harga');

        $totalBayarLunasEarlybird = $this->getPageTableQuery()->where('status_pembayaran', StatusBayar::PENDING)
            ->sum('total_harga');

        return [
            Stat::make('Total Pembayaran Lunas', Number::format($totalBayarLunas, 0, null, 'id'))
                ->description('Jumlah Pembayaran Lunas')
                ->color('primary'),
            Stat::make('Total Pembayaran Pending', Number::format($totalBayarLunasEarlybird, 0, null, 'id'))
                ->description('Jumlah Pembayaran Pending')
                ->color('yellow'),
            Stat::make('Total Pembayaran Earlybird', Number::format($totalEarlyBird, 0, null, 'id'))
                ->description('Kategori Earlybird')
                ->color('success'),
            Stat::make('Total Pembayaran Normal', Number::format($totalNormal, 0, null, 'id'))
                ->description('Kategori Normal')
                ->color('danger'),
        ];
    }
}
