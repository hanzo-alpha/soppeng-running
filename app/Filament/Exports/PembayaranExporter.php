<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\Pembayaran;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PembayaranExporter extends Exporter
{
    protected static ?string $model = Pembayaran::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('uuid_pembayaran')
                ->label('UUID Pembayaran'),
            ExportColumn::make('registrasi.nama_lengkap')
                ->label('Nama Lengkap'),
            ExportColumn::make('nama_kegiatan')
                ->label('Nama Kegiatan'),
            ExportColumn::make('ukuran_jersey')
                ->label('Ukuran Jersey'),
            ExportColumn::make('kategori.nama')
                ->label('Kategori Lomba'),
            ExportColumn::make('jumlah'),
            ExportColumn::make('satuan'),
            ExportColumn::make('harga_satuan')
                ->label('Harga Satuan'),
            ExportColumn::make('total_harga')
                ->label('Total Harga'),
            ExportColumn::make('tipe_pembayaran')
                ->label('Tipe Pembayaran'),
            ExportColumn::make('status_pembayaran')
                ->label('Status Pembayaran'),
            ExportColumn::make('status_transaksi')
                ->label('Status Transaksi'),
            ExportColumn::make('status_daftar')
                ->label('Status Daftar'),
            ExportColumn::make('status_pendaftaran')
                ->label('Status Pendaftaran'),
            ExportColumn::make('keterangan')
                ->label('Keterangan'),
            ExportColumn::make('created_at')
                ->label('Created At'),
            ExportColumn::make('updated_at')
                ->label('Updated At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Data pembayaran anda telah di eksport dan ' . number_format($export->successful_rows) . ' ' . str('baris')->plural($export->successful_rows) . ' berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' gagal di ekspor.';
        }

        return $body;
    }
}
