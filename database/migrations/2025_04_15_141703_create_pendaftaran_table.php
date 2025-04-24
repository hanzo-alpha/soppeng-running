<?php

declare(strict_types=1);

use App\Enums\StatusPendaftaran;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('pendaftaran', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid_pendaftaran')->nullable()->default(Str::uuid()->toString());
            $table->foreignId('peserta_id')->nullable();
            $table->string('alamat');
            $table->string('negara');
            $table->string('provinsi');
            $table->string('kabupaten');
            $table->string('kecamatan');
            $table->string('ukuran_jersey');
            $table->foreignId('kategori_lomba')->nullable();
            $table->string('komunitas')->nullable();
            $table->string('status_registrasi')->nullable();
            $table->string('status_pendaftaran')->nullable()->default(StatusPendaftaran::BELUM);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};
