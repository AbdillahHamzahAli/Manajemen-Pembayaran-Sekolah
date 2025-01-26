<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiSPPResource\Pages;
use App\Filament\Resources\TransaksiSPPResource\RelationManagers;
use App\Models\Anggota_Kelas;
use App\Models\Kelas;
use App\Models\SPP;
use App\Models\Tahun_Ajaran;
use App\Models\Transaksi_SPP;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Container\Attributes\Tag;

class TransaksiSPPResource extends Resource
{
    protected static ?string $model = Transaksi_SPP::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $pluralModelLabel = 'SPP';

    protected static ?string $slug = 'transaksi-spp';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 1;

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
                            $set('bulan',null);
                            $set('bayar',null);
                            $set('nominal','Data SPP belum ada');
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
                            $siswa = Siswa::where('nama_siswa', $state)->first();
                            $set('siswa_nis', $siswa?->nis);
                            $set('kelas_id',null);
                            $set('tahun_ajaran',null);
                            $set('bulan',null);
                            $set('bayar',null);
                            $set('nominal','Data SPP belum ada');
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
                            $sppNominal = SPP::where('kelas_id', $state)->value('nominal');
                            if($sppNominal){
                                $set('nominal', number_format($sppNominal, 2, ',', '.'));
                            } else {
                                $set('nominal', 'Data SPP belum ada');
                            }
                            $kelasTahunAjaran = Kelas::where('id', $state)->value('tahun_ajaran_id');
                            $tahunAjaran = Tahun_Ajaran::where('id', $kelasTahunAjaran)->value('tahun_ajaran');
                            $set('tahun_ajaran', $tahunAjaran);
                        }
                    }),
                
                TextInput::make('tahun_ajaran')->label('Tahun Ajaran')
                    ->disabled()
                    ->reactive()
                    ->required(),

                Select::make('bulan')->label('Bulan')
                    ->options(function (callable $get) {
                        $siswaNis = $get('siswa_nis');
                        $kelasId = $get('kelas_id');
                        
                        $bulanTerbayar = Transaksi_SPP::with('anggota_kelas')
                            ->whereHas('anggota_kelas', function ($query) use ($siswaNis, $kelasId) {
                                    $query->where('siswa_nis', $siswaNis)
                                        ->where('kelas_id', $kelasId);
                                })
                            ->where('status', 'lunas')    
                            ->pluck('bulan')->toArray();

                        $bulanBelumLunas = Transaksi_SPP::with('anggota_kelas')
                            ->whereHas('anggota_kelas', function ($query) use ($siswaNis, $kelasId) {
                                    $query->where('siswa_nis', $siswaNis)
                                        ->where('kelas_id', $kelasId);
                                })
                            ->where('status', '!=', 'lunas')
                            ->get();

                        $semuaBulan = [
                            'Januari' => 'Januari',
                            'Februari' => 'Februari',
                            'Maret' => 'Maret',
                            'April' => 'April',
                            'Mei' => 'Mei',
                            'Juni' => 'Juni',
                            'Juli' => 'Juli',
                            'Agustus' => 'Agustus',
                            'September' => 'September',
                            'Oktober' => 'Oktober',
                            'November' => 'November',
                            'Desember' => 'Desember'
                        ];

                        return collect($semuaBulan)
                            ->reject(function ($value, $key) use ($bulanTerbayar) {
                                return in_array($value, $bulanTerbayar);
                            });
                    })
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $get, $state) {
                        $siswaNis = $get('siswa_nis');
                        $kelasId = $get('kelas_id');

                        if($kelasId){
                            $transaksi = Transaksi_SPP::with('anggota_kelas')
                            ->whereHas('anggota_kelas', function ($query) use ($siswaNis, $kelasId) {
                                    $query->where('siswa_nis', $siswaNis)
                                        ->where('kelas_id', $kelasId);
                                })
                            ->where('bulan', $state)
                            ->where('status', '!=', 'lunas')
                            ->latest()
                            ->first();

                            if($transaksi) {
                                $set('nominal',number_format($transaksi->tunggakan, 2, ',', '.'));
                            } else {
                                $sppNominal = SPP::where('kelas_id', $state)->value('nominal');
                                if($sppNominal){
                                    $set('nominal', number_format($sppNominal, 2, ',', '.'));
                                } else {
                                    $set('nominal', 'Data SPP belum ada');
                                }                      
                            }        
                        }
                    })
                    ->required(),

                TextInput::make('nominal')
                    ->label('Nominal')
                    ->disabled() 
                    ->default('Data SPP belum ada')
                    ->reactive()
                    ->prefix('Rp')
                    ->required()
                    ->dehydrated(true),
                
                TextInput::make('bayar')
                    ->label('Bayar')
                    ->numeric()
                    ->prefix('Rp')
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
            'index' => Pages\ListTransaksiSPP::route('/'),
            'create' => Pages\CreateTransaksiSPP::route('/create'),
        ];
    }
}
