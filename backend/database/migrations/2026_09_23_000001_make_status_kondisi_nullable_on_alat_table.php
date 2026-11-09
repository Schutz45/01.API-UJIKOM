<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * status_kondisi tidak lagi menjadi input manual.
 * Nilainya sekarang dihitung dari stok & stok_rusak via accessor di Model Alat.
 * Kolom dibuat nullable (bukan dihapus) untuk masa transisi data historis.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->string('status_kondisi')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->string('status_kondisi')->nullable(false)->default('Baik')->change();
        });
    }
};
