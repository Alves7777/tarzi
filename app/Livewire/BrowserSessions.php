<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Session;
use App\Models\User;
use App\Support\SessionRegistry;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * The signed-in user's own devices, shown inside the profile page.
 */
class BrowserSessions extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    /**
     * @return Collection<int, Session>
     */
    #[Computed]
    public function sessions(): Collection
    {
        return SessionRegistry::forUser($this->user());
    }

    public function revokeAction(): Action
    {
        return Action::make('revoke')
            ->label(__('sessions.actions.revoke'))
            ->icon(Heroicon::ArrowRightStartOnRectangle)
            ->color('danger')
            ->link()
            ->requiresConfirmation()
            ->modalHeading(__('sessions.actions.revoke_confirm_heading'))
            ->modalDescription(__('sessions.actions.revoke_confirm_description'))
            ->action(function (array $arguments): void {
                $session = Session::query()
                    ->forUser($this->user())
                    ->whereKey($arguments['session'] ?? null)
                    ->first();

                if ($session === null || $session->isCurrent()) {
                    return;
                }

                SessionRegistry::revoke($session);

                unset($this->sessions);

                Notification::make()
                    ->title(__('sessions.notifications.revoked'))
                    ->success()
                    ->send();
            });
    }

    public function revokeOthersAction(): Action
    {
        return Action::make('revokeOthers')
            ->label(__('sessions.actions.revoke_others'))
            ->icon(Heroicon::ShieldExclamation)
            ->color('danger')
            ->outlined()
            ->requiresConfirmation()
            ->modalHeading(__('sessions.actions.revoke_others_confirm_heading'))
            ->modalDescription(__('sessions.actions.revoke_others_confirm_description'))
            ->hidden(fn (): bool => $this->sessions->count() < 2)
            ->action(function (): void {
                $revoked = SessionRegistry::revokeOthersFor($this->user());

                unset($this->sessions);

                Notification::make()
                    ->title(trans_choice('sessions.notifications.revoked_others', $revoked, ['count' => $revoked]))
                    ->success()
                    ->send();
            });
    }

    public function render(): View
    {
        return view('livewire.browser-sessions');
    }

    private function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
