<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Enums\GolonganDarah;
use App\Enums\JenisKelamin;
use App\Enums\PaymentStatus;
use App\Enums\StatusBayar;
use App\Enums\StatusDaftar;
use App\Enums\StatusPendaftaran;
use App\Enums\StatusRegistrasi;
use App\Enums\TipeBayar;
use App\Enums\TipeKartuIdentitas;
use App\Enums\UkuranJersey;
use App\Mail\PembayaranBerhasil;
use App\Models\KategoriLomba;
use App\Models\Pembayaran;
use App\Models\Peserta;
use App\Services\MidtransAPI;
use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;

use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use function Filament\Support\is_app_url;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use Throwable;

class Pendaftaran extends Page implements HasForms
{
    use CanUseDatabaseTransactions;
    use HasUnsavedDataChangesAlert;
    use InteractsWithFormActions;
    use InteractsWithForms;

    private const int MINIMUM_AGE = 18;

    public ?Model $record = null;
    public ?array $data = [];

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';
    protected static ?string $slug = 'registrasi-peserta';
    protected static string $view = 'filament.app.pages.pendafaran';
    protected static ?string $navigationLabel = 'Registrasi Peserta';
    protected static bool $shouldRegisterNavigation = true;
    protected ?string $heading = 'Pendaftaran Online Peserta ETC Night Run & Concert 2025';
    protected ?string $subheading = 'Silahkan lengkapi data peserta di bawah ini.';

    public static function getNavigationBadge(): ?string
    {
        return (string) \App\Models\Pendaftaran::count();
    }

    /** Builds the form configuration */
    public static function formOtomatis(): array
    {
        return [
            Forms\Components\Wizard::make([
                self::stepDataPribadiPeserta(),
                self::stepDataAlamatPeserta(),
                self::stepDataPendaftaranPeserta(),
            ])
                ->submitAction(self::renderSubmitAction())
                ->columnSpanFull(),
        ];
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    #[On('detailTransaction')]
    public function detailTransaction($result)
    {
        $transactionOrderId = $result['order_id'] ?? null;
        $transactionStatus = $result['transaction_status'] ?? null;
        $paymentType = $result['payment_type'] ?? TipeBayar::QRIS;
        $transactionTime = $result['transaction_time'] ?? null;
        $grossAmount = $result['gross_amount'] ?? 0;
        $redirectUrl = $result['finish_redirect_url'] ?? $this->getRedirectUrl();

        // Retrieve registrasi and related pembayaran
        $registrasi = \App\Models\Pendaftaran::where('uuid_pendaftaran', $transactionOrderId)->first();
        $peserta = $registrasi?->peserta;
        $pembayaran = $registrasi?->pembayaran;

        // Handle missing pembayaran
        if (null === $pembayaran) {
            return $this->sendNotification(
                'Gagal',
                'Transaksi tidak ditemukan',
                'danger',
                'heroicon-o-check-circle',
            );
        }

        // Process payment and registration statuses
        $statusBayar = $this->getPaymentStatus($transactionStatus);
        $statusRegistrasi = $this->getRegistrationStatus($transactionStatus);
        $statusPendaftaran = $this->getPendaftaranStatus($registrasi->status_pendaftaran);
        $paymentTypeEnum = $this->getPaymentType($paymentType);
        $statusDaftar = StatusRegistrasi::BERHASIL === $statusRegistrasi
            ? StatusDaftar::TERDAFTAR
            : StatusDaftar::BELUM_TERDAFTAR;

        // Update pembayaran and registrasi
        $this->updatePayment(
            $pembayaran,
            $transactionOrderId,
            $paymentTypeEnum,
            $statusBayar,
            $transactionStatus,
            $result,
            $grossAmount,
        );

        $this->updatePeserta($peserta, $statusDaftar);
        $this->updateRegistration($registrasi, $statusRegistrasi);

        // Handle redirect
        $this->redirectToUrl($this->getRedirectUrl());

        // Send email in non-production environments
        $this->sendNotificationEmail($pembayaran, $registrasi);

        // Send appropriate payment notifications
        return $this->handlePaymentNotification($statusBayar, $transactionOrderId, $grossAmount, $transactionTime);

    }

    /**
     * @throws Throwable
     */
    public function create(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $data = $this->form->getState();

            $this->record = $this->handleRecordCreation($data);

            $this->form->model($this->getRecord());

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();
            throw $exception;
        }

        $this->rememberData();

        $this->getCreatedNotification()?->send();

    }

    public function getModel(): string
    {
        return \App\Models\Pendaftaran::class;
    }

    public function getRecord(): ?Model
    {
        return $this->record;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(static::formOtomatis())
            ->columns(2);
    }

    public function getFormStatePath(): ?string
    {
        return 'data';
    }

    /**
     * @throws Exception
     */
    protected function handleRecordCreation(array $data): Model
    {
        $this->initializeDefaults($data);
        $peserta = $data['peserta'];

        $biaya = biaya_pendaftaran($data['kategori_lomba']) ?? 0;
        $totalAmount = $biaya;
        $kategori = KategoriLomba::find($data['kategori_lomba']);
        $namaKegiatan = $this->buildNamaKegiatan($kategori);
        $merchant = 'Soppeng Berlari';

        midtrans_config();

        $transactions = $this->createTransactionData($data['uuid_pendaftaran'], $totalAmount);
        $items = $this->createItemData($biaya, $kategori, $namaKegiatan, $merchant);
        $customers = $this->createCustomerData($data);

        $detailTransaksi = [
            'transactions' => $transactions,
            'items' => $items,
            'customers' => $customers,
            'custom_field1' => 5000,
        ];

        $idPeserta = $this->createPeserta($peserta);
        $data['peserta_id'] = $idPeserta;
        unset($data['peserta']);
        $record = new ($this->getModel())($data);
        $record->save();

        $this->createPembayaran($record, $data, $detailTransaksi, $namaKegiatan, $biaya, $totalAmount);

        $snapToken = MidtransAPI::getSnapTokenApi($transactions, $items, $customers);
        $this->dispatch('processPayment', ['snap_token' => $snapToken]);

        return $record;

    }


    protected function getCreatedNotification(): ?Notification
    {
        $title = $this->getCreatedNotificationTitle();

        if (blank($title)) {
            return null;
        }

        return Notification::make()
            ->success()
            ->title($title)
            ->body($this->getCreatedNotificationMessage());
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return $this->getCreatedNotificationMessage() ?? __('filament-panels::resources/pages/create-record.notifications.created.title');
    }

    protected function getCreatedNotificationMessage(): ?string
    {
        return 'Pembayaran berhasil ditambahkan.';
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form($this->makeForm()
                ->operation('create')
                ->model($this->getModel())
                ->statePath($this->getFormStatePath())
                ->columns($this->hasInlineLabels() ? 1 : 2)
                ->inlineLabel($this->hasInlineLabels())),
        ];
    }

    protected function getSubmitFormAction(): Action
    {
        return $this->getCreateFormAction();
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label('Pembayaran')
            ->icon('heroicon-o-credit-card')
            ->submit('create')
            ->keyBindings(['ctrl+s']);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return Pendaftaran::getUrl();
    }

    /** Step 1: Data Pribadi Peserta */
    private static function stepDataPribadiPeserta(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('Data Pribadi Peserta')
            ->icon('heroicon-o-user')
            ->schema([
                Section::make('Data Peserta')
                    ->schema(self::buildDataPesertaFields())
                    ->model(Peserta::class)
                    ->columns(2),
            ]);
    }

    /** Step 2: Data Alamat Peserta */
    private static function stepDataAlamatPeserta(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('Data Alamat Peserta')
            ->icon('heroicon-o-map-pin')
            ->schema([
                Section::make('Data Alamat Peserta')
                    ->schema(self::buildDataAlamatFields())
                    ->columns(2),
            ]);
    }

    /** Step 2: Data Alamat Peserta */
    private static function stepDataPendaftaranPeserta(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('Data Pendaftaran')
            ->icon('heroicon-o-credit-card')
            ->completedIcon('heroicon-m-hand-thumb-up')
            ->schema([
                Section::make('Data Alamat Peserta')
                    ->schema(self::buildDataPendaftaranFields())
                    ->columns(2),
            ]);
    }

    /** Fields for "Data Peserta" */
    private static function buildDataPesertaFields(): array
    {
        return [
            Forms\Components\TextInput::make('uuid_pendaftaran')
                ->label('Kode Peserta')
                ->required()
                ->hidden()
                ->default(generateUuid())
                ->suffixAction(self::copyToClipboardAction())
                ->maxLength(255),

            Forms\Components\TextInput::make('nama_lengkap')
                ->label('Nama Peserta')
                ->required()
                ->autofocus()
                ->maxLength(255),
            Forms\Components\TextInput::make('no_telp')
                ->label('Nomor Telepon/WA Peserta')
                ->required()
                ->numeric()
                ->maxLength(12),
            Forms\Components\TextInput::make('email')
                ->email()
                ->unique(ignoreRecord: true)
                ->label('Email Peserta')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('tempat_lahir')
                ->label('Tempat Lahir')
                ->required(),
            Forms\Components\DatePicker::make('tanggal_lahir')
                ->label('Tanggal Lahir')
                ->required()
                ->displayFormat('d-m-Y')
                ->format('Y-m-d')
                ->rules([fn(): Closure => self::validateMinimumAgeClosure()]),
            Forms\Components\Select::make('jenis_kelamin')
                ->label('Jenis Kelamin')
                ->options(JenisKelamin::class)
                ->required()
                ->default(JenisKelamin::LAKI),
            Forms\Components\Select::make('tipe_kartu_identitas')
                ->label('Tipe Kartu Identitas')
                ->options(TipeKartuIdentitas::class)
                ->required()
                ->default(TipeKartuIdentitas::KTP),
            Forms\Components\TextInput::make('nomor_kartu_identitas')
                ->label('Nomor Kartu Identitas')
                ->required()
                ->numeric()
                ->minLength(16)
                ->maxLength(16),
            Forms\Components\TextInput::make('nama_kontak_darurat')
                ->label('Nama Kontak Darurat')
                ->required(),
            Forms\Components\TextInput::make('nomor_kontak_darurat')
                ->label('Nomor Kontak Darurat')
                ->required()
                ->minLength(9)
                ->maxLength(12),
            Forms\Components\Select::make('golongan_darah')
                ->label('Golongan Darah')
                ->options(GolonganDarah::class)
                ->required(),
            Forms\Components\TextInput::make('komunitas')
                ->label('Komunitas (Optional)')
                ->maxLength(255),
        ];
    }

    /** Fields for "Data Alamat Peserta" */
    private static function buildDataAlamatFields(): array
    {
        return [
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
            Forms\Components\Grid::make()->schema([
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
        ];

    }

    /** Fields for "Data Pendaftaran" */
    private static function buildDataPendaftaranFields(): array
    {
        return [
            Forms\Components\Select::make('status_pendaftaran')
                ->label('Jenis Pendaftaran')
                ->disabled()
                ->options(StatusPendaftaran::class)
                ->native(false)
                ->live(onBlur: true)
                ->required()
                ->dehydrated()
                ->default(StatusPendaftaran::TIKET_RUN)
                ->afterStateUpdated(fn(Forms\Set $set) => $set('kategori_lomba', null)),
            Forms\Components\Select::make('kategori_lomba')
                ->label('Kategori Lomba')
                ->relationship(
                    name: 'kategori',
                    titleAttribute: 'nama',
                    modifyQueryUsing: fn(
                        Forms\Get $get,
                        Builder $query,
                    ) => $query->where('kategori', $get('status_pendaftaran')),
                )
                ->preload()
                ->native(false)
                ->required(),
            Forms\Components\Select::make('ukuran_jersey')
                ->label('Ukuran Jersey')
                ->native(false)
                ->options(UkuranJersey::class)
                ->enum(UkuranJersey::class)
                ->required(),
            Forms\Components\TextInput::make('no_bib')
                ->label('Nomor BIB Peserta')
                ->default(generateNomorBib())
                ->required(),

            Forms\Components\TextInput::make('nama_bib')
                ->label('Nama BIB Peserta')
                ->required(),
        ];
    }

    /** Action for copying text to clipboard */
    private static function copyToClipboardAction(): Forms\Components\Actions\Action
    {
        return Forms\Components\Actions\Action::make('copy')
            ->icon('heroicon-s-clipboard-document-check')
            ->action(fn($livewire, $state) => $livewire->js(
                'window.navigator.clipboard.writeText("' . $state . '");
                           $tooltip("' . __('Copied to clipboard') . '", { timeout: 1500 });',
            ));
    }

    /** Closure to validate minimum age */
    private static function validateMinimumAgeClosure(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $age = Carbon::parse($value)->age;
            if ($age < self::MINIMUM_AGE) {
                $fail('Tanggal lahir harus lebih dari 18 tahun');
            }
        };
    }

    /** Render Submit Button using HTML String */
    private static function renderSubmitAction(): HtmlString
    {
        return new HtmlString(Blade::render(
            <<<BLADE
                <x-filament::button
                    type="submit"
                    size="sm"
                >
                    Submit
                </x-filament::button>
            BLADE,
        ));
    }

    private function initializeDefaults(array &$data): void
    {
        $data['uuid_pendaftaran'] ??= generateUuid();
        $data['status_registrasi'] ??= StatusRegistrasi::BELUM_BAYAR;
        $data['provinsi'] ??= '73';
    }

    private function buildNamaKegiatan(KategoriLomba $kategori): string
    {
        return 'Pendaftaran ETC Night Run & Concert - ' . $kategori->nama;
    }

    private function createTransactionData(string $orderId, int|float $grossAmount): array
    {
        return [
            'order_id' => $orderId,
            'gross_amount' => $grossAmount,
        ];
    }

    private function createItemData(int|float $price, KategoriLomba $kategori, string $eventName, string $merchant): array
    {
        return [
            'id' => generateUuid(),
            'price' => $price,
            'quantity' => 1,
            'name' => $eventName,
            'merchant_name' => $merchant,
            'category' => $kategori->nama,
        ];
    }

    private function createCustomerData(array $data): array
    {
        return [
            'first_name' => $data['nama_lengkap'],
            'last_name' => '',
            'email' => $data['email'],
            'phone' => $data['no_telp'],
            'address' => $data['alamat'],
            'shipping_address' => [
                'first_name' => $data['nama_lengkap'],
                'last_name' => '',
                'email' => $data['email'],
                'phone' => $data['no_telp'],
                'address' => $data['alamat'],
            ],
        ];
    }

    private function createPeserta(array $data): ?int
    {
        $peserta = Peserta::create([
            'nama_lengkap' => $data['nama_lengkap'],
            'no_telp' => $data['no_telp'],
            'email' => $data['email'],
            'tempat_lahir' => $data['tempat_lahir'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'tipe_kartu_identitas' => $data['tipe_kartu_identitas'],
            'nomor_kartu_identitas' => $data['nomor_kartu_identitas'],
            'nama_kontak_darurat' => $data['nama_kontak_darurat'],
            'nomor_kontak_darurat' => $data['nomor_kontak_darurat'],
            'golongan_darah' => $data['golongan_darah'],
            'komunitas' => $data['komunitas'],
        ]);

        return $peserta->id;
    }

    private function createPembayaran(
        Model $record,
        array $data,
        array $detailTransaksi,
        string $eventName,
        int|float $unitPrice,
        int|float $totalAmount,
    ): void {
        Pembayaran::create([
            'order_id' => $record->uuid_pendaftaran,
            'pendaftaran_id' => $record->id,
            'nama_kegiatan' => $eventName,
            'jumlah' => 1,
            'satuan' => 'Peserta',
            'harga_satuan' => $unitPrice,
            'total_harga' => $totalAmount,
            'status_pembayaran' => StatusBayar::BELUM_BAYAR,
            'status_transaksi' => PaymentStatus::CAPTURE,
            'keterangan' => null,
            'detail_transaksi' => $detailTransaksi,
            'lampiran' => null,
        ]);
    }

    private function getPaymentStatus(?string $transactionStatus): StatusBayar
    {
        return match ($transactionStatus) {
            PaymentStatus::SETTLEMENT->value, PaymentStatus::CAPTURE->value => StatusBayar::SUDAH_BAYAR,
            PaymentStatus::FAILURE->value, PaymentStatus::CANCEL->value,
            PaymentStatus::DENY->value, PaymentStatus::EXPIRE->value => StatusBayar::GAGAL,
            PaymentStatus::PENDING->value => StatusBayar::PENDING,
            default => StatusBayar::BELUM_BAYAR,
        };
    }

    private function getRegistrationStatus(?string $transactionStatus): StatusRegistrasi
    {
        return match ($transactionStatus) {
            PaymentStatus::SETTLEMENT->value, PaymentStatus::CAPTURE->value => StatusRegistrasi::BERHASIL,
            PaymentStatus::FAILURE->value, PaymentStatus::CANCEL->value,
            PaymentStatus::DENY->value, PaymentStatus::EXPIRE->value => StatusRegistrasi::BATAL,
            PaymentStatus::PENDING->value => StatusRegistrasi::TUNDA,
            PaymentStatus::AUTHORIZE->value => StatusRegistrasi::PROSES,
            PaymentStatus::CHARGEBACK->value, PaymentStatus::PARTIAL_REFUND->value,
            PaymentStatus::REFUND->value, PaymentStatus::PARTIAL_CHARGEBACK->value => StatusRegistrasi::PENGEMBALIAN,
        };
    }

    private function getPendaftaranStatus($transactionStatus): StatusPendaftaran
    {
        return match ($transactionStatus) {
            StatusPendaftaran::TIKET_KONSER->value => StatusPendaftaran::TIKET_KONSER,
            StatusPendaftaran::NIGHT_RUN->value => StatusPendaftaran::NIGHT_RUN,
            default => StatusPendaftaran::TIKET_RUN,
        };
    }

    private function getPaymentType($paymentType): TipeBayar
    {
        return match ($paymentType) {
            'qris', 'gopay', 'shopeepay' => TipeBayar::QRIS,
            default => TipeBayar::TRANSFER,
        };
    }

    private function updatePayment(
        $pembayaran,
        $orderId,
        $paymentType,
        $status,
        $transactionStatus,
        $result,
        $grossAmount,
    ): void {
        $pembayaran->order_id = $orderId;
        $pembayaran->uuid_pembayaran ??= generateUuid();
        $pembayaran->tipe_pembayaran = $paymentType;
        $pembayaran->status_pembayaran = $status;
        $pembayaran->total_harga = $grossAmount;
        $pembayaran->harga_satuan = $grossAmount;
        $pembayaran->status_transaksi = $transactionStatus;
        $pembayaran->detail_transaksi = $result;
        $pembayaran->lampiran = null;
        $pembayaran->save();
    }

    private function updatePeserta($peserta, $statusPeserta): void
    {
        $peserta->status_peserta = $statusPeserta;
        $peserta->save();
    }

    private function updateRegistration($registrasi, $statusRegistrasi): void
    {
        $registrasi->status_registrasi = $statusRegistrasi;
        $registrasi->status_pengambilan = false;
        $registrasi->save();
    }

    private function redirectToUrl($redirectUrl): void
    {
        $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url((string) $redirectUrl));
    }

    private function sendNotificationEmail($pembayaran, $registrasi): void
    {
        if ( ! app()->environment('production')) {
            defer(function () use ($pembayaran, $registrasi): void {
                Mail::to($registrasi->email)->send(new PembayaranBerhasil($pembayaran));
            });
        }
    }

    private function handlePaymentNotification(
        StatusBayar $status,
        ?string $orderId,
        int|float|string $grossAmount,
        ?string $transactionTime,
    ) {
        $formattedAmount = Number::format((float) $grossAmount, locale: 'id');
        return match ($status) {
            StatusBayar::BELUM_BAYAR => $this->sendNotification(
                'Belum ada Pembayaran',
                "Transaksi dengan order id: {$orderId} belum melakukan pembayaran sebesar Rp. {$formattedAmount}",
                'danger',
                'heroicon-o-x-circle',
            ),
            StatusBayar::PENDING => $this->sendNotification(
                'Menunggu Pembayaran',
                "Transaksi dengan order id: {$orderId} masih menunggu pembayaran sebesar Rp. {$formattedAmount}",
                'warning',
                'heroicon-o-information-circle',
            ),
            StatusBayar::GAGAL => $this->sendNotification(
                'Pembayaran Gagal',
                "Transaksi dengan order id: {$orderId} gagal melakukan pembayaran sebesar Rp. {$formattedAmount}",
                'danger',
                'heroicon-o-x-mark',
            ),
            default => $this->sendNotification(
                'Pembayaran Berhasil',
                "Transaksi dengan order id: {$orderId} telah berhasil dilakukan pada " .
                Carbon::parse($transactionTime)->format('d/m/Y H:i:s') .
                " sebesar Rp. {$formattedAmount}",
                'success',
                'heroicon-o-check-circle',
            ),
        };
    }

    private function sendNotification(string $title, string $body, string $type, string $icon)
    {
        return Notification::make()
            ->title($title)
            ->{$type}()
            ->body($body)
            ->icon($icon)
            ->send();
    }
}
