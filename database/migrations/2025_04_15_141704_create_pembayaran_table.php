<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid_pembayaran')->nullable()->index();
            $table->uuid('order_id')->nullable()->index();
            $table->foreignId('pendaftaran_id')->index()->nullable();
            $table->string('nama_kegiatan')->nullable();
            $table->string('ukuran_jersey')->nullable();
            $table->string('kategori_lomba')->nullable();
            $table->unsignedBigInteger('jumlah')->nullable()->default(0);
            $table->string('satuan')->nullable()->default('peserta');
            $table->double('harga_satuan')->nullable()->default(0);
            $table->double('total_harga')->nullable()->default(0);
            $table->string('tipe_pembayaran')->nullable();
            $table->string('status_pembayaran')->nullable();
            $table->string('status_transaksi')->nullable();
            $table->string('keterangan')->nullable();
            $table->json('detail_transaksi')->nullable();
            $table->string('lampiran')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
