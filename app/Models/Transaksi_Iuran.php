<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi_Iuran extends Model
{
    protected $table = 'transaksi_iuran';
    protected $fillable = ['anggota_kelas_id', 'iuran_id', 'bulan', 'bayar', 'tunggakan', 'status'];
    public $timestamps = true;

    public function anggota_kelas()
    {
        return $this->belongsTo(anggota_kelas::class);
    }
    public function iuran()
    {
        return $this->belongsTo(iuran::class);
    }
}
