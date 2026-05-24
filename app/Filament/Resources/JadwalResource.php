<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalResource\Pages;
use App\Filament\Resources\JadwalResource\RelationManagers;
use App\Models\Jadwal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JadwalResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    protected static ?string $model = Jadwal::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Jadwal Pelajaran';

    protected static ?string $modelLabel = 'Jadwal Pelajaran';

    protected static ?string $pluralModelLabel = 'Jadwal Pelajaran';

    protected static ?string $navigationGroup = 'Data Master';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('teacher', 'name', fn ($query) => $query->where('role', 'guru'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Guru Pengajar'),
                        Forms\Components\Select::make('kelas_id')
                            ->relationship('classroom', 'nama')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Kelas'),
                        Forms\Components\Select::make('mata_pelajaran_id')
                            ->relationship('mataPelajaran', 'nama')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Mata Pelajaran'),
                        Forms\Components\Select::make('tahun_ajaran_id')
                            ->relationship('tahunAjaran', 'tahun', fn ($query) => $query->orderBy('tahun', 'desc')->orderBy('semester', 'desc'))
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->tahun} - Semester {$record->semester}")
                            ->required()
                            ->label('Tahun Ajaran'),
                        Forms\Components\Select::make('hari')
                            ->options([
                                'Senin' => 'Senin',
                                'Selasa' => 'Selasa',
                                'Rabu' => 'Rabu',
                                'Kamis' => 'Kamis',
                                'Jumat' => 'Jumat',
                                'Sabtu' => 'Sabtu',
                                'Minggu' => 'Minggu',
                            ])
                            ->required()
                            ->label('Hari'),
                        Forms\Components\TimePicker::make('jam_mulai')
                            ->required()
                            ->label('Jam Mulai'),
                        Forms\Components\TimePicker::make('jam_selesai')
                            ->required()
                            ->label('Jam Selesai'),
                    ])->columns(2)
            ]);
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
                Tables\Columns\TextColumn::make('teacher.name')
                    ->sortable()
                    ->searchable()
                    ->label('Guru Pengajar'),
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
                    ->state(fn ($record) => "{$record->tahunAjaran->tahun} ({$record->tahunAjaran->semester})"),
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
            'index' => Pages\ManageJadwals::route('/'),
        ];
    }
}
