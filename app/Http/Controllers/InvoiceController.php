<?php

namespace App\Http\Controllers;

use App\Models\Iuran;
use App\Models\Siswa;
use App\Models\SPP;

use Illuminate\Http\Request;
use App\Models\Transaksi_Iuran;
use App\Models\Transaksi_SPP;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;


class InvoiceController extends Controller
{
    public function InvoiceSPP($record)
    {
        $transaksiSPP = Transaksi_SPP::findOrFail($record);
        $pdf = Pdf::loadView('invoices.SPP', compact('transaksiSPP'));
        return $pdf->stream('invoice-' . $transaksiSPP->anggota_kelas->siswa->nis . '.pdf');
    }

    public function InvoiceIuran($record)
    {
        $transaksiIuran = Transaksi_Iuran::findOrFail($record);
        $pdf = Pdf::loadView('invoices.iuran', compact('transaksiIuran'));
        return $pdf->stream('invoice-' . $transaksiIuran->anggota_kelas->siswa->nis . '.pdf');
    }

    public function LaporanSiswa($nis, $id)
    {
        $tahunAjaran = $id;
        $siswa = Siswa::findOrFail($nis);

        $SPP = SPP::with('kelas')
                ->whereHas('kelas', function (Builder $query) use ($siswa, $tahunAjaran) {
                    $query->where('id', $siswa->anggotaKelas->where('kelas.tahun_ajaran_id', $tahunAjaran)->first()->kelas->id)
                    ->where('tahun_ajaran_id', $tahunAjaran);
            })->first();

        $Iuran = Iuran::where('tahun_ajaran_id', $tahunAjaran)->get();

        $TransaksiSPP = Transaksi_SPP::where('spp_id', $SPP->id)
                ->where('anggota_kelas_id', $siswa->anggotaKelas->where('kelas.tahun_ajaran_id', $tahunAjaran)->first()->id)
                ->selectRaw('bulan, SUM(bayar) as total_bayar')
                ->groupBy('bulan')
                ->get();        
        $TransaksiIuran = Transaksi_Iuran::where('anggota_kelas_id', $siswa->anggotaKelas->where('kelas.tahun_ajaran_id', $tahunAjaran)->first()->id)
                ->whereIn('iuran_id', $Iuran->pluck('id'))
                ->selectRaw('iuran_id, SUM(bayar) as total_bayar')
                ->groupBy('iuran_id')
                ->get();        

        $pdf = Pdf::loadView('laporan.laporan-siswa', compact('siswa', 'tahunAjaran', 'SPP', 'Iuran', 'TransaksiSPP', 'TransaksiIuran'))->setPaper('a4', 'landscape');
        return $pdf->stream('laporan-siswa-' . $siswa->nis . '.pdf');    }
}
