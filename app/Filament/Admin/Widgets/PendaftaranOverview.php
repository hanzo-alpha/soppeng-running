<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\StatusRegistrasi;
use App\Filament\Admin\Resources\PendaftaranResource\Pages\ListPendaftarans;
use App\Models\Pendaftaran;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Number;

class PendaftaranOverview extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListPendaftarans::class;
    }

    protected function getStats(): array
    {
        $countTiketKonser = $this->getPageTableQuery()->whereHas(
            'kategori',
            fn($query) => $query->where('kategori', 'tiket_konser'),
        )->count();
        $countNightRun = $this->getPageTableQuery()->whereHas(
            'kategori',
            fn($query) => $query->where('kategori', 'night_run'),
        )->count();
        $countTiketRun = $this->getPageTableQuery()->whereHas(
            'kategori',
            fn($query) => $query->where('kategori', 'tiket_run'),
        )->count();
        $countAll = $this->getPageTableQuery()->count();

        $totalPending = Pendaftaran::query()
            ->whereIn('status_registrasi', [
                StatusRegistrasi::TUNDA,
                StatusRegistrasi::BELUM_BAYAR,
            ])
            ->count();
        return [
            Stat::make(
                'Jumlah Semua Peserta',
                Number::format($countAll, 0, null, 'id') . ' Peserta',
            )
                ->description('Jumlah Peserta Semua Kategori')
                ->color('primary'),
            Stat::make(
                'Jumlah Peserta Tiket Konser',
                Number::format($countTiketKonser, 0, null, 'id') . ' Peserta',
            )
                ->description('Peserta Kategori Tiket Konser')
                ->color('secondary'),
            Stat::make(
                'Jumlah Peserta Night Run',
                Number::format($countNightRun, 0, null, 'id') . ' Peserta',
            )
                ->description('Peserta Kategori Night Run')
                ->color('danger'),
            Stat::make(
                'Peserta Tiket Run & Konser',
                Number::format($countTiketRun, 0, null, 'id') . ' Peserta',
            )
                ->description('Kategori Tiket Run & Konser')
                ->color('warning'),
        ];
    }
}
