<?php

namespace App\Filament\Resources\TransaksiIuranResource\Pages;

use App\Filament\Resources\TransaksiIuranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiIuran extends EditRecord
{
    protected static string $resource = TransaksiIuranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
