<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Iuran extends Model
{
    use HasFactory;

    protected $table = 'iuran';
    protected $fillable = ['nama_iuran', 'nominal', 'tahun_ajaran_id'];

    public function tahunAjaran()
    {
        return $this->belongsTo(Tahun_Ajaran::class, 'tahun_ajaran_id', 'id');
    }

    public function transaksiIuran()
    {
        return $this->hasMany(Transaksi_Iuran::class, 'iuran_id', 'id');
    }
}
