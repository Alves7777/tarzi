<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;

class AdminLogin extends BaseLogin
{
    public function mount(): void
    {
        $user = Filament::auth()->user();

        if ($user instanceof User && Filament::auth()->check()) {
            if ($user->canAccessPanel(Filament::getCurrentOrDefaultPanel())) {
                redirect()->intended(Filament::getUrl());

                return;
            }

            // Sessão de anunciante (ou outra conta) — evita 403 ao abrir /admin.
            Filament::auth()->logout();
            session()->invalidate();
            session()->regenerateToken();
        }

        $this->form->fill();
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();
        $credentials = $this->getCredentialsFromFormData($data);
        $authProvider = Filament::auth()->getProvider();
        $user = $authProvider->retrieveByCredentials($credentials);

        if (
            $user instanceof User
            && $authProvider->validateCredentials($user, $credentials)
            && $user->isAdvertiser()
            && ! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel())
        ) {
            throw ValidationException::withMessages([
                'data.email' => 'Esta conta é de anunciante. Acesse o portal em /anunciante/login',
            ]);
        }

        return parent::authenticate();
    }
}
