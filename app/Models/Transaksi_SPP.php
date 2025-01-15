<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi_SPP extends Model
{
    protected $table = 'transaksi_spp';
    protected $fillable = ['anggota_kelas_id', 'spp_id', 'bulan', 'bayar', 'tunggakan', 'status'];
    public $timestamps = true;
    
    public function anggota_kelas()
    {
        return $this->belongsTo(anggota_kelas::class);
    }
    public function spp()
    {
        return $this->belongsTo(spp::class);
    }
}
