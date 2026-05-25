<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UbahPassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static string $view = 'filament.pages.ubah-password';

    protected static ?string $title = 'Ubah Password';

    protected static ?string $navigationLabel = 'Ubah Password';

    protected static ?int $navigationSort = 99;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Keamanan Akun')
                    ->description('Silakan isi form di bawah ini untuk mengganti password Anda secara mandiri.')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Password Saat Ini')
                            ->password()
                            ->required()
                            ->rules(['current_password']),
                        TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->required()
                            ->rules(['confirmed', Password::min(8)])
                            ->helperText('Minimal 8 karakter.'),
                        TextInput::make('new_password_confirmation')
                            ->label('Konfirmasi Password Baru')
                            ->password()
                            ->required(),
                    ])
                    ->columns(1)
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        auth()->user()->update([
            'password' => Hash::make($data['new_password']),
        ]);

        $this->form->fill();

        Notification::make()
            ->title('Password Berhasil Diubah')
            ->body('Password Anda telah berhasil diperbarui. Silakan gunakan password baru ini pada login berikutnya.')
            ->success()
            ->send();
    }
}
