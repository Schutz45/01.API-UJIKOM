<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->dropColumn(['stok', 'stok_rusak', 'status_kondisi']);
        });
    }

    public function down(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->integer('stok')->default(0)->after('nama_alat');
            $table->integer('stok_rusak')->default(0)->after('stok');
            $table->string('status_kondisi')->nullable()->after('stok_rusak');
        });
    }
};
