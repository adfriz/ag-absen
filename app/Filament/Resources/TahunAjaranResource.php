<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TahunAjaranResource\Pages;
use App\Filament\Resources\TahunAjaranResource\RelationManagers;
use App\Models\TahunAjaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TahunAjaranResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    protected static ?string $model = TahunAjaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Tahun Ajaran';

    protected static ?string $modelLabel = 'Tahun Ajaran';

    protected static ?string $pluralModelLabel = 'Tahun Ajaran';

    protected static ?string $slug = 'tahun-ajaran';

    protected static ?string $navigationGroup = 'Data Master';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('tahun')
                            ->required()
                            ->placeholder('Contoh: 2025/2026')
                            ->label('Tahun Ajaran'),
                        Forms\Components\Select::make('semester')
                            ->options([
                                'Ganjil' => 'Ganjil',
                                'Genap' => 'Genap',
                            ])
                            ->required()
                            ->label('Semester'),
                        Forms\Components\Toggle::make('apakah_aktif')
                            ->default(false)
                            ->label('Status Aktif (Tahun Ajaran Berjalan)')
                            ->helperText('Hanya boleh ada satu tahun ajaran yang aktif secara bersamaan.'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tahun')
                    ->sortable()
                    ->searchable()
                    ->label('Tahun Ajaran'),
                Tables\Columns\TextColumn::make('semester')
                    ->sortable()
                    ->label('Semester'),
                Tables\Columns\IconColumn::make('apakah_aktif')
                    ->boolean()
                    ->sortable()
                    ->label('Status Aktif'),
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
            'index' => Pages\ManageTahunAjarans::route('/'),
        ];
    }
}
