<?php

use App\Enums\StatusDaftar;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_peserta')->nullable()->default(Str::uuid()->toString());
            $table->string('nama_lengkap');
            $table->string('email');
            $table->string('no_telp');
            $table->string('jenis_kelamin');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('tipe_kartu_identitas');
            $table->string('nomor_kartu_identitas');
            $table->string('nama_kontak_darurat');
            $table->string('nomor_kontak_darurat');
            $table->string('golongan_darah');
            $table->string('komunitas')->nullable();
            $table->string('status_peserta')->nullable()->default(StatusDaftar::BELUM_TERDAFTAR);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};
