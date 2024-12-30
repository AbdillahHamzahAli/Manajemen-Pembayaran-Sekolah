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

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::saving(function ($model) {
    //         $exists = self::where('nama_kelas', $model->nama_kelas)
    //             ->where('tahun_ajaran_id', $model->tahun_ajaran_id)
    //             ->where('tingkat_kelas', $model->tingkat_kelas)
    //             ->exists();

    //         if ($exists) {
    //             throw ValidationException::withMessages([
    //                 'nama_kelas' => 'Kelas dengan nama, tingkat, dan tahun ajaran ini sudah ada.',
    //             ]);
    //         }
    //     });
    // }

    public function tahunAjaran()
    {
        return $this->belongsTo(Tahun_Ajaran::class, 'tahun_ajaran_id', 'id');
    }
}
