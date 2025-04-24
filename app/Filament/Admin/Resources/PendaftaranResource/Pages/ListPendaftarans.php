<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PendaftaranResource\Pages;

use App\Enums\StatusPendaftaran;
use App\Filament\Admin\Resources\PendaftaranResource;
use App\Filament\Admin\Widgets\PendaftaranOverview;
use App\Filament\Exports\PendaftaranExporter;
use App\Models\Pembayaran;
use Filament\Actions;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPendaftarans extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PendaftaranResource::class;

    //    public function getTabs(): array
    //    {
    //        return [
    //            'normal' => Tab::make()
    //                ->label('Normal')
    //                ->icon('heroicon-m-minus-circle')
    //                ->badge(
    //                    Pembayaran::query()
    //                        ->where('status_pendaftaran', StatusPendaftaran::NORMAL)
    //                        ->count(),
    //                )
    //                ->modifyQueryUsing(
    //                    fn(Builder $query) => $query
    //                        ->where('status_pendaftaran', StatusPendaftaran::NORMAL),
    //                ),
    //            'early_bird' => Tab::make()
    //                ->label('Early Bird')
    //                ->icon('heroicon-m-check-circle')
    //                ->badge(
    //                    Pembayaran::query()
    //                        ->where('status_pendaftaran', StatusPendaftaran::EARLYBIRD)
    //                        ->count(),
    //                )
    //                ->modifyQueryUsing(
    //                    fn(Builder $query) => $query
    //                        ->where('status_pendaftaran', StatusPendaftaran::EARLYBIRD),
    //                ),
    //        ];
    //    }

    protected function getHeaderWidgets(): array
    {
        return [
            PendaftaranOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()
                ->label('Ekspor')
                ->exporter(PendaftaranExporter::class)
                ->formats([
                    ExportFormat::Xlsx,
                ])
                ->color('secondary')
                ->icon('heroicon-s-arrow-down-tray')
                ->maxRows(10000)
                ->chunkSize(250),
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
