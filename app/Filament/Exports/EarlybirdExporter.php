<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\Earlybird;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EarlybirdExporter extends Exporter
{
    protected static ?string $model = Earlybird::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('uuid_earlybird'),
            ExportColumn::make('nama_lengkap'),
            ExportColumn::make('email'),
            ExportColumn::make('no_telp'),
            ExportColumn::make('jenis_kelamin'),
            ExportColumn::make('tempat_lahir'),
            ExportColumn::make('tanggal_lahir'),
            ExportColumn::make('alamat'),
            ExportColumn::make('negara'),
            ExportColumn::make('provinsi'),
            ExportColumn::make('kabupaten'),
            ExportColumn::make('kecamatan'),
            ExportColumn::make('tipe_kartu_identitas'),
            ExportColumn::make('nomor_kartu_identitas'),
            ExportColumn::make('nama_kontak_darurat'),
            ExportColumn::make('nomor_kontak_darurat'),
            ExportColumn::make('golongan_darah'),
            ExportColumn::make('ukuran_jersey'),
            ExportColumn::make('kategori_lomba'),
            ExportColumn::make('komunitas'),
            ExportColumn::make('status_earlybird'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your earlybird export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
