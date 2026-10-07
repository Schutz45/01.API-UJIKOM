<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pinjam_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_pinjam_id')->constrained('detail_pinjam')->onDelete('cascade');
            $table->foreignId('unit_alat_id')->constrained('unit_alat')->onDelete('cascade');
            $table->enum('kondisi_keluar', ['baik', 'rusak'])->default('baik');
            $table->enum('kondisi_masuk', ['baik', 'rusak'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pinjam_unit');
    }
};
