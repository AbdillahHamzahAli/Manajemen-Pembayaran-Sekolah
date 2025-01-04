<?php

namespace App\Filament\Resources\AnggotaKelasResource\Pages;

use App\Filament\Resources\AnggotaKelasResource;
use App\Models\Anggota_Kelas;
use App\Models\Kelas;
use App\Models\Tahun_Ajaran;
use Doctrine\DBAL\Schema\Table;
use Faker\Provider\ar_EG\Text;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ListAnggotaKelas extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    protected static string $resource = AnggotaKelasResource::class;

    protected static string $view = 'filament.resources.anggota-kelas-resource.pages.list-anggota-kelas';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah Anggota Kelas')
                ->icon('heroicon-o-plus-circle')
                ->url(route('filament.admin.resources.anggota-kelas.create')),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Kelas::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('nama_kelas')
                ->label('Kelas')
                ->getStateUsing(fn ($record) => $record->tingkat_kelas . ' ' . $record->nama_kelas)
                ->sortable()
                ->searchable(),
            TextColumn::make('tahun_ajaran_id')
                ->label('Tahun Ajaran')
                ->getStateUsing(fn ($record) => Tahun_Ajaran::find($record->tahun_ajaran_id)?->tahun_ajaran)
                ->sortable()
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('tingkat_kelas')
                    ->label('Tingkat Kelas')
                    ->options([
                        'X' => 'X',
                        'XI' => 'XI',
                        'XII' => 'XII',
                    ]),
            SelectFilter::make('tahun_ajaran_id')
                ->label('Tahun Ajaran')
                ->options(Tahun_Ajaran::pluck('tahun_ajaran', 'id'))
                ->searchable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\ViewAction::make('show')
                ->url(fn ($record) => route('filament.admin.resources.anggota-kelas.view', $record)),
        ];
    }
}
