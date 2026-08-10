@php
    use App\Support\UiPreferences;
@endphp

{{--
    Only the values that change per user live here; the switcher's own component
    styles are part of the compiled panel theme.

    Every value below comes from the `ui-switcher` config allow list.
--}}
<style>
    :root {
        @foreach (UiPreferences::cssVariables() as $variable => $value)
            {{ $variable }}: {{ $value }};
        @endforeach
    }

    html.fi {
        font-size: var(--ui-switcher-font-size, 16px);
    }
</style>
