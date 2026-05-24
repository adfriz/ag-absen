<?php

namespace App\Livewire;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class PortalLogin extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    
    public string $role = 'guru'; // Default role

    public function mount(string $role = 'guru'): void
    {
        $this->role = $role;
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('username')
                    ->label('Username / NIP')
                    ->required()
                    ->autofocus(),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(),
            ])
            ->statePath('data');
    }

    public function authenticate()
    {
        $data = $this->form->getState();

        if (Auth::attempt(['username' => $data['username'], 'password' => $data['password']])) {
            $user = Auth::user();
            
            // Cek apakah role sesuai dengan pintu masuknya
            if ($user->role !== $this->role && $user->role !== 'admin') {
                Auth::logout();
                Notification::make()
                    ->title('Akses Ditolak')
                    ->body('Akun Anda tidak memiliki akses untuk peran ini.')
                    ->danger()
                    ->send();
                return;
            }

            request()->session()->regenerate();

            return redirect()->intended($this->role === 'admin' ? '/admin' : '/guru');
        }

        Notification::make()
            ->title('Login Gagal')
            ->body('Username atau password salah.')
            ->danger()
            ->send();
    }

    public function render()
    {
        return view('livewire.portal-login');
    }
}
