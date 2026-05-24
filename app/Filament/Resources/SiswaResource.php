<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Filament\Resources\SiswaResource\RelationManagers;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SiswaResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Siswa';

    protected static ?string $modelLabel = 'Siswa';

    protected static ?string $pluralModelLabel = 'Siswa';

    protected static ?string $slug = 'siswa';

    protected static ?string $navigationGroup = 'Data Master';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('nisn')
                            ->required()
                            ->unique(ignorable: fn ($record) => $record)
                            ->label('NISN'),
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->label('Nama Lengkap Siswa'),
                        Forms\Components\Select::make('jenis_kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required()
                            ->label('Jenis Kelamin'),
                        Forms\Components\Select::make('kelas_id')
                            ->label('Kelas (Tahun Ajaran Aktif)')
                            ->placeholder('Pilih kelas untuk tahun ajaran aktif saat ini')
                            ->options(\App\Models\Kelas::all()->pluck('nama', 'id'))
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component, $state, ?Siswa $record) {
                                if ($record) {
                                    $kelas = $record->kelasAktif();
                                    $component->state($kelas ? $kelas->id : null);
                                }
                            })
                            ->saveRelationshipsUsing(function (Siswa $record, $state, \Filament\Forms\Get $get) {
                                if ($state) {
                                    $tahunAktif = \App\Models\TahunAjaran::where('apakah_aktif', true)->first();
                                    if ($tahunAktif) {
                                        $record->kelas()->syncWithPivotValues([$state], [
                                            'tahun_ajaran_id' => $tahunAktif->id,
                                            'nomor_absen' => $get('nomor_absen'),
                                        ], false);
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('nomor_absen')
                            ->label('Nomor Absen')
                            ->numeric()
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component, $state, ?Siswa $record) {
                                if ($record) {
                                    $kelas = $record->kelasAktif();
                                    $component->state($kelas ? $kelas->pivot->nomor_absen : null);
                                }
                            }),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nisn')
                    ->sortable()
                    ->searchable()
                    ->label('NISN'),
                Tables\Columns\TextColumn::make('nama')
                    ->sortable()
                    ->searchable()
                    ->label('Nama Lengkap'),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('L/P')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nomor_absen')
                    ->label('No. Absen')
                    ->state(function (Siswa $record) {
                        $kelas = $record->kelasAktif();
                        return $kelas ? $kelas->pivot->nomor_absen : '-';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas_aktif')
                    ->label('Kelas (Aktif)')
                    ->state(function (Siswa $record) {
                        $kelas = $record->kelasAktif();
                        return $kelas ? $kelas->nama : 'Belum Ada Kelas';
                    })
                    ->badge()
                    ->color(fn ($state) => $state === 'Belum Ada Kelas' ? 'gray' : 'primary'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSiswas::route('/'),
        ];
    }
}
