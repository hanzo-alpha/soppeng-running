<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Pembayaran;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use LaraZeus\Qr\Facades\Qr;

class InfoPeserta extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;
    public ?Model $record;
    public ?array $data = [];

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'E-Tiket';
    protected static string $view = 'filament.app.pages.info-peserta';
    //    protected static ?string $slug = 'info-peserta';
    protected static bool $shouldRegisterNavigation = false;
    protected ?string $heading = 'Info E-Tiket Peserta';

    /**
     * @param  string|null  $slug
     */
    public static function setSlug(?string $slug): string
    {
        return InfoPeserta::getUrl(['id' => request()->get('id')]);
    }

    public function mount(Pembayaran $pembayaran): void
    {
        $id = request()->has('id') ? request()->get('id') : null;
        $this->record = $pembayaran->findOrFail($id);
    }

    public function getFormStatePath(): ?string
    {
        return 'data';
    }

    public function getModel(): string
    {
        return Pembayaran::class;
    }

    public function pembayaranInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->getRecord())
            ->schema([
                Group::make()->schema([
                    Section::make('Data Peserta')->schema([
                        TextEntry::make('pendaftaran.peserta.uuid_peserta')
                            ->label('Peserta ID')
                            ->color('secondary'),
                        TextEntry::make('pendaftaran.peserta.nama_lengkap')
                            ->label('Nama Lengkap')
                            ->color('secondary'),
                        TextEntry::make('pendaftaran.peserta.email')
                            ->label('Email')
                            ->color('secondary'),
                        TextEntry::make('pendaftaran.peserta.no_telp')
                            ->label('No Telp')
                            ->color('secondary'),
                        TextEntry::make('pendaftaran.peserta.jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->badge(),
                        TextEntry::make('pendaftaran.peserta.tempat_lahir')
                            ->label('Tempat Lahir')
                            ->color('secondary'),
                        TextEntry::make('pendaftaran.peserta.tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->date('d M Y')
                            ->color('secondary'),
                        TextEntry::make('pendaftaran.peserta.tipe_kartu_identitas')
                            ->badge()
                            ->label('Tipe Kartu Identitas')
                            ->color('secondary'),
                        TextEntry::make('pendaftaran.peserta.nomor_kartu_identitas')
                            ->label('Nomor Kartu Identitas')
                            ->color('secondary'),
                        TextEntry::make('pendaftaran.peserta.nama_kontak_darurat')
                            ->label('Nama Kontak Darurat')
                            ->color('secondary'),
                        TextEntry::make('pendaftaran.peserta.nomor_kontak_darurat')
                            ->label('Nomor Kontak Darurat')
                            ->color('secondary'),
                        TextEntry::make('pendaftaran.peserta.golongan_darah')
                            ->badge()
                            ->label('Golongan Darah'),
                        TextEntry::make('pendaftaran.peserta.komunitas')
                            ->label('Komunitas'),
                    ])->columns(2),

                    Section::make('Data Alamat')->schema([
                        TextEntry::make('pendaftaran.alamat')
                            ->label('Alamat')
                            ->columnSpanFull()
                            ->color('secondary'),
                        TextEntry::make('pendaftaran.negara')
                            ->label('Negara')
                            ->color('secondary'),
                        TextEntry::make('pendaftaran.prov.name')
                            ->label('Provinsi')
                            ->color('secondary'),
                        TextEntry::make('pendaftaran.kab.name')
                            ->label('Kabupaten')
                            ->color('secondary'),
                        TextEntry::make('pendaftaran.kec.name')
                            ->label('Kecamatan')
                            ->color('secondary'),
                    ])->columns(2),
                ])->columnSpan(2),
                Group::make()->schema([
                    Section::make('Status Peserta')->schema([
                        TextEntry::make('pendaftaran.ukuran_jersey')
                            ->label('Ukuran Jersey')
                            ->badge(),
                        TextEntry::make('pendaftaran.kategori.nama')
                            ->label('Kategori')
                            ->badge(),
                        TextEntry::make('pendaftaran.peserta.status_peserta')
                            ->label('Status Peserta')
                            ->badge(),
                        TextEntry::make('pendaftaran.status_pendaftaran')
                            ->label('Status Pendaftaran')
                            ->badge(),
                        IconEntry::make('pendaftaran.status_pengambilan')
                            ->label('Status Pengambilan')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-badge')
                            ->falseIcon('heroicon-o-x-mark'),
                    ])->columns(2),
                    Section::make('Pembayaran')->schema([
                        TextEntry::make('order_id')
                            ->label('Order ID')->badge(),
                        TextEntry::make('tipe_pembayaran')
                            ->label('Tipe Bayar')->badge(),
                        TextEntry::make('status_pembayaran')
                            ->label('Status Pembayaran')->badge(),
                        TextEntry::make('status_transaksi')
                            ->label('Status Transaksi')->badge(),
                    ])->columns(2),

                    Section::make('E-Tiket')->schema([
                        TextEntry::make('pendaftaran.qr_url')
                            ->hiddenLabel()
                            ->formatStateUsing(fn(string $state, $record) => Qr::render(
                                data: $state,
                                options: $record->options,
                            )),
                    ]),
                ])->columns(1),
            ])->columns(3);
    }

    public function getRecord(): ?Model
    {
        return $this->record;
    }
}
