<?php

namespace App\Filament\Resources\KelasResource\Pages;

use App\Filament\Resources\KelasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Filament\Notifications\Notification;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class EditKelas extends EditRecord
{
    protected static string $resource = KelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $exists = Kelas::where('nama_kelas', $data['nama_kelas'])
            ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->where('tingkat_kelas', $data['tingkat_kelas'])
            ->where('id', '!=', $record->id)
            ->exists();

        if ($exists) {
            Notification::make()
                ->title('Tidak Dapat Mengubah Data')
                ->body('Kelas dengan nama, tingkat, dan tahun ajaran ini sudah ada.')
                ->danger()
                ->send();
            throw ValidationException::withMessages([
                'nama_kelas' => 'Kelas dengan nama, tingkat, dan tahun ajaran ini sudah ada.',
            ]);
        }

        return parent::handleRecordUpdate($record, $data);
    }
}
