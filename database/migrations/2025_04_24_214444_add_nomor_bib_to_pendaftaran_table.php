<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table): void {
            $table->string('nomor_bib')->nullable()->after('peserta_id');
            $table->string('nama_bib')->nullable()->after('nomor_bib');
            ;
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table): void {
            $table->dropColumn(['nomor_bib', 'nama_bib']);
        });
    }
};
