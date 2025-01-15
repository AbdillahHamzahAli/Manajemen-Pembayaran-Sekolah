<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IuranResource\Pages;
use App\Filament\Resources\IuranResource\RelationManagers;
use App\Models\Iuran;
use App\Models\Tahun_Ajaran;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Collection;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IuranResource extends Resource
{
    protected static ?string $model = Iuran::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $pluralModelLabel = 'Iuran';

    protected static ?string $slug = 'iuran';

    protected static ?string $navigationGroup = 'Input Data';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_iuran')
                    ->label('Nama Iuran')
                    ->required(),
                TextInput::make('nominal')
                    ->label('Nominal')
                    ->numeric()
                    ->required(),
                Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(Tahun_Ajaran::all()->pluck('tahun_ajaran', 'id'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_iuran')
                    ->label('Nama Iuran')
                    ->searchable(),
                TextColumn::make('nominal')
                    ->label('Nominal'),
                TextColumn::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->getStateUsing(fn ($record) => Tahun_Ajaran::find($record->tahun_ajaran_id)?->tahun_ajaran),
            ])
            ->filters([
                SelectFilter::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(fn () => Tahun_Ajaran::all()->pluck('tahun_ajaran', 'id')->toArray()),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->action(function (Iuran $iuran){
                        if($iuran->transaksiIuran()->exists()){
                            Notification::make()
                                ->title('Tidak Dapat Menghapus Data')
                                ->body('Data sudah digunakan pada Transaksi Iuran')
                                ->danger()
                                ->send();
                            return;
                        }
                        $iuran->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->transaksiIuran()->exists()) {
                                    Notification::make()
                                        ->title('Tidak Dapat Menghapus Data')
                                        ->body('Data sudah digunakan pada Transaksi Iuran')
                                        ->danger()
                                        ->send();
                                    return;
                                }
                                $record->delete();
                            }
                        }),
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
            'index' => Pages\ListIurans::route('/'),
            'create' => Pages\CreateIuran::route('/create'),
        ];
    }
}
