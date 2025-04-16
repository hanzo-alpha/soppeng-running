<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Enums\GolonganDarah;
use App\Enums\JenisKelamin;
use App\Enums\StatusPendaftaran;
use App\Enums\StatusRegistrasi;
use App\Enums\TipeKartuIdentitas;
use App\Enums\UkuranJersey;
use App\Models\Pendaftaran;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;

class PendaftaranExporter extends Exporter
{
    protected static ?string $model = Pendaftaran::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('uuid_pendaftaran')
                ->label('UUID Pendaftaran'),
            ExportColumn::make('nama_lengkap')
                ->label('Nama Lengkap'),
            ExportColumn::make('email')
                ->label('Email'),
            ExportColumn::make('no_telp')
                ->label('No Telp'),
            ExportColumn::make('jenis_kelamin')
                ->label('Jenis Kelamin')
                ->formatStateUsing(function ($state) {
                    return match ($state->value) {
                        JenisKelamin::LAKI->value => JenisKelamin::LAKI->value,
                        JenisKelamin::PEREMPUAN->value => JenisKelamin::PEREMPUAN->value,
                    };
                }),
            ExportColumn::make('tempat_lahir')
                ->label('Tempat Lahir'),
            ExportColumn::make('tanggal_lahir')
                ->label('Tanggal Lahir')
                ->state(fn($record) => $record->tanggal_lahir->format('d-m-Y')),
            ExportColumn::make('alamat')
                ->label('Alamat'),
            ExportColumn::make('negara')
                ->label('Negara'),
            ExportColumn::make('prov.name')
                ->label('Provinsi'),
            ExportColumn::make('kab.name')
                ->label('Kabupaten'),
            ExportColumn::make('kec.name')
                ->label('Kecamatan'),
            ExportColumn::make('tipe_kartu_identitas')
                ->label('Tipe Kartu Identitas')
                ->formatStateUsing(function ($state) {
                    return match ($state->value) {
                        TipeKartuIdentitas::KTP->value => TipeKartuIdentitas::KTP->value,
                        TipeKartuIdentitas::SIM->value => TipeKartuIdentitas::SIM->value,
                        TipeKartuIdentitas::PASSPORT->value => TipeKartuIdentitas::PASSPORT->value,
                    };
                }),
            ExportColumn::make('nomor_kartu_identitas')
                ->label('Nomor Kartu Identitas'),
            ExportColumn::make('nama_kontak_darurat')
                ->label('Nama Kontak Darurat'),
            ExportColumn::make('nomor_kontak_darurat')
                ->label('Nomor Kontak Darurat'),
            ExportColumn::make('golongan_darah')
                ->label('Golongan Darah')
                ->formatStateUsing(function ($state) {
                    return match ($state->value) {
                        GolonganDarah::A->value => GolonganDarah::A->value,
                        GolonganDarah::A_MINUS->value => GolonganDarah::A_MINUS->value,
                        GolonganDarah::A_PLUS->value => GolonganDarah::A_PLUS->value,
                        GolonganDarah::B->value => GolonganDarah::B->value,
                        GolonganDarah::B_MINUS->value => GolonganDarah::B_MINUS->value,
                        GolonganDarah::B_PLUS->value => GolonganDarah::B_PLUS->value,
                        GolonganDarah::AB->value => GolonganDarah::AB->value,
                        GolonganDarah::AB_MINUS->value => GolonganDarah::AB_MINUS->value,
                        GolonganDarah::AB_PLUS->value => GolonganDarah::AB_PLUS->value,
                        GolonganDarah::O->value => GolonganDarah::O->value,
                        GolonganDarah::O_MINUS->value => GolonganDarah::O_MINUS->value,
                        GolonganDarah::O_PLUS->value => GolonganDarah::O_PLUS->value,
                    };
                }),
            ExportColumn::make('ukuran_jersey')
                ->label('Ukuran Jersey')
                ->formatStateUsing(function ($state) {
                    return match ($state->value) {
                        UkuranJersey::M_MEN->value => UkuranJersey::M_MEN->value,
                        UkuranJersey::M_WOMEN->value => UkuranJersey::M_WOMEN->value,
                        UkuranJersey::XS_MEN->value => UkuranJersey::XS_MEN->value,
                        UkuranJersey::XS_WOMEN->value => UkuranJersey::XS_WOMEN->value,
                        UkuranJersey::XXS_MEN->value => UkuranJersey::XXS_MEN->value,
                        UkuranJersey::XXS_WOMEN->value => UkuranJersey::XXS_WOMEN->value,
                        UkuranJersey::XL_MEN->value => UkuranJersey::XL_MEN->value,
                        UkuranJersey::XL_WOMEN->value => UkuranJersey::XL_WOMEN->value,
                        UkuranJersey::XXL_MEN->value => UkuranJersey::XXL_MEN->value,
                        UkuranJersey::XXL_WOMEN->value => UkuranJersey::XXL_WOMEN->value,
                        UkuranJersey::XXXL_MEN->value => UkuranJersey::XXXL_MEN->value,
                        UkuranJersey::XXXL_WOMEN->value => UkuranJersey::XXXL_WOMEN->value,
                        UkuranJersey::L_MEN->value => UkuranJersey::L_MEN->value,
                        UkuranJersey::L_WOMEN->value => UkuranJersey::L_WOMEN->value,
                        UkuranJersey::S_MEN->value => UkuranJersey::S_MEN->value,
                        UkuranJersey::S_WOMEN->value => UkuranJersey::S_WOMEN->value,
                    };
                }),
            ExportColumn::make('kategori.nama')
                ->label('Kategori Lomba'),
            ExportColumn::make('komunitas')
                ->label('Komunitas'),
            ExportColumn::make('status_registrasi')
                ->label('Status Registrasi')
                ->formatStateUsing(function ($state) {
                    return match ($state->value) {
                        StatusRegistrasi::BATAL->value => StatusRegistrasi::BATAL->value,
                        StatusRegistrasi::TUNDA->value => StatusRegistrasi::TUNDA->value,
                        StatusRegistrasi::BERHASIL->value => StatusRegistrasi::BERHASIL->value,
                        StatusRegistrasi::BELUM_BAYAR->value => StatusRegistrasi::BELUM_BAYAR->value,
                        StatusRegistrasi::PENGEMBALIAN->value => StatusRegistrasi::PENGEMBALIAN->value,
                        StatusRegistrasi::PROSES->value => StatusRegistrasi::PROSES->value,
                    };
                }),
            ExportColumn::make('status_pendaftaran')
                ->label('Status Pendaftaran')
                ->formatStateUsing(function ($state) {
                    return match ($state->value) {
                        StatusPendaftaran::NORMAL->value => StatusPendaftaran::NORMAL->value,
                        StatusPendaftaran::EARLYBIRD->value => StatusPendaftaran::EARLYBIRD->value,
                    };
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your pendaftaran export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    public function getXlsxHeaderCellStyle(): ?Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontItalic()
            ->setFontSize(12)
            ->setFontName('Calibri')
            ->setFontColor(Color::DARK_BLUE)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
    }
}
