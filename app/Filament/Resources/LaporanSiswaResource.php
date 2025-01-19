<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanSiswaResource\Pages;
use App\Filament\Resources\LaporanSiswaResource\RelationManagers;
use App\Models\LaporanSiswa;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LaporanSiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $pluralModelLabel = 'Per Siswa';

    protected static ?string $slug = 'laporan-siswa';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanSiswa::route('/'),
            'view' => Pages\ViewLaporanSiswa::route('/{record}'),
        ];
    }

}
