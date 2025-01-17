<?php

namespace App\Filament\Resources\LaporanSiswaResource\Pages;

use App\Filament\Resources\LaporanSiswaResource;
use App\Models\Siswa;

use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\Page;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class ListLaporanSiswa extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = LaporanSiswaResource::class;

    protected static string $view = 'filament.resources.laporan-siswa-resource.pages.list-laporan-siswa';

    protected function getTableQuery(): Builder
    {
        return Siswa::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('nis')
                ->label('NIS')
                ->sortable()
                ->searchable(),
            TextColumn::make('nama_siswa')
                ->label('Nama Siswa')
                ->searchable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\ViewAction::make('show')
                ->url(fn (Siswa $record) => route('filament.admin.resources.laporan-siswa.view', $record)),
        ];
    }
}
