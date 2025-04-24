<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PembayaranResource\Pages;

use App\Enums\StatusPendaftaran;
use App\Filament\Admin\Resources\PembayaranResource;
use App\Filament\Admin\Widgets\PembayaranOverview;
use App\Filament\Exports\PembayaranExporter;
use App\Models\Pembayaran;
use Filament\Actions;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPembayarans extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PembayaranResource::class;

//    public function getTabs(): array
//    {
//        return [
//            'terdaftar' => Tab::make()
//                ->label('Terdaftar')
//                ->icon('heroicon-m-minus-circle')
//                ->badge(
//                    Pembayaran::query()
//                        ->where('status_pendaftaran', StatusPendaftaran::SUDAH)
//                        ->count(),
//                )
//                ->modifyQueryUsing(
//                    fn(Builder $query) => $query
//                        ->where('status_pendaftaran', StatusPendaftaran::SUDAH),
//                ),
//            'belum_terdaftar' => Tab::make()
//                ->label('Belum Terdaftar')
//                ->icon('heroicon-m-check-circle')
//                ->badge(
//                    Pembayaran::query()
//                        ->where('status_pendaftaran', StatusPendaftaran::BELUM)
//                        ->count(),
//                )
//                ->modifyQueryUsing(
//                    fn(Builder $query) => $query
//                        ->where('status_pendaftaran', StatusPendaftaran::BELUM),
//                ),
//        ];
//    }

    protected function getHeaderWidgets(): array
    {
        return [
            PembayaranOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()
                ->label('Ekspor Data Pembayaran')
                ->exporter(PembayaranExporter::class)
                ->formats([
                    ExportFormat::Xlsx,
                ])
                ->color('secondary')
                ->icon('heroicon-s-arrow-down-tray')
                ->maxRows(10000)
                ->chunkSize(250),
            Actions\CreateAction::make()
                ->icon('heroicon-s-plus'),
        ];
    }
}
