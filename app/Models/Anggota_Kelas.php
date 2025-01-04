<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota_Kelas extends Model
{
    protected $table = 'anggota_kelas';
    protected $fillable = ['siswa_nis', 'kelas_id'];
    public $timestamps = false;

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_nis', 'nis');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id','id');
    }
}
