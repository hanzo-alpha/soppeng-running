<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table): void {
            $table->string('qr_url')->nullable()->after('status_pengambilan');
            $table->text('qr_options')->nullable()->after('qr_url');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table): void {
            $table->dropColumn('qr_url');
            $table->dropColumn('qr_options');
        });
    }
};
