<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Filament\Resources\SiswaResource\RelationManagers;
use App\Models\Siswa;
use App\Models\Kelas;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Date;
use Filament\Notifications\Notification;


class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    
    protected static ?string $pluralModelLabel = 'Data Siswa';

    protected static ?string $slug = 'data-siswa';

    protected static ?string $navigationGroup = 'Input Data';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nis')
                    ->label('NIS')
                    ->minLength(10)
                    ->required(),
                
                TextInput::make('nama_siswa')
                    ->label('Nama Siswa')
                    ->columnSpan(2)
                    ->required(),
                
                Select::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->required(),
                
                TextInput::make('tempat_lahir')
                    ->label('Tempat Lahir')
                    ->required(),
                
                DatePicker::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->required(),
                
                DatePicker::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->required(),
                
                // Select::make('kelas_id')
                //     ->label('Kelas')
                //     ->options(Kelas::pluck('nama_kelas', 'id'))
                //     ->searchable()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable(),
                
                TextColumn::make('nama_siswa')
                    ->label('Nama Siswa')
                    ->searchable(),
                
                TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
            ])
            ->filters([
                
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->action(function (Siswa $siswa) {
                        if ($siswa->anggotaKelas()->exists()) {
                            Notification::make()
                                ->title('Tidak Dapat Menghapus Data')
                                ->body('Siswa ini masih terdaftar sebagai anggota kelas.')
                                ->danger()
                                ->send();
                            return;
                        }
                        $siswa->delete();
                    }),
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
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}
