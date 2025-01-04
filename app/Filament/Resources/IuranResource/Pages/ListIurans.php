<?php

namespace App\Filament\Resources\IuranResource\Pages;

use App\Filament\Resources\IuranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIurans extends ListRecords
{
    protected static string $resource = IuranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Iuran'),
        ];
    }
}
