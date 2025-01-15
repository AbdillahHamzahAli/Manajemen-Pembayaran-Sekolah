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
        Schema::create('transaksi_iuran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iuran_id')->constrained('iuran');
            $table->foreignId('anggota_kelas_id')->constrained('anggota_kelas');
            $table->decimal('bayar', 10, 2);
            $table->decimal('tunggakan', 10, 2);
            $table->enum('status',['Lunas','Belum Lunas']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_iuran');
    }
};
