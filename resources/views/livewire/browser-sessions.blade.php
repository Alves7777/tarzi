<div class="uisess">
    <div class="uisess-list">
        @foreach ($this->sessions as $session)
            <div class="uisess-row" wire:key="session-{{ $session->getKey() }}">
                <span class="uisess-icon">
                    <x-filament::icon
                        :icon="in_array($session->agent()->platform, ['iOS', 'Android'], true)
                            ? \Filament\Support\Icons\Heroicon::OutlinedDevicePhoneMobile
                            : \Filament\Support\Icons\Heroicon::OutlinedComputerDesktop"
                    />
                </span>

                <div class="uisess-details">
                    <div class="uisess-device">
                        {{ $session->agent()->describe() }}

                        @if ($session->isCurrent())
                            <x-filament::badge color="success" size="xs">
                                {{ __('sessions.current_device') }}
                            </x-filament::badge>
                        @endif
                    </div>

                    <div class="uisess-meta">
                        {{ $session->ip_address ?? __('sessions.unknown_ip') }}

                        &middot;

                        @if ($session->isCurrent())
                            {{ __('sessions.active_now') }}
                        @else
                            {{ __('sessions.last_active', ['time' => $session->lastActiveAt()->diffForHumans()]) }}
                        @endif
                    </div>
                </div>

                @unless ($session->isCurrent())
                    <div class="uisess-actions">
                        {{ ($this->revokeAction)(['session' => $session->getKey()]) }}
                    </div>
                @endunless
            </div>
        @endforeach
    </div>

    <div class="uisess-footer">
        {{ $this->revokeOthersAction }}
    </div>

    <x-filament-actions::modals />
</div>
