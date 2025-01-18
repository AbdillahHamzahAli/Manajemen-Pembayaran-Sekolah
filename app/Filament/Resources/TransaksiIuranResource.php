<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiIuranResource\Pages;
use App\Filament\Resources\TransaksiIuranResource\RelationManagers;

use App\Models\Transaksi_Iuran;
use App\Models\Anggota_Kelas;
use App\Models\Iuran;
use App\Models\Kelas;
use App\Models\Tahun_Ajaran;
use App\Models\Siswa;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class TransaksiIuranResource extends Resource
{
    protected static ?string $model = Transaksi_Iuran::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $pluralModelLabel = 'Iuran';

    protected static ?string $slug = 'transaksi-iuran';
    
    protected static ?string $navigationGroup = 'Transaksi';
    
    protected static ?int $navigationSort = 2;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('siswa_nis')
                    ->label('NIS')
                    ->options(Siswa::pluck('nis', 'nis')) 
                    ->searchable()
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $siswa = Siswa::where('nis', $state)->first();
                            $set('siswa_nama', $siswa?->nama_siswa);
                            $set('kelas_id',null);
                            $set('tahun_ajaran',null);
                            $set('bayar',null);
                            $set('nominal','Data Iuran belum ada');
                        }
                    }),

                Select::make('siswa_nama')
                    ->label('Nama Siswa')
                    ->options(Siswa::pluck('nama_siswa', 'nama_siswa'))
                    ->searchable()
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $siswa = Siswa::where('nis', $state)->first();
                            $set('siswa_nama', $siswa?->nama_siswa);
                            $set('kelas_id',null);
                            $set('tahun_ajaran',null);
                            $set('bayar',null);
                            $set('nominal','Data Iuran belum ada');
                        }
                    }),

                Select::make('kelas_id')->label('Kelas')
                    ->options(function (callable $get) {

                        $siswaNis = $get('siswa_nis');
                        
                        return Kelas::with('anggotaKelas')
                            ->whereHas('anggotaKelas', function ($query) use ($siswaNis) {
                                $query->where('siswa_nis', $siswaNis);
                            })
                            ->get()
                            ->mapWithKeys(fn ($kelas) => [
                                $kelas->id => "{$kelas->tingkat_kelas} {$kelas->nama_kelas}",
                            ]);
                    })
                    ->searchable()
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $kelasTahunAjaran = Kelas::where('id', $state)->value('tahun_ajaran_id');
                            $tahunAjaran = Tahun_Ajaran::where('id', $kelasTahunAjaran)->value('tahun_ajaran');
                            $set('tahun_ajaran', $tahunAjaran);
                        }
                    }),
                
                TextInput::make('tahun_ajaran')->label('Tahun Ajaran')
                    ->disabled()
                    ->reactive()
                    ->required(),
                
                Select::make('iuran_id')
                    ->label('Jenis Iuran')
                    ->options(function (callable $get) {
                        $tahunAjaran = $get('tahun_ajaran');
                        $kelasID = $get('kelas_id');
                        $siswaNis = $get('siswa_nis');
                        if($tahunAjaran && $kelasID && $siswaNis) {
                            $tahunAjaranId = Tahun_Ajaran::where('tahun_ajaran', $tahunAjaran)->value('id');
                            $anggota = Anggota_Kelas::where('siswa_nis', $siswaNis)->where('kelas_id', $kelasID)->first();
                            $TrIuranLunas = Transaksi_Iuran::where('anggota_kelas_id', $anggota->id)
                                ->where('status', 'Lunas')
                                ->pluck('iuran_id');

                            return Iuran::where('tahun_ajaran_id', $tahunAjaranId)
                                    ->whereNotIn('id', $TrIuranLunas)
                                    ->pluck('nama_iuran', 'id');
                        }
                        return [];
                    })
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set,$get, $state) {
                        $siswaNis = $get('siswa_nis');
                        $kelasId = $get('kelas_id');

                        $transaksiIuran = Transaksi_Iuran::with('anggota_kelas')
                        ->whereHas('anggota_kelas', function ($query) use ($siswaNis, $kelasId) {
                            $query->where('siswa_nis', $siswaNis)
                                ->where('kelas_id', $kelasId);
                        })
                        ->where('iuran_id', $state)
                        ->where('status', '!=', 'lunas')
                            ->latest()
                            ->first();

                        if($transaksiIuran){
                            $set('nominal',number_format($transaksiIuran->tunggakan, 2, ',', '.'));
                        } else {
                            $set('nominal',number_format(Iuran::where('id',$state)->value('nominal'), 2, ',', '.') ?? 'Data Iuran belum ada');
                        }
                    })
                    ->required(),

                TextInput::make('nominal')
                    ->label('Nominal')
                    ->disabled() 
                    ->default('Data Iuran belum ada')
                    ->reactive()
                    ->prefix('Rp')
                    ->required()
                    ->dehydrated(true),
                
                TextInput::make('bayar')
                    ->label('Bayar')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('anggota_kelas.siswa.nama_siswa')
                    ->label('Nama Siswa')
                    ->searchable(),
                TextColumn::make('iuran.nama_iuran')
                    ->label('Jenis Iuran'),
                TextColumn::make('iuran.tahunAjaran.tahun_ajaran')
                    ->label('Tahun Ajaran'),
                TextColumn::make('status')
                    ->label('Status')
                    ->color(fn ($state) => match ($state) {
                        'Lunas' => 'success',
                        'Belum Lunas' => 'danger',
                        default => 'warning',
                    })
                
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->label('Download PDF')
                ->url(fn (Transaksi_Iuran $record): string => route('transaksi-iuran-invoice.pdf', $record->id)),
            
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTransaksiIurans::route('/'),
            'create' => Pages\CreateTransaksiIuran::route('/create'),
            'edit' => Pages\EditTransaksiIuran::route('/{record}/edit'),
        ];
    }
}
