<?php

namespace App\Filament\Resources\KelasResource\Pages;

use Filament\Actions;
use App\Filament\Resources\KelasResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

use Filament\Notifications\Notification;
use App\Models\Kelas;

class CreateKelas extends CreateRecord
{
    protected static string $resource = KelasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $exists = Kelas::where('nama_kelas', $data['nama_kelas'])
            ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->where('tingkat_kelas', $data['tingkat_kelas'])
            ->exists();

        if ($exists) {
            Notification::make()
                ->title('Tidak Dapat Menambah Data')
                ->body('Kelas dengan nama, tingkat, dan tahun ajaran ini sudah ada.')
                ->danger()
                ->send();
            throw ValidationException::withMessages([
                'nama_kelas' => 'Kelas dengan nama, tingkat, dan tahun ajaran ini sudah ada.',
            ]);
        }

        return $data;
    }
}
