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
        Schema::create('transaksi_spp', function (Blueprint $table) {
            $table->id();
            $table->enum('bulan', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']);
            $table->decimal('bayar', 10, 2);
            $table->decimal('tunggakan', 10, 2);
            $table->enum('status', ['Lunas', 'Belum Lunas']);
            $table->foreignId('anggota_kelas_id')->constrained('anggota_kelas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('spp_id')->constrained('spp')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_spp');
    }
};
