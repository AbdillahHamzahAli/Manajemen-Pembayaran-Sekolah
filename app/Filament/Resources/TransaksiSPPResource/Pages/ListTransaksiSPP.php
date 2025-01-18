<?php

namespace App\Filament\Resources\TransaksiSPPResource\Pages;

use App\Filament\Resources\TransaksiSPPResource;
use App\Models\Transaksi_SPP;
use Doctrine\DBAL\Schema\Table;
use Filament\Resources\Pages\Page;
use Filament\Actions;

use Filament\Tables;

use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;


class ListTransaksiSPP extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    protected static string $resource = TransaksiSPPResource::class;

    protected static string $view = 'filament.resources.transaksi-spp-resource.pages.list-transaksi-spp';

    protected static ?string $title = 'Transaksi SPP';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Transaksi SPP')
                ->url(route('filament.admin.resources.transaksi-spp.create')),
            
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Transaksi_SPP::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('anggota_kelas.siswa.nama_siswa')
                ->searchable(),
            TextColumn::make('bulan'),
            TextColumn::make('anggota_kelas.kelas.nama_kelas')
                ->label('Kelas')
                ->getStateUsing(fn ($record) => $record->anggota_kelas->kelas->tingkat_kelas . ' ' . $record->anggota_kelas->kelas->nama_kelas),
            TextColumn::make('tunggakan')
                ->money('IDR', true),
            TextColumn::make('status')
                ->label('Status')
                ->color(fn ($state) => match ($state) {
                    'Lunas' => 'success',
                    'Belum Lunas' => 'danger',
                    default => 'warning',
                }),
                TextColumn::make('created_at')
                    ->sortable()
                    ->hidden()      
            ];
    }

    protected function getDefaultTableSortColumn(): ?string    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }
    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->label('Download PDF')
                ->url(fn (Transaksi_SPP $record): string => route('transaksi-spp-invoice.pdf', $record->id)),
            Tables\Actions\DeleteAction::make('delete')
        ];
    }
    
    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\DeleteBulkAction::make(),
        ];
    }
}
