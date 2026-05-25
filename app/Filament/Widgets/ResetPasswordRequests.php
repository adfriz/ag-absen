<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class ResetPasswordRequests extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->role === 'admin';
    }

    protected function getHeading(): string
    {
        return 'Permintaan Reset Password Guru';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->where('role', 'guru')
                    ->where('needs_password_reset', true)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Guru')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->label('Username / NIP')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Username disalin'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),
            ])
            ->actions([
                Tables\Actions\Action::make('reset_password')
                    ->label('Reset ke Default')
                    ->color('danger')
                    ->icon('heroicon-m-key')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password Guru')
                    ->modalDescription('Apakah Anda yakin ingin mereset password guru ini? Password akan diubah menjadi "alghazaly2026".')
                    ->modalSubmitActionLabel('Ya, Reset')
                    ->action(function (User $record) {
                        $record->update([
                            'password' => Hash::make('alghazaly2026'),
                            'needs_password_reset' => false,
                        ]);

                        Notification::make()
                            ->title('Password Berhasil Direset')
                            ->body("Password untuk {$record->name} telah diubah menjadi 'alghazaly2026'.")
                            ->success()
                            ->send();
                    })
            ])
            ->emptyStateHeading('Tidak ada permintaan reset password')
            ->emptyStateDescription('Semua akun guru dalam keadaan aman.');
    }
}
