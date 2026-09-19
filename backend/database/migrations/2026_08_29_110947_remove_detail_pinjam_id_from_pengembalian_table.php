<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('pengembalian', 'detail_pinjam_id')) {
            Schema::table('pengembalian', function (Blueprint $table) {
                $table->dropColumn('detail_pinjam_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->unsignedBigInteger('detail_pinjam_id');
        });
    }
};
