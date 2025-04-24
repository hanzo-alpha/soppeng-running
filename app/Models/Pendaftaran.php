<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GolonganDarah;
use App\Enums\JenisKelamin;
use App\Enums\StatusPendaftaran;
use App\Enums\StatusRegistrasi;
use App\Enums\TipeKartuIdentitas;
use App\Enums\UkuranJersey;
use App\Traits\HasWilayah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendaftaran extends Model
{
    use HasWilayah;
    use SoftDeletes;

    protected $table = 'pendaftaran';

    protected $fillable = [
        'peserta_id',
        'no_bib',
        'nama_bib',
        'alamat',
        'negara',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'ukuran_jersey',
        'jumlah_peserta',
        'kategori_lomba',
        'status_registrasi',
        'status_pendaftaran',
        'uuid_pendaftaran',
        'qr_url',
        'qr_options',
    ];

//    protected $with = ['kategori', 'pembayaran', 'peserta'];

    public function uniqueIds(): array
    {
        return ['uuid_pendaftaran'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid_pendaftaran';
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'pendaftaran_id', 'id');
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriLomba::class, 'kategori_lomba', 'id');
    }

    protected function casts(): array
    {
        return [
            'uuid_pendaftaran' => 'string',
            'ukuran_jersey' => UkuranJersey::class,
            'status_registrasi' => StatusRegistrasi::class,
            'status_pendaftaran' => StatusPendaftaran::class,
            'status_pengambilan' => 'boolean',
            'qr_options' => 'array',
            'no_bib' => 'string',
            'nama_bib' => 'string',
        ];
    }
}
