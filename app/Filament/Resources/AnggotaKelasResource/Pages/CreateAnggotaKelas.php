<?php

namespace App\Filament\Resources\AnggotaKelasResource\Pages;

use App\Filament\Resources\AnggotaKelasResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Models\Anggota_Kelas;
use Illuminate\Database\Eloquent\Model;


class CreateAnggotaKelas extends CreateRecord
{
    protected static string $resource = AnggotaKelasResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);
        $newData = [];
        foreach ($data['anggota'] as $anggota) {
            $newData[] = [
                'kelas_id' => $data['kelas_id'],
                'siswa_nis' => $anggota['siswa_nis'],
            ];
        }

        $data = $newData;
        return $data;
    }

 
    protected function handleRecordCreation(array $data): Model
    {
        static::getModel()::insert($data);
        return new Anggota_Kelas();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
