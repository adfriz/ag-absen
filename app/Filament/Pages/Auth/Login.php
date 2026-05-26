<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected static string $layout = 'filament-panels::components.layout.base';
    protected static string $view = 'filament.pages.auth.login';

    public $forgotUsername;
    public $resetSuccess = false;
    public bool $forgotOpen = false;

    public function openForgotModal()
    {
        $this->resetResetState();
        $this->forgotOpen = true;
    }

    public function closeForgotModal()
    {
        $this->forgotOpen = false;
    }

    public function requestPasswordReset()
    {
        $this->validate([
            'forgotUsername' => 'required|exists:users,username',
        ], [
            'forgotUsername.required' => 'Username / NIP wajib diisi.',
            'forgotUsername.exists' => 'Username / NIP tidak terdaftar.',
        ]);

        $user = User::query()->where('username', $this->forgotUsername)->first();
        if ($user) {
            $user->update(['needs_password_reset' => true]);

            // Send notification to admins
            $admins = User::query()->where('role', 'admin')->get();
            foreach ($admins as $admin) {
                \Filament\Notifications\Notification::make()
                    ->title('Permintaan Reset Password')
                    ->body("Guru {$user->name} meminta reset password.")
                    ->icon('heroicon-o-key')
                    ->iconColor('danger')
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->button()
                            ->label('Lihat Permintaan')
                            ->url('/dashboard'),
                    ])
                    ->sendToDatabase($admin);
            }

            $this->resetSuccess = true;
            $this->forgotUsername = null;
        }
    }

    public function resetResetState()
    {
        $this->resetSuccess = false;
        $this->forgotUsername = null;
        $this->resetErrorBag();
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                $this->getUsernameFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getUsernameFormComponent(): \Filament\Forms\Components\Component
    {
        return \Filament\Forms\Components\TextInput::make('username')
            ->label('Username / NIP')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    protected function getAuthenticateFormAction(): \Filament\Actions\Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Masuk');
    }


    public function render(): \Illuminate\Contracts\View\View
    {
        /** @var mixed $view */
        $view = view($this->getView(), $this->getViewData());

        return $view->layout('filament-panels::components.layout.base', [
            'livewire' => $this,
            'maxContentWidth' => $this->getMaxContentWidth(),
            ...$this->getLayoutData(),
        ]);
    }
}
