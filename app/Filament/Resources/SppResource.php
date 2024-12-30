<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SppResource\Pages;
use App\Filament\Resources\SppResource\RelationManagers;
use App\Models\Spp;
use App\Models\Tahun_Ajaran;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SppResource extends Resource
{
    protected static ?string $model = Spp::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $pluralModelLabel = 'SPP';

    protected static ?string $slug = 'SPP';

    protected static ?string $navigationGroup = 'Input Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nominal')
                    ->required()
                    ->numeric()
                    ->required(),
                
                Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(Tahun_Ajaran::pluck('tahun_ajaran', 'id'))
                    ->searchable()
                    ->reactive(),
                
                Select::make('kelas_id')
                    ->label('Kelas')
                    ->options(function (callable $get) {
                        $tahunAjaran = $get('tahun_ajaran_id');
                        if ($tahunAjaran) {
                            return \App\Models\Kelas::where('tahun_ajaran_id', $tahunAjaran)->pluck('nama_kelas', 'id');
                        }
                        return [];
                    })
                    ->unique()
                    ->searchable()
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->numeric(),
                TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->searchable(),
                TextColumn::make('kelas.tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->getStateUsing(fn ($record) => Tahun_Ajaran::find($record->kelas->tahun_ajaran_id)?->tahun_ajaran)
            ])
            ->filters([
                SelectFilter::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(Tahun_Ajaran::pluck('tahun_ajaran', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            return $query->whereHas('kelas', function (Builder $query) use ($data) {
                                $query->where('tahun_ajaran_id', $data['value']);
                            });
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSpps::route('/'),
            'create' => Pages\CreateSpp::route('/create'),
            'edit' => Pages\EditSpp::route('/{record}/edit'),
        ];
    }
}
