@php
    use App\Support\LoginAppearance;

    $layout = LoginAppearance::layout();
@endphp

{{--
    Side panel of the split login screen. The illustration is drawn with the
    panel's own colours (white over --primary-600), so it always follows the
    colour chosen in the settings page instead of carrying one of its own.
--}}
<aside @class(['filogin-aside', 'filogin-aside-start' => $layout === 'right'])>
    <div class="filogin-aside-inner">
        <svg class="filogin-illustration" viewBox="0 0 480 360" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle cx="400" cy="70" r="90" fill="#fff" fill-opacity="0.08" />
            <circle cx="70" cy="300" r="60" fill="#fff" fill-opacity="0.06" />
            <circle cx="96" cy="88" r="8" fill="#fff" fill-opacity="0.35" />
            <circle cx="404" cy="316" r="6" fill="#fff" fill-opacity="0.3" />

            <rect x="110" y="60" width="260" height="240" rx="20" fill="#fff" fill-opacity="0.12" />
            <rect x="110.5" y="60.5" width="259" height="239" rx="19.5" stroke="#fff" stroke-opacity="0.25" />

            <circle cx="134" cy="82" r="5" fill="#fff" fill-opacity="0.45" />
            <circle cx="152" cy="82" r="5" fill="#fff" fill-opacity="0.3" />
            <circle cx="170" cy="82" r="5" fill="#fff" fill-opacity="0.3" />
            <rect x="110" y="103" width="260" height="1" fill="#fff" fill-opacity="0.2" />

            <circle cx="240" cy="150" r="28" fill="#fff" fill-opacity="0.92" />
            <circle cx="240" cy="143" r="9" fill="var(--primary-600)" />
            <path d="M226 168a14 14 0 0 1 28 0z" fill="var(--primary-600)" />

            <rect x="160" y="196" width="160" height="18" rx="9" fill="#fff" fill-opacity="0.25" />
            <rect x="160" y="222" width="160" height="18" rx="9" fill="#fff" fill-opacity="0.25" />
            <rect x="160" y="256" width="160" height="22" rx="11" fill="#fff" fill-opacity="0.9" />

            <circle cx="366" cy="266" r="28" fill="#fff" fill-opacity="0.95" />
            <rect x="355" y="262" width="22" height="17" rx="4" fill="var(--primary-600)" />
            <path d="M359 262v-5a7 7 0 0 1 14 0v5" stroke="var(--primary-600)" stroke-width="3" stroke-linecap="round" />
        </svg>

        <div class="filogin-aside-copy">
            <p class="filogin-aside-heading">{{ __('login-screen.panel.heading') }}</p>
            <p class="filogin-aside-subheading">{{ __('login-screen.panel.subheading') }}</p>
        </div>
    </div>
</aside>
