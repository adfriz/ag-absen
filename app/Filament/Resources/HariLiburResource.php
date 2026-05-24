<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HariLiburResource\Pages;
use App\Filament\Resources\HariLiburResource\RelationManagers;
use App\Models\HariLibur;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HariLiburResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    protected static ?string $model = HariLibur::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'Hari Libur / Tanggal Merah';

    protected static ?string $modelLabel = 'Hari Libur';

    protected static ?string $pluralModelLabel = 'Hari Libur';

    protected static ?string $navigationGroup = 'Data Master';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal')
                            ->visible(fn (string $context) => $context === 'edit')
                            ->required(fn (string $context) => $context === 'edit')
                            ->unique(ignorable: fn ($record) => $record)
                            ->label('Tanggal Hari Libur'),
                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->visible(fn (string $context) => $context === 'create')
                            ->required(fn (string $context) => $context === 'create')
                            ->label('Tanggal Mulai'),
                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->visible(fn (string $context) => $context === 'create')
                            ->label('Tanggal Selesai (Opsional)')
                            ->helperText('Kosongkan jika hanya libur 1 hari')
                            ->afterOrEqual('tanggal_mulai'),
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->placeholder('Contoh: Tahun Baru Islam, Libur Idul Fitri')
                            ->label('Nama Hari Libur'),
                        Forms\Components\Textarea::make('deskripsi')
                            ->placeholder('Keterangan atau catatan tambahan mengenai hari libur')
                            ->label('Keterangan / Deskripsi'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->sortable()
                    ->date('d F Y')
                    ->label('Tanggal Libur')
                    ->badge()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('nama')
                    ->sortable()
                    ->searchable()
                    ->label('Nama Hari Libur'),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->limit(50)
                    ->label('Deskripsi'),
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
            'index' => Pages\ManageHariLiburs::route('/'),
        ];
    }
}
