@php
    use App\Support\LoginAppearance;

    $statePath = $getStatePath();
    $selected = $getState();
@endphp

<div class="uisw-grid uisw-grid-3">
    @foreach (LoginAppearance::layouts() as $option)
        <button
            type="button"
            class="uisw-option"
            aria-pressed="{{ $selected === $option ? 'true' : 'false' }}"
            wire:click="$set('{{ $statePath }}', '{{ $option }}')"
            wire:loading.attr="disabled"
        >
            <span @class(['filogin-preview', "filogin-preview-{$option}"])>
                @if ($option !== 'default')
                    <span class="filogin-preview-panel"></span>
                @endif

                <span class="filogin-preview-form">
                    <span class="filogin-preview-line"></span>
                    <span class="filogin-preview-line"></span>
                    <span class="filogin-preview-line filogin-preview-line-cta"></span>
                </span>
            </span>

            <span>{{ __("settings.login.layout.options.{$option}") }}</span>
        </button>
    @endforeach
</div>
