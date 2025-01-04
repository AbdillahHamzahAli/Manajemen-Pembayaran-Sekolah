<?php

namespace App\Filament\Resources\IuranResource\Pages;

use App\Filament\Resources\IuranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIuran extends CreateRecord
{
    protected static string $resource = IuranResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
