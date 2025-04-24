<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\GolonganDarah;
use App\Enums\JenisKelamin;
use App\Enums\StatusDaftar;
use App\Enums\StatusPendaftaran;
use App\Enums\StatusRegistrasi;
use App\Enums\TipeKartuIdentitas;
use App\Enums\UkuranJersey;
use App\Filament\Admin\Resources\PendaftaranResource\Pages;
use App\Filament\Admin\Widgets\PendaftaranOverview;
use App\Models\Pendaftaran;
use App\Services\MidtransAPI;
use App\Services\TripayClientService;
use App\Services\TripayClosedTransactionService;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use LaraZeus\Qr\Components\Qr;
use Number;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;

class PendaftaranResource extends Resource
{
    protected static ?string $model = Pendaftaran::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?string $label = 'Pendaftaran';
    protected static ?string $slug = 'pendaftaran';
    protected static ?string $modelLabel = 'Pendaftaran';
    protected static ?string $pluralLabel = 'Pendaftaran';
    protected static ?string $pluralModelLabel = 'Pendaftaran';
    protected static ?string $navigationLabel = 'Pendaftaran';
    protected static ?string $navigationGroup = 'Pendaftaran';
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'nama_lengkap';

    public static function formPendaftaran(): array
    {
        return [
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make('Data Peserta')->schema([
                        Forms\Components\Select::make('peserta_id')
                            ->required()
                            ->relationship('peserta', 'nama_lengkap')
                            ->live(onBlur: true)
                            ->native(false)
                            ->loadingMessage('Sedang dimuat...')
                            ->placeholder('Pilih salah satu peserta')
                            ->searchPrompt('Cari peserta berdasarkan NIK, Nama Lengkap, Peserta ID, dll')
                            ->preload()
                            ->optionsLimit(20)
                            ->createOptionForm(PesertaResource::formPeserta())
                            ->createOptionAction(
                                fn(Action $action) => $action->modalWidth('5xl')
                                    ->modalSubmitActionLabel('Simpan Peserta')
                                    ->closeModalByEscaping(false)
                                    ->closeModalByClickingAway(),
                            )
                            ->searchingMessage('Sedang mencari peserta...')
                            ->noSearchResultsMessage('Peserta tidak ditemukan.')
                            ->searchable(),
                        Forms\Components\TextInput::make('no_bib')
                            ->label('Nomor BIB Lari')
                            ->default(generateNomorBib()),
                        Forms\Components\TextInput::make('nama_bib')
                            ->label('Nama BIB Lari'),
                        Forms\Components\Select::make('kategori_lomba')
                            ->label('Kategori Lomba')
                            ->relationship('kategori', 'nama')
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('ukuran_jersey')
                            ->required()
                            ->native(false)
                            ->options(UkuranJersey::class)
                            ->enum(UkuranJersey::class)
                            ->searchable(),
                    ])->columns(2),

                    Forms\Components\Section::make('Data Alamat')->schema([
                        Forms\Components\TextInput::make('alamat')
                            ->required()
                            ->columnSpanFull(),
                        Country::make('negara')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('provinsi')
                            ->required()
                            ->options(Province::pluck('name', 'code'))
                            ->dehydrated()
                            ->live(onBlur: true)
                            ->native(false)
                            ->searchable()
                            ->afterStateUpdated(fn(Forms\Set $set) => $set('kabupaten', null)),
                        Forms\Components\Select::make('kabupaten')
                            ->required()
                            ->live(onBlur: true)
                            ->options(fn(Forms\Get $get) => City::where(
                                'province_code',
                                $get('provinsi'),
                            )->pluck(
                                'name',
                                'code',
                            ))
                            ->native(false)
                            ->searchable()
                            ->afterStateUpdated(fn(Forms\Set $set) => $set('kecamatan', null)),
                        Forms\Components\Select::make('kecamatan')
                            ->required()
                            ->live(onBlur: true)
                            ->options(fn(Forms\Get $get) => District::where(
                                'city_code',
                                $get('kabupaten'),
                            )->pluck(
                                'name',
                                'code',
                            ))
                            ->native(false)
                            ->searchable(),
                    ])->columns(2),
                ])
                ->columnSpan(2),
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make('Status Pendaftaran')->schema([
                        Forms\Components\Select::make('status_registrasi')
                            ->native(false)
                            ->options(StatusRegistrasi::class)
                            ->enum(StatusRegistrasi::class)
                            ->required(),
                        Qr::make('qr_url')
                            ->label('QR Code')
                            ->optionsColumn('qr_options')
                            ->actionIcon('heroicon-s-building-library'),
                        Forms\Components\ToggleButtons::make('status_pengambilan')
                            ->label('Status Pengambilan')
                            ->inline()
                            ->options([
                                true => 'Sudah',
                                false => 'Belum',
                            ])
                            ->colors([
                                true => 'success',
                                false => 'danger',
                            ])
                            ->icons([
                                true => 'heroicon-m-check',
                                false => 'heroicon-m-x-mark',
                            ])
                            ->grouped()
                            ->default(false),
                    ])->columns(1),
                ])
                ->columns(1),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::formPendaftaran())->columns(3);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Group::make()->schema([
                Section::make('Data Peserta')->schema([
                    TextEntry::make('uuid_pendaftaran')
                        ->label('UUID')
                        ->color('secondary'),
                    TextEntry::make('peserta.nama_lengkap')
                        ->label('Nama Lengkap')
                        ->color('secondary'),
                    TextEntry::make('peserta.email')
                        ->label('Email')
                        ->color('secondary'),
                    TextEntry::make('peserta.no_telp')
                        ->label('No Telp')
                        ->color('secondary'),
                    TextEntry::make('peserta.jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->badge(),
                    TextEntry::make('peserta.tempat_lahir')
                        ->label('Tempat Lahir')
                        ->color('secondary'),
                    TextEntry::make('peserta.tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->date('d M Y')
                        ->color('secondary'),
                    TextEntry::make('peserta.tipe_kartu_identitas')
                        ->label('Tipe Kartu Identitas')
                        ->badge(),
                    TextEntry::make('peserta.nomor_kartu_identitas')
                        ->label('Nomor Kartu Identitas')
                        ->color('secondary'),
                    TextEntry::make('peserta.nama_kontak_darurat')
                        ->label('Nama Kontak Darurat')
                        ->color('secondary'),
                    TextEntry::make('peserta.nomor_kontak_darurat')
                        ->label('Nomor Kontak Darurat')
                        ->color('secondary'),
                    TextEntry::make('peserta.golongan_darah')
                        ->label('Golongan Darah')
                        ->badge()
                        ->color('secondary'),
                    TextEntry::make('peserta.komunitas')
                        ->label('Komunitas')
                        ->badge()
                        ->color('secondary'),
                    TextEntry::make('peserta.status_peserta')
                        ->label('Status Peserta')
                        ->badge(),
                ])->columns(2),
                Section::make('Data Alamat')->schema([
                    TextEntry::make('alamat')
                        ->label('Alamat')
                        ->columnSpanFull()
                        ->color('secondary'),
                    TextEntry::make('negara')
                        ->label('Negara')
                        ->color('secondary'),
                    TextEntry::make('prov.name')
                        ->label('Provinsi')
                        ->color('secondary'),
                    TextEntry::make('kab.name')
                        ->label('Kabupaten')
                        ->color('secondary'),
                    TextEntry::make('kec.name')
                        ->label('Kecamatan')
                        ->color('secondary'),
                ])->columns(2),
            ])->columnSpan(2),
            Group::make()->schema([
                Section::make('Status Pendaftaran')->schema([
                    TextEntry::make('no_bib')
                        ->label('No. BIB')
                        ->badge()
                        ->color('secondary'),
                    TextEntry::make('nama_bib')
                        ->label('Nama BIB')
                        ->badge()
                        ->color('secondary'),
                    TextEntry::make('kategori.nama')
                        ->label('Kategori')
                        ->badge(),
                    TextEntry::make('status_pendaftaran')
                        ->label('Status Pendaftaran')
                        ->badge(),
                    TextEntry::make('status_registrasi')
                        ->label('Status Registrasi')
                        ->badge(),
                    TextEntry::make('status_pengambilan')
                        ->label('Status Pengambilan')
                        ->formatStateUsing(fn(
                            $state,
                        ) => $state ? 'Sudah mengambil racepack' : 'Belum mengambil racepack')
                        ->color(fn($state) => $state ? 'success' : 'danger')
                        ->badge(),
                ])->columns(2),
                Section::make('Pembayaran')->schema([
                    TextEntry::make('pembayaran.tipe_pembayaran')
                        ->label('Tipe Bayar')->badge(),
                    TextEntry::make('pembayaran.status_pembayaran')
                        ->label('Status Pembayaran')->badge(),
                    TextEntry::make('status_pendaftaran')
                        ->label('Status Pendaftaran')->badge(),
                    TextEntry::make('pembayaran.total_harga')
                        ->label('Total Bayar')
                        ->formatStateUsing(fn($state) => Number::currency($state, 'IDR', 'id', 0))
                        ->color('primary'),
                    TextEntry::make('pembayaran.status_transaksi')
                        ->label('Status Transaksi')->badge(),
                ])->columns(2),
                Section::make('E-Tiket')->schema([
                    TextEntry::make('qr_url')
                        ->hiddenLabel()
                        ->formatStateUsing(fn(string $state, $record) => \LaraZeus\Qr\Facades\Qr::render(
                            data: $state,
                            options: $record->qr_options,
                        )),
                ])->columns(1),
            ])->columns(1),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->deferLoading()
            ->columns([
                Tables\Columns\TextColumn::make('uuid_pendaftaran')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('peserta.nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable(),
                Tables\Columns\TextColumn::make('peserta.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('peserta.no_telp')
                    ->label('No. Telp')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('peserta.jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('peserta.tempat_lahir')
                    ->label('Tempat Lahir')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('peserta.tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('alamat')
                    ->label('Alamat')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('negara')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('prov.name')
                    ->label('Provinsi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kab.name')
                    ->label('Kabupaten')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kec.name')
                    ->label('Kecamatan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('peserta.tipe_kartu_identitas')
                    ->label('Tipe Kartu Identitas')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('peserta.nomor_kartu_identitas')
                    ->label('Nomor Kartu Identitas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('peserta.nama_kontak_darurat')
                    ->label('Nama Kontak Darurat')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('peserta.nomor_kontak_darurat')
                    ->label('Nomor Kontak Darurat')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('peserta.golongan_darah')
                    ->label('Golongan Darah')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ukuran_jersey')
                    ->label('Ukuran Jersey')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('kategori.nama')
                    ->label('Kategori Lomba')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('peserta.komunitas')
                    ->label('Komunitas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status_registrasi')
                    ->label('Status Registrasi')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status_pendaftaran')
                    ->label('Jenis Daftar')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_registrasi')
                    ->label('Status Registrasi')
                    ->options(StatusRegistrasi::class)
                    ->searchable(),
                Tables\Filters\SelectFilter::make('status_pendaftaran')
                    ->label('Status Pendaftaran')
                    ->options(StatusPendaftaran::class)
                    ->searchable(),
                Tables\Filters\SelectFilter::make('kategori_lomba')
                    ->label('Kategori Lomba')
                    ->relationship('kategori', 'nama')
                    ->preload()
                    ->searchable(),
                Tables\Filters\TernaryFilter::make('status_pengambilan')
                    ->label('Status Pengambilan')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah')
                    ->falseLabel('Belum')
                    ->nullable(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('update_pembayaran')
                    ->label('Status Bayar')
                    ->icon('heroicon-o-check')
                    ->color('yellow')
                    ->action(function ($record): void {
                        self::checkPembayaran($record);
                    }),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->action(function ($record): void {
                            $record->delete();
                            $record->pembayaran?->delete();
                        }),
                    Tables\Actions\ForceDeleteAction::make()
                        ->action(function ($record): void {
                            $record->forceDelete();
                            $record->pembayaran?->forceDelete();
                        }),
                    Tables\Actions\RestoreAction::make()
                        ->action(function ($record): void {
                            $record->restore();
                            $record->pembayaran?->restore();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $records->each(function (Model $record): void {
                                $record->delete();
                                $record->pembayaran?->delete();
                            });
                        }),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $records->each(function (Model $record): void {
                                $record->forceDelete();
                                $record->pembayaran?->forceDelete();
                            });
                        }),
                    Tables\Actions\RestoreBulkAction::make()
                        ->action(function (Collection $records): void {
                            $records->each(function (Model $record): void {
                                $record->restore();
                                $record->pembayaran?->restore();
                            });
                        }),
                    Tables\Actions\BulkAction::make('check_pembayaran')
                        ->label('Check Pembayaran')
                        ->icon('heroicon-s-check')
                        ->color(Color::Yellow)
                        ->action(function (Collection $records): void {
                            $records->each(function (Model $record): void {
                                self::checkPembayaran($record);
                            });
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getWidgets(): array
    {
        return [
            PendaftaranOverview::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('created_at')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendaftarans::route('/'),
            'create' => Pages\CreatePendaftaran::route('/create'),
            'view' => Pages\ViewPendaftaran::route('/{record}'),
            'edit' => Pages\EditPendaftaran::route('/{record}/edit'),
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model|\App\Models\Earlybird  $record
     * @return void
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    private static function checkPembayaran(Model|Pendaftaran $record): void
    {
        $orderId = $record->pembayaran?->order_id;
        $data = MidtransAPI::getStatusMessage($orderId);

        if (null === $data['status_message'] || '404' === $data['status']) {
            Notification::make()
                ->danger()
                ->title('Status Transaksi ' . $data['status'])
                ->body($data['status_message'])
                ->send();
            return;
        }

        $notif = Notification::make('statuspembayaran');
        match ($data['status']) {
            'success' => $notif->success(),
            'warning' => $notif->warning(),
            'info' => $notif->info(),
            'danger' => $notif->danger(),
        };

        $notif->title('Notifikasi Pembayaran')
            ->body($data['status_message'])
            ->send()
            ->sendToDatabase(auth()->user());
    }
}
