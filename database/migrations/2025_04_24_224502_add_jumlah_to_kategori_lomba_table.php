<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('kategori_lomba', function (Blueprint $table): void {
            $table->integer('jumlah')->nullable()->default(0)->after('harga');
        });
    }

    public function down(): void
    {
        Schema::table('kategori_lomba', function (Blueprint $table): void {
            $table->dropColumn('jumlah');
        });
    }
};
