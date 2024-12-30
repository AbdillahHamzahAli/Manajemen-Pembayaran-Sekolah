<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Model
{
    //
    use HasFactory;
    protected $table = 'siswa';
    protected $primaryKey = 'nis';
    protected $fillable = ['nis','nama_siswa', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'tanggal_masuk', 'kelas_id'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id');
    }
}

