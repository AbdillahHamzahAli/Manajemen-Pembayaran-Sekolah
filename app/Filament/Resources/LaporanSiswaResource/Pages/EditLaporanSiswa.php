<?php

namespace App\Filament\Resources\LaporanSiswaResource\Pages;

use App\Filament\Resources\LaporanSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanSiswa extends EditRecord
{
    protected static string $resource = LaporanSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
