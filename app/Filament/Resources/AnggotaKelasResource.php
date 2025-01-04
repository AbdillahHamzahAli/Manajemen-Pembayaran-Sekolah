<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnggotaKelasResource\Pages;
use App\Filament\Resources\AnggotaKelasResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Models\Anggota_Kelas;
use App\Models\Tahun_Ajaran;
use App\Models\Kelas;
use App\Models\Siswa;
use Faker\Provider\ar_EG\Text;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Set;
use Filament\Tables\Columns\TextColumn;

class AnggotaKelasResource extends Resource
{
    protected static ?string $model = Anggota_Kelas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $pluralModelLabel = 'Anggota Kelas';

    protected static ?string $slug = 'anggota-kelas';

    protected static ?string $navigationGroup = 'Manajemen Kelas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('kelas_id')
                    ->label('Kelas')
                    ->options(Kelas::all()->sortBy(function ($kelas) {
                        $tahunAjaran = Tahun_Ajaran::find($kelas->tahun_ajaran_id);
                        return "{$tahunAjaran->tahun_ajaran} {$kelas->nama_kelas}";
                    })->mapWithKeys(function ($kelas) {
                        $tahunAjaran = Tahun_Ajaran::find($kelas->tahun_ajaran_id);
                        return [$kelas->id => "{$kelas->tingkat_kelas} {$kelas->nama_kelas} - {$tahunAjaran->tahun_ajaran}"];
                    }))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn (Set $set, $state) => 
                        $set('tahun_ajaran_id', Kelas::find($state)->tahun_ajaran_id ?? '')
                    )
                    ->required(),
                TextInput::make('tahun_ajaran_id')
                    ->disabled()
                    ->dehydrated(false)
                    ->reactive(),

                Repeater::make('anggota')
                    ->label('Anggota Kelas')
                    ->schema([
                        Select::make('siswa_nis')
                            ->label('Siswa')
                            ->options(function (callable $get) {
                                return 
                                    Siswa::with(['anggotaKelas','anggotaKelas.kelas'])
                                    ->get()
                                    ->mapWithKeys(function ($siswa) {
                                        $tahunAjaran = $siswa->anggotaKelas->first()->kelas->tahunAjaran->tahun_ajaran ?? '';
                                        return [$siswa->nis => "{$siswa->nis} - {$siswa->nama_siswa} - {$tahunAjaran}"];
                                    });
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

            ])
            ->filters([
                
            ])
            ->actions([
            
            ])
            ->bulkActions([
                
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnggotaKelas::route('/'),
            'create' => Pages\CreateAnggotaKelas::route('/create'),
            'view' => Pages\ViewAnggotaKelas::route('/{record}'),
            'edit' => Pages\EditAnggotaKelas::route('/{record}/edit'),
        ];
    }
}
