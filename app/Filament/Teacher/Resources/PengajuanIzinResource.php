<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\PengajuanIzinResource\Pages;
use App\Models\IzinGuru;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengajuanIzinResource extends Resource
{
    protected static ?string $model = IzinGuru::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Pengajuan Izin';

    protected static ?string $modelLabel = 'Pengajuan Izin';

    protected static ?string $pluralModelLabel = 'Pengajuan Izin';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenis_izin')
                    ->options([
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                        'Dinas Luar' => 'Dinas Luar',
                        'Cuti' => 'Cuti',
                    ])
                    ->required()
                    ->label('Jenis Izin'),
                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->required()
                    ->label('Mulai Tanggal'),
                Forms\Components\DatePicker::make('tanggal_selesai')
                    ->required()
                    ->afterOrEqual('tanggal_mulai')
                    ->label('Selesai Tanggal'),
                Forms\Components\Textarea::make('alasan')
                    ->required()
                    ->label('Alasan Izin')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('bukti_surat')
                    ->label('Bukti Surat Fisik (Opsional)')
                    ->directory('bukti_surat')
                    ->columnSpanFull(),
                
                Forms\Components\Section::make('Tinjauan Admin')
                    ->schema([
                        Forms\Components\TextInput::make('status')
                            ->disabled()
                            ->label('Status Persetujuan'),
                        Forms\Components\Textarea::make('catatan_admin')
                            ->disabled()
                            ->label('Catatan Admin'),
                    ])
                    ->visible(fn ($record) => $record !== null)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                Tables\Columns\TextColumn::make('catatan_admin')
                    ->limit(30)
                    ->label('Catatan Admin'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status === 'Pending'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => $record->status === 'Pending'),
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
            'index' => Pages\ManagePengajuanIzins::route('/'),
        ];
    }
}
