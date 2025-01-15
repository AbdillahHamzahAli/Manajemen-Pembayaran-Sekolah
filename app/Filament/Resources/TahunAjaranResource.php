<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TahunAjaranResource\Pages;
use App\Filament\Resources\TahunAjaranResource\RelationManagers;
use App\Models\Tahun_Ajaran;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Notifications\Collection;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;

class TahunAjaranResource extends Resource
{
    protected static ?string $model = Tahun_Ajaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    protected static ?string $pluralModelLabel = 'Tahun Ajaran';

    protected static ?string $slug = 'tahun-ajaran';

    protected static ?string $navigationGroup = 'Input Data';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            TextInput::make('tahun_ajaran')
                ->label('Tahun Ajaran')
                ->placeholder('2024/2025')
                ->unique()
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->action(function (Tahun_Ajaran $tahunAjaran) {
                        if ($tahunAjaran->kelas()->exists()) {
                            Notification::make()
                                ->title('Tidak Dapat Menghapus Data')
                                ->body('Tahun Ajaran ini masih memiliki data Kelas.')
                                ->danger()
                                ->send();
                            return;
                        }
                        $tahunAjaran->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                                foreach ($records as $record) {
                                    if ($record->kelas()->exists()) {
                                        Notification::make()
                                            ->title('Tidak Dapat Menghapus Data')
                                            ->body('Tahun Ajaran masih memiliki data Kelas.')
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
            'index' => Pages\ListTahunAjarans::route('/'),
            'create' => Pages\CreateTahunAjaran::route('/create'),
            'edit' => Pages\EditTahunAjaran::route('/{record}/edit'),
        ];
    }
}
