<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_kelas', 'tingkat_kelas', 'tahun_ajaran_id'];

    public function tahunAjaran()
    {
        return $this->belongsTo(Tahun_Ajaran::class, 'tahun_ajaran_id', 'id');
    }

    public function anggotaKelas()
    {
        return $this->hasMany(Anggota_Kelas::class, 'kelas_id', 'id');
    }
    
}
