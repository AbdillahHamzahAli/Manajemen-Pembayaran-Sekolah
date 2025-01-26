<?php

namespace App\Filament\Resources\TransaksiSPPResource\Pages;

use App\Filament\Resources\TransaksiSPPResource;
use App\Models\Anggota_Kelas;
use App\Models\SPP;
use App\Models\Transaksi_SPP;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class CreateTransaksiSPP extends CreateRecord
{
    protected static string $resource = TransaksiSPPResource::class;

    protected static ?string $title = 'Tambah Transaksi SPP';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $anggotaKelas = Anggota_Kelas::where('siswa_nis', $data['siswa_nis'])
            ->where('kelas_id', $data['kelas_id'])
            ->first();

        $spp = SPP::where('kelas_id', $data['kelas_id'])->first();        
        
        if (!$spp) {
            Notification::make()
                ->title('Tidak Dapat Menambah Data')
                ->body('Data SPP belum ada, Silakan tambah data SPP')
                ->danger()
                ->send();
            throw ValidationException::withMessages([
                'nominal' => 'Data SPP belum ada',
            ]);
        }

        $oldTransaksi = Transaksi_SPP::where('anggota_kelas_id', $anggotaKelas->id)
            ->where('bulan', $data['bulan'])
            ->sum('bayar');            

        $tunggakan = $oldTransaksi === 0 ? 
            ($spp->nominal - $data['bayar']) : 
            ($spp->nominal - $data['bayar'] - $oldTransaksi);        
        
        // dd($tunggakan);
        $status = $tunggakan > 0 ? 'Belum Lunas' : 'Lunas';

        $newData = [
            'anggota_kelas_id' => $anggotaKelas->id,
            'spp_id' => $spp->id,
            'bulan' => $data['bulan'],
            'bayar' => $data['bayar'],
            'tunggakan' => $tunggakan,
            'status' => $status,
        ];
        

        return $newData;
    }


    protected function handleRecordCreation(array $data): Model
    {
        Transaksi_SPP::create($data);
        return new Transaksi_SPP();
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
