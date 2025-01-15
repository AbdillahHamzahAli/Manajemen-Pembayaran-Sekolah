<?php

namespace App\Filament\Resources\TransaksiIuranResource\Pages;

use App\Filament\Resources\TransaksiIuranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiIurans extends ListRecords
{
    protected static string $resource = TransaksiIuranResource::class;

    protected static ?string $title = 'Transaksi Iuran';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Transaksi Iuran'),
        ];
    }
}
