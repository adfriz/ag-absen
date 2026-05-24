<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IzinGuruResource\Pages;
use App\Filament\Resources\IzinGuruResource\RelationManagers;
use App\Models\IzinGuru;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IzinGuruResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    protected static ?string $model = IzinGuru::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Izin & Guru Pengganti';

    protected static ?string $modelLabel = 'Pengajuan Izin Guru';

    protected static ?string $pluralModelLabel = 'Pengajuan Izin Guru';

    protected static ?string $navigationGroup = 'Manajemen Kehadiran';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'Pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->relationship('teacher', 'name', fn ($query) => $query->where('role', 'guru'))
                                    ->disabled(fn (string $context): bool => $context === 'edit')
                                    ->required()
                                    ->label('Guru Yang Mengajukan'),
                                Forms\Components\Select::make('jenis_izin')
                                    ->options([
                                        'Sakit' => 'Sakit',
                                        'Izin' => 'Izin',
                                        'Dinas Luar' => 'Dinas Luar',
                                        'Cuti' => 'Cuti',
                                    ])
                                    ->disabled(fn (string $context): bool => $context === 'edit')
                                    ->required()
                                    ->label('Jenis Izin'),
                                Forms\Components\DatePicker::make('tanggal_mulai')
                                    ->disabled(fn (string $context): bool => $context === 'edit')
                                    ->required()
                                    ->label('Mulai Tanggal'),
                                Forms\Components\DatePicker::make('tanggal_selesai')
                                    ->disabled(fn (string $context): bool => $context === 'edit')
                                    ->required()
                                    ->label('Selesai Tanggal'),
                                Forms\Components\Textarea::make('alasan')
                                    ->disabled(fn (string $context): bool => $context === 'edit')
                                    ->required()
                                    ->columnSpan(2)
                                    ->label('Alasan Izin'),
                                Forms\Components\FileUpload::make('bukti_surat')
                                    ->disabled(fn (string $context): bool => $context === 'edit')
                                    ->columnSpan(2)
                                    ->label('Unduh/Lihat Bukti Surat Fisik'),
                            ]),
                    ])->columnSpan(2),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'Pending' => 'Pending',
                                'Disetujui' => 'Disetujui',
                                'Ditolak' => 'Ditolak',
                            ])
                            ->required()
                            ->reactive()
                            ->label('Status Persetujuan'),
                        Forms\Components\Textarea::make('catatan_admin')
                            ->placeholder('Masukkan catatan jika menolak atau memberikan instruksi khusus')
                            ->label('Catatan Admin'),
                    ])->columnSpan(1),

                Forms\Components\Section::make('Penunjukan Guru Pengganti (Substitusi)')
                    ->description('Tunjuk guru pengganti untuk jadwal-jadwal guru yang berhalangan selama masa izin.')
                    ->schema([
                        Forms\Components\Repeater::make('substitusi')
                            ->relationship('substitusi')
                            ->schema([
                                Forms\Components\Select::make('jadwal_id')
                                    ->label('Jadwal Asli')
                                    ->options(function (Forms\Get $get, ?IzinGuru $record) {
                                        $userId = $record?->user_id;
                                        if (!$userId) return [];
                                        return \App\Models\Jadwal::where('user_id', $userId)
                                            ->with(['classroom', 'mataPelajaran'])
                                            ->get()
                                            ->mapWithKeys(fn ($j) => [$j->id => "{$j->hari}: {$j->mataPelajaran->nama} di {$j->classroom->nama} ({$j->jam_mulai} - {$j->jam_selesai})"]);
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\DatePicker::make('tanggal')
                                    ->label('Tanggal Penggantian')
                                    ->required(),
                                Forms\Components\Select::make('guru_pengganti_id')
                                    ->label('Guru Pengganti')
                                    ->options(\App\Models\User::where('role', 'guru')->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->columns(3)
                            ->label('Jadwal Guru Pengganti')
                    ])
                    ->visible(fn (Forms\Get $get) => $get('status') === 'Disetujui')
                    ->columnSpan(3)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('teacher.name')
                    ->sortable()
                    ->searchable()
                    ->label('Nama Guru'),
                Tables\Columns\TextColumn::make('jenis_izin')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Sakit' => 'warning',
                        'Izin' => 'primary',
                        'Dinas Luar' => 'info',
                        'Cuti' => 'gray',
                    })
                    ->label('Jenis Izin'),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->date('d F Y')
                    ->label('Mulai'),
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->date('d F Y')
                    ->label('Selesai'),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Pending' => 'warning',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                    })
                    ->label('Status'),
                Tables\Columns\TextColumn::make('substitusi_count')
                    ->state(fn ($record) => $record->substitusi()->count() . ' Jadwal')
                    ->label('Substitusi'),
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
            'index' => Pages\ManageIzinGurus::route('/'),
        ];
    }
}
