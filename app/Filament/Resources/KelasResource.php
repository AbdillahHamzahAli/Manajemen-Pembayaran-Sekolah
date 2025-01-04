<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelasResource\Pages;
use App\Filament\Resources\KelasResource\RelationManagers;
use App\Models\Kelas;
use App\Models\Tahun_Ajaran;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $pluralModelLabel = 'Data Kelas';

    protected static ?string $slug = 'data-kelas';

    protected static ?string $navigationGroup = 'Input Data';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_kelas')
                    ->label('Nama Kelas')
                    ->required(),
                Select::make('tingkat_kelas')
                    ->label('Tingkat Kelas')
                    ->options([
                        'X' => 'X',
                        'XI' => 'XI',
                        'XII' => 'XII',
                    ])
                    ->required(),
                Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(Tahun_Ajaran::pluck('tahun_ajaran', 'id'))
                    ->searchable()
                    ->required(),
            ])
            ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kelas')
                    ->label('Nama Kelas')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tingkat_kelas')
                    ->label('Tingkat Kelas'),
                TextColumn::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->getStateUsing(fn ($record) => Tahun_Ajaran::find($record->tahun_ajaran_id)?->tahun_ajaran)
            ])
            ->filters([
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->action(function (Kelas $kelas) {
                        if ($kelas->siswa()->exists()) {
                            Notification::make()
                                ->title('Tidak Dapat Menghapus Data')
                                ->body('Kelas ini masih memiliki data Siswa.')
                                ->danger()
                                ->send();
                            return;
                        }
                        $kelas->delete();
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
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }
}
