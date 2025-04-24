<?php

namespace App\Models;

use App\Enums\GolonganDarah;
use App\Enums\JenisKelamin;
use App\Enums\StatusDaftar;
use App\Enums\TipeKartuIdentitas;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasUuids;

    protected $table = 'peserta';

    public function uniqueIds(): array
    {
        return ['uuid_peserta'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid_peserta';
    }

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'uuid_peserta' => 'string',
            'tipe_kartu_identitas' => TipeKartuIdentitas::class,
            'golongan_darah' => GolonganDarah::class,
            'jenis_kelamin' => JenisKelamin::class,
            'status_peserta' => StatusDaftar::class,
        ];
    }
}
