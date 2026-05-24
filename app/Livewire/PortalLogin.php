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
    
    public function mount(): void
    {
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
            request()->session()->regenerate();

            return redirect()->intended('/dashboard');
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
