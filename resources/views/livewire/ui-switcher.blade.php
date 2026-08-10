@php
    $busyTargets = 'save,cancel,resetPreferences';
@endphp

<div
    x-data="{
        apply(detail) {
            // The sidebar remembers its collapsed state client side, so a layout
            // change has to move it before the reload that applies the rest.
            if (detail.isSidebarOpen !== null && window.Alpine?.store('sidebar')) {
                detail.isSidebarOpen
                    ? window.Alpine.store('sidebar').open()
                    : window.Alpine.store('sidebar').close()
            }

            setTimeout(() => window.location.reload(), 150)
        },
    }"
    x-on:ui-switcher-applied.window="apply($event.detail)"
    x-on:open-modal.window="if ($event.detail.id === 'ui-switcher') $wire.refreshDraft()"
>
    <x-filament::modal id="ui-switcher" slide-over width="md" sticky-footer>
        <x-slot name="trigger">
            <x-filament::icon-button
                :icon="$this->icon"
                :label="__('ui-switcher.trigger')"
                :tooltip="__('ui-switcher.trigger')"
                color="gray"
            />
        </x-slot>

        <x-slot name="header">
            <div class="uisw-header">
                <x-filament::icon :icon="$this->icon" class="fi-icon" />

                <h2 class="uisw-header-heading">
                    {{ __('ui-switcher.heading') }}
                </h2>

                <button
                    type="button"
                    class="uisw-reset"
                    wire:click="resetPreferences"
                    wire:loading.attr="disabled"
                    wire:target="{{ $busyTargets }}"
                >
                    <x-filament::icon icon="heroicon-m-arrow-path" class="fi-icon" />
                    {{ __('ui-switcher.reset') }}
                </button>
            </div>
        </x-slot>

        <div class="uisw-sections">
            @if ($hasModeSwitcher)
                <section>
                    <h3 class="uisw-section-title">
                        <x-filament::icon icon="heroicon-m-computer-desktop" class="fi-icon" />
                        {{ __('ui-switcher.mode.heading') }}
                    </h3>

                    <x-filament-panels::theme-switcher />
                </section>
            @endif

            @if (filled($this->layouts))
                <section>
                    <h3 class="uisw-section-title">
                        <x-filament::icon icon="heroicon-m-squares-2x2" class="fi-icon" />
                        {{ __('ui-switcher.layout.heading') }}
                    </h3>

                    <div class="uisw-grid">
                        @foreach ($this->layouts as $option)
                            <button
                                type="button"
                                class="uisw-option"
                                aria-pressed="{{ $layout === $option ? 'true' : 'false' }}"
                                wire:click="setLayout('{{ $option }}')"
                                wire:loading.attr="disabled"
                                wire:target="{{ $busyTargets }}"
                            >
                                <span class="uisw-preview">
                                    @if ($option !== 'sidebar-no-topbar')
                                        <span class="uisw-preview-bar"></span>
                                    @endif

                                    <span class="uisw-preview-row">
                                        @if ($option !== 'topbar')
                                            <span
                                                @class([
                                                    'uisw-preview-nav',
                                                    'uisw-preview-nav-narrow' => $option === 'sidebar-collapsed',
                                                ])
                                            ></span>
                                        @endif

                                        <span class="uisw-preview-body"></span>
                                    </span>
                                </span>

                                <span>{{ __("ui-switcher.layout.{$option}") }}</span>
                            </button>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (filled($this->colors))
                <section>
                    <h3 class="uisw-section-title">
                        <x-filament::icon icon="heroicon-m-swatch" class="fi-icon" />
                        {{ __('ui-switcher.color.heading') }}
                    </h3>

                    <div class="uisw-swatches">
                        @foreach ($this->colors as $name => $hex)
                            <button
                                type="button"
                                class="uisw-swatch"
                                style="background-color: {{ $hex }}"
                                aria-label="{{ __('ui-switcher.colors.'.$name) }}"
                                aria-pressed="{{ $color === $hex ? 'true' : 'false' }}"
                                wire:click="setColor('{{ $hex }}')"
                                wire:loading.attr="disabled"
                                wire:target="{{ $busyTargets }}"
                            >
                                @if ($color === $hex)
                                    <x-filament::icon icon="heroicon-m-check" class="uisw-swatch-check" />
                                @endif
                            </button>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (filled($this->fonts))
                <section>
                    <h3 class="uisw-section-title">
                        <x-filament::icon icon="heroicon-m-language" class="fi-icon" />
                        {{ __('ui-switcher.font.heading') }}
                    </h3>

                    <div class="uisw-grid">
                        @foreach ($this->fonts as $option)
                            <button
                                type="button"
                                class="uisw-option"
                                style="font-family: '{{ $option }}', sans-serif"
                                aria-pressed="{{ $font === $option ? 'true' : 'false' }}"
                                wire:click="setFont('{{ $option }}')"
                                wire:loading.attr="disabled"
                                wire:target="{{ $busyTargets }}"
                            >
                                <span class="uisw-option-sample">Aa</span>
                                <span>{{ $option }}</span>
                            </button>
                        @endforeach
                    </div>
                </section>
            @endif

            <section
                x-data="{
                    size: @js($fontSize),
                    min: @js($this->fontSizeRange['min']),
                    max: @js($this->fontSizeRange['max']),
                }"
                x-effect="size = $wire.fontSize"
            >
                <div class="uisw-slider-header">
                    <span>{{ __('ui-switcher.font.size') }}</span>
                    <span class="uisw-slider-value" x-text="`${size}px`"></span>
                </div>

                <div wire:ignore>
                    <input
                        type="range"
                        class="uisw-slider"
                        step="1"
                        :min="min"
                        :max="max"
                        x-model.number="size"
                        x-on:change="$wire.setFontSize(size)"
                        aria-label="{{ __('ui-switcher.font.size') }}"
                    />
                </div>
            </section>

            @if (filled($this->densities))
                <section>
                    <h3 class="uisw-section-title">
                        <x-filament::icon icon="heroicon-m-arrows-up-down" class="fi-icon" />
                        {{ __('ui-switcher.density.heading') }}
                    </h3>

                    <div class="uisw-grid uisw-grid-3">
                        @foreach ($this->densities as $option)
                            <button
                                type="button"
                                class="uisw-option"
                                aria-pressed="{{ $density === $option ? 'true' : 'false' }}"
                                wire:click="setDensity('{{ $option }}')"
                                wire:loading.attr="disabled"
                                wire:target="{{ $busyTargets }}"
                            >
                                {{ __("ui-switcher.density.{$option}") }}
                            </button>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <x-slot name="footer">
            <div class="uisw-footer">
                <x-filament::button
                    color="gray"
                    wire:click="cancel"
                    wire:loading.attr="disabled"
                    wire:target="{{ $busyTargets }}"
                >
                    {{ __('ui-switcher.cancel') }}
                </x-filament::button>

                <x-filament::button
                    wire:click="save"
                    wire:loading.attr="disabled"
                    wire:target="{{ $busyTargets }}"
                >
                    {{ __('ui-switcher.save') }}
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>
</div>
