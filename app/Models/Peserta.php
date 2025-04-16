<?php

namespace App\Models;

use App\Enums\GolonganDarah;
use App\Enums\JenisKelamin;
use App\Enums\StatusDaftar;
use App\Enums\TipeKartuIdentitas;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $table = 'peserta';

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'uuid_pendaftaran' => 'string',
            'tipe_kartu_identitas' => TipeKartuIdentitas::class,
            'golongan_darah' => GolonganDarah::class,
            'jenis_kelamin' => JenisKelamin::class,
            'status_peserta' => StatusDaftar::class,
        ];
    }
}
