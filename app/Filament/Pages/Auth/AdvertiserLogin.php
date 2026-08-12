<?php

namespace App\Filament\Pages\Auth;

use App\Filament\Advertiser\Resources\Advertisements\AdvertisementResource;
use App\Models\User;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;

class AdvertiserLogin extends BaseLogin
{
    public function mount(): void
    {
        $user = Filament::auth()->user();

        if ($user instanceof User && Filament::auth()->check()) {
            if ($user->canAccessPanel(Filament::getCurrentOrDefaultPanel())) {
                redirect()->intended(
                    AdvertisementResource::getUrl('index', panel: 'advertiser')
                );

                return;
            }

            // Evita loop quando admin (ou outra conta) tenta abrir /anunciante logado.
            Filament::auth()->logout();
            session()->invalidate();
            session()->regenerateToken();
        }

        $this->form->fill();
    }
}
