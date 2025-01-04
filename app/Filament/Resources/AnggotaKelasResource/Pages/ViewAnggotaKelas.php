<?php

namespace App\Filament\Resources\AnggotaKelasResource\Pages;

use App\Filament\Resources\AnggotaKelasResource;
use Filament\Resources\Pages\Page;

use App\Models\Anggota_Kelas;
use App\Models\Kelas;
use Filament\Tables;
use App\Models\Tahun_Ajaran;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class ViewAnggotaKelas extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = AnggotaKelasResource::class;

    protected static string $view = 'filament.resources.anggota-kelas-resource.pages.view-anggota-kelas';

    public Kelas $kelas;

    public function mount($record)
    {
        $this->kelas = Kelas::with('tahunAjaran')->findOrFail($record);
    }

    protected function getTableQuery(): Builder
    {
        return Anggota_Kelas::query()->where('kelas_id', $this->kelas->id);
    }
    
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('siswa.nis')->label('NIS'),
            TextColumn::make('siswa.nama_siswa')->label('Nama')->sortable(),
            TextColumn::make('siswa.jenis_kelamin')->label('Jenis Kelamin'),
            ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\DeleteAction::make(),
        ];
    }
    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\DeleteBulkAction::make(),
        ];
    }
}
