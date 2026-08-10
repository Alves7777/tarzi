<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Livewire\BrowserSessions;
use App\Support\SessionRegistry;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;

/**
 * The panel profile page, with the user's own browser sessions appended.
 *
 * Everything above the sessions section — the identity form, the password
 * change and the multi-factor options — comes from Filament itself.
 */
class EditProfile extends BaseEditProfile
{
    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
                ...Arr::wrap($this->getMultiFactorAuthenticationContentComponent()),
                ...Arr::wrap($this->getBrowserSessionsContentComponent()),
            ]);
    }

    public function getBrowserSessionsContentComponent(): ?Component
    {
        if (! SessionRegistry::isSupported()) {
            return null;
        }

        return Section::make()
            ->label(__('sessions.profile.heading'))
            ->description(__('sessions.profile.description'))
            ->compact()
            ->secondary()
            ->schema([
                Livewire::make(BrowserSessions::class),
            ]);
    }
}
