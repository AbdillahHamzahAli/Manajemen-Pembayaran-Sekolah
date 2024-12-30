<?php

namespace App\Filament\Resources\SppResource\Pages;

use App\Filament\Resources\SppResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpps extends ListRecords
{
    protected static string $resource = SppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
