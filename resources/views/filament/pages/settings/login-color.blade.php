@php
    use App\Support\LoginAppearance;

    $statePath = $getStatePath();
    $selected = $getState();
@endphp

<div class="uisw-swatches">
    @foreach (LoginAppearance::colors() as $name => $hex)
        <button
            type="button"
            class="uisw-swatch"
            style="background-color: {{ $hex }}"
            aria-label="{{ __('ui-switcher.colors.'.$name) }}"
            aria-pressed="{{ $selected === $hex ? 'true' : 'false' }}"
            wire:click="$set('{{ $statePath }}', '{{ $hex }}')"
            wire:loading.attr="disabled"
        >
            @if ($selected === $hex)
                <x-filament::icon icon="heroicon-m-check" class="uisw-swatch-check" />
            @endif
        </button>
    @endforeach
</div>
