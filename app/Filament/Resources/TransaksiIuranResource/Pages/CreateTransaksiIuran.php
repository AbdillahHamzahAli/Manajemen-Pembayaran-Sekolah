<?php

namespace App\Filament\Resources\TransaksiIuranResource\Pages;

use App\Filament\Resources\TransaksiIuranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use App\Models\Anggota_Kelas;
use App\Models\Iuran;
use App\Models\Transaksi_Iuran;
use Illuminate\Database\Eloquent\Model;

class CreateTransaksiIuran extends CreateRecord
{
    protected static string $resource = TransaksiIuranResource::class;

    protected static ?string $title = 'Transaksi Iuran';


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $anggotaKelas = Anggota_Kelas::where('siswa_nis', $data['siswa_nis'])
            ->where('kelas_id', $data['kelas_id'])
            ->first();

        $iuran = Iuran::where('id', $data['iuran_id'])->first();

        $oldTransaksi = Transaksi_Iuran::where('anggota_kelas_id', $anggotaKelas->id)
            ->where('iuran_id', $data['iuran_id'])
            ->sum('bayar');

        $tunggakan = $oldTransaksi === 0 ?
        ($iuran->nominal - $data['bayar']) :
        ($iuran->nominal - $data['bayar'] - $oldTransaksi);

        $status = $tunggakan > 0 ? 'Belum Lunas' : 'Lunas';
        
        $newData = [
            'anggota_kelas_id' => $anggotaKelas->id,
            'iuran_id' => $data['iuran_id'],
            'bayar' => $data['bayar'],
            'tunggakan' => $tunggakan,
            'status' => $status,
        ];

        return $newData;
    }


    protected function handleRecordCreation(array $data): Model
    {
        Transaksi_Iuran::create($data);
        return new Transaksi_Iuran();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
