<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table): void {
            $table->boolean('status_pengambilan')->nullable()->default(false)->after('status_pendaftaran');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table): void {
            $table->dropColumn('status_pengambilan');
        });
    }
};
