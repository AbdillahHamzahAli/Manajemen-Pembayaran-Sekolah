<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SPP extends Model
{
    protected $table = 'SPP';
    protected $fillable = ['nominal', 'kelas_id'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id');
    }

    public function transaksiSPP()
    {
        return $this->hasMany(Transaksi_SPP::class, 'spp_id', 'id');
    }
}
