<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\JadwalSayaResource\Pages;
use App\Models\Jadwal;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JadwalSayaResource extends Resource
{
    protected static ?string $model = Jadwal::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Jadwal Saya';

    protected static ?string $modelLabel = 'Jadwal Mengajar';

    protected static ?string $pluralModelLabel = 'Jadwal Mengajar';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->whereHas('tahunAjaran', function ($q) {
                $q->where('apakah_aktif', true);
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('classroom.nama')
                    ->sortable()
                    ->searchable()
                    ->label('Kelas')
                    ->badge(),
                Tables\Columns\TextColumn::make('mataPelajaran.nama')
                    ->sortable()
                    ->searchable()
                    ->label('Mata Pelajaran'),
                Tables\Columns\TextColumn::make('hari')
                    ->sortable()
                    ->label('Hari'),
                Tables\Columns\TextColumn::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('tahunAjaran')
                    ->label('Tahun Ajaran')
                    ->state(fn ($record) => "{$record->tahunAjaran->tahun} - Semester {$record->tahunAjaran->semester}"),
            ])
            ->filters([
                //
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwalSayas::route('/'),
        ];
    }
}
