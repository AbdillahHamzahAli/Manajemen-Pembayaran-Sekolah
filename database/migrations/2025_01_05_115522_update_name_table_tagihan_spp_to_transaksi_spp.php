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
        Schema::rename('tagihan_spp', 'transaksi_spp');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('transaksi_spp', 'tagihan_spp');
    }
};
