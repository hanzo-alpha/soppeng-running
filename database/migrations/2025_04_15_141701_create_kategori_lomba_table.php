<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if ( ! Schema::hasTable('kategori_lomba')) {
            Schema::create('kategori_lomba', function (Blueprint $table): void {
                $table->id();
                $table->string('nama');
                $table->double('harga')->nullable();
                $table->string('warna')->nullable();
                $table->string('kategori')->nullable();
                $table->string('deskripsi')->nullable();
                $table->timestamps();
            });
        }

    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_lomba');
    }
};
