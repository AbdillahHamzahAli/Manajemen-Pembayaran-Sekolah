<?php

namespace App\Filament\Resources\LaporanSiswaResource\Pages;

use App\Filament\Resources\LaporanSiswaResource;
use App\Models\Anggota_Kelas;
use App\Models\Iuran;
use App\Models\Siswa;
use App\Models\SPP;
use App\Models\Tahun_Ajaran;
use App\Models\Transaksi_Iuran;
use App\Models\Transaksi_SPP;
use Doctrine\DBAL\Schema\Table;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;


class ViewLaporanSiswa extends Page
{
    protected static string $resource = LaporanSiswaResource::class;

    protected static string $view = 'filament.resources.laporan-siswa-resource.pages.view-laporan-siswa';

    protected static ?string $title = 'Laporan Siswa';

    public Siswa $siswa;
    public $tahunAjaran;

    public ?SPP $SPP = null;
    public $Iuran = null;
    public $TransaksiSPP;
    public $TransaksiIuran;

    public function mount($record): void
    {
        // memunculkan data siswa yang memiliki anggota kelas atau tidak
        $this->siswa = Siswa::when(
                Anggota_Kelas::where('siswa_nis', $record)->exists(),
                fn($query) => $query->with('anggotaKelas.kelas.tahunAjaran'),
        )->findOrFail($record);

        // memunculkan data jika siswa menjadi anggota kelas
        $this->tahunAjaran = $this->siswa->anggotaKelas->isNotEmpty() 
            ? $this->siswa->anggotaKelas->first()->kelas->tahunAjaran->id 
            : null;        
        
        // munculkan data SPP berdasarkan kelas dan tahun ajaran
        if ($this->tahunAjaran !== null) {
            $this->SPP = SPP::with('kelas')
                ->whereHas('kelas', function (Builder $query) {
                    $query->where('id', $this->siswa->anggotaKelas->first()->kelas->id)
                    ->where('tahun_ajaran_id', $this->tahunAjaran);
            })->first();

            $this->Iuran = Iuran::where('tahun_ajaran_id', $this->tahunAjaran)->get();
        } 
        
        if ($this->SPP !== null) {
            $this->TransaksiSPP = Transaksi_SPP::where('spp_id', $this->SPP->id)
                ->where('anggota_kelas_id', $this->siswa->anggotaKelas->where('kelas.tahun_ajaran_id', $this->tahunAjaran)->first()->id)
                ->selectRaw('bulan, SUM(bayar) as total_bayar')
                ->groupBy('bulan')
                ->get();
        }
        
        if($this->Iuran !== null) {
            $this->TransaksiIuran = Transaksi_Iuran::where('anggota_kelas_id', $this->siswa->anggotaKelas->where('kelas.tahun_ajaran_id', $this->tahunAjaran)->first()->id)
                ->whereIn('iuran_id', $this->Iuran->pluck('id'))
                ->selectRaw('iuran_id, SUM(bayar) as total_bayar')
                ->groupBy('iuran_id')
                ->get();
        }
    }

    public function filter(): void
    {
        $this->tahunAjaran = $this->tahunAjaran;
        
        if ($this->tahunAjaran !== null) {
            $this->SPP = SPP::with('kelas')
                ->whereHas('kelas', function (Builder $query) {
                    $query->where('id', $this->siswa->anggotaKelas->where('kelas.tahun_ajaran_id', $this->tahunAjaran)->first()->kelas->id)
                    ->where('tahun_ajaran_id', $this->tahunAjaran);
            })->first();
            $this->Iuran = Iuran::where('tahun_ajaran_id', $this->tahunAjaran)->get();
        }

        if ($this->SPP !== null) {
            $this->TransaksiSPP = Transaksi_SPP::where('spp_id', $this->SPP->id)
                ->where('anggota_kelas_id', $this->siswa->anggotaKelas->where('kelas.tahun_ajaran_id', $this->tahunAjaran)->first()->id)
                ->selectRaw('bulan, SUM(bayar) as total_bayar')
                ->groupBy('bulan')
                ->get();
        }

        if($this->Iuran !== null) {
            $this->TransaksiIuran = Transaksi_Iuran::where('anggota_kelas_id', $this->siswa->anggotaKelas->where('kelas.tahun_ajaran_id', $this->tahunAjaran)->first()->id)
                ->whereIn('iuran_id', $this->Iuran->pluck('id'))
                ->selectRaw('iuran_id, SUM(bayar) as total_bayar')
                ->groupBy('iuran_id')
                ->get();
        }
    }
}
