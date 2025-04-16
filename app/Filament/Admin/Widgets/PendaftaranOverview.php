<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\StatusRegistrasi;
use App\Models\Pendaftaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Number;

class PendaftaranOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $countEarly = Pendaftaran::count();
        $totalBayarLunas = Pendaftaran::query()
            ->where('status_registrasi', StatusRegistrasi::BERHASIL)
            ->count();
        $totalFailed = Pendaftaran::query()
            ->where('status_registrasi', StatusRegistrasi::BATAL)
            ->count();
        $totalPending = Pendaftaran::query()
            ->whereIn('status_registrasi', [
                StatusRegistrasi::TUNDA,
                StatusRegistrasi::BELUM_BAYAR,
            ])
            ->count();
        return [
            Stat::make(
                'Jumlah Pendaftar Registrasi',
                Number::format($countEarly, 0, null, 'id') . ' Peserta',
            )
                ->description('Total Semua Pendaftar')
                ->color('primary'),
            Stat::make(
                'Jumlah Peserta Lunas',
                Number::format($totalBayarLunas, 0, null, 'id') . ' Peserta',
            )
                ->description('Pembayaran Lunas')
                ->color('info'),
            Stat::make(
                'Jumlah Peserta Pending',
                Number::format($totalPending, 0, null, 'id') . ' Peserta',
            )
                ->description('Pembayaran Pending')
                ->color('warning'),
            Stat::make(
                'Jumlah Peserta Kedaluwarsa',
                Number::format($totalFailed, 0, null, 'id') . ' Peserta',
            )
                ->description('Pembayaran Kedaluwarsa')
                ->color('danger'),
        ];
    }
}
