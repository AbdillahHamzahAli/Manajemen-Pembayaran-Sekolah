<?php

namespace App\Filament\Resources\AnggotaKelasResource\Pages;

use App\Filament\Resources\AnggotaKelasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnggotaKelas extends EditRecord
{
    protected static string $resource = AnggotaKelasResource::class;

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
}
