<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusPendaftaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravolt\Indonesia\Models\City;

class KategoriLomba extends Model
{
    protected $table = 'kategori_lomba';

    protected $fillable = [
        'nama',
        'harga',
        'warna',
        'kategori',
        'kabupaten',
        'deskripsi',
    ];

    protected $casts = [
        'kategori' => StatusPendaftaran::class,
        'kabupaten' => 'array',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'id', 'kategori_lomba');
    }

    public function kab(): BelongsTo
    {
        return $this->belongsTo(City::class, 'kabupaten', 'code');
    }
}
