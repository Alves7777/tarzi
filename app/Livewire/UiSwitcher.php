<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Support\UiPreferences;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Slide-over that lets the user restyle the Filament panel.
 *
 * Selections stay as a draft until Save persists them. Preferences are applied
 * by `App\Http\Middleware\ApplyUiPreferences` on the next request, so Save asks
 * the browser to reload once everything is stored.
 */
class UiSwitcher extends Component
{
    public string $color = '#6366f1';

    public string $font = 'Inter';

    public int $fontSize = 16;

    public string $layout = 'sidebar';

    public string $density = 'default';

    #[Locked]
    public bool $hasModeSwitcher = false;

    public function mount(bool $hasModeSwitcher = false): void
    {
        $this->hasModeSwitcher = $hasModeSwitcher;

        $this->pullPreferences();
    }

    public function refreshDraft(): void
    {
        $this->pullPreferences();
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function setFont(string $font): void
    {
        $this->font = $font;
    }

    public function setFontSize(int $fontSize): void
    {
        $this->fontSize = $fontSize;
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function setDensity(string $density): void
    {
        $this->density = $density;
    }

    /**
     * Restore the draft to the configured defaults. Nothing is persisted until Save.
     */
    public function resetPreferences(): void
    {
        $this->color = (string) UiPreferences::default(UiPreferences::COLOR);
        $this->font = (string) UiPreferences::default(UiPreferences::FONT);
        $this->fontSize = (int) UiPreferences::default(UiPreferences::FONT_SIZE);
        $this->layout = (string) UiPreferences::default(UiPreferences::LAYOUT);
        $this->density = (string) UiPreferences::default(UiPreferences::DENSITY);
    }

    public function save(): void
    {
        $previousLayout = (string) UiPreferences::get(UiPreferences::LAYOUT);

        UiPreferences::fill([
            UiPreferences::COLOR => $this->color,
            UiPreferences::FONT => $this->font,
            UiPreferences::FONT_SIZE => $this->fontSize,
            UiPreferences::LAYOUT => $this->layout,
            UiPreferences::DENSITY => $this->density,
        ]);

        $this->pullPreferences();

        $this->dispatch('close-modal', id: 'ui-switcher');
        $this->dispatchApplied(syncsSidebar: $previousLayout !== $this->layout);
    }

    public function cancel(): void
    {
        $this->pullPreferences();

        $this->dispatch('close-modal', id: 'ui-switcher');
    }

    /**
     * @return array<string, string>
     */
    #[Computed]
    public function colors(): array
    {
        return UiPreferences::colors();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function fonts(): array
    {
        return UiPreferences::fonts();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function layouts(): array
    {
        return UiPreferences::layouts();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function densities(): array
    {
        return array_keys(UiPreferences::densities());
    }

    /**
     * @return array{min: int, max: int}
     */
    #[Computed]
    public function fontSizeRange(): array
    {
        return UiPreferences::fontSizeRange();
    }

    #[Computed]
    public function icon(): string
    {
        return (string) config('ui-switcher.icon', 'heroicon-o-cog-6-tooth');
    }

    public function render(): View
    {
        return view('livewire.ui-switcher');
    }

    private function pullPreferences(): void
    {
        $preferences = UiPreferences::all();

        $this->color = $preferences[UiPreferences::COLOR];
        $this->font = $preferences[UiPreferences::FONT];
        $this->fontSize = $preferences[UiPreferences::FONT_SIZE];
        $this->layout = $preferences[UiPreferences::LAYOUT];
        $this->density = $preferences[UiPreferences::DENSITY];
    }

    /**
     * Ask the browser to reload so the middleware can reconfigure the panel.
     *
     * The collapsed state of the sidebar lives in local storage rather than on
     * the server, so a layout change has to carry the state it expects. Other
     * changes leave it alone, otherwise they would undo a manual collapse.
     */
    private function dispatchApplied(bool $syncsSidebar = false): void
    {
        $this->dispatch(
            'ui-switcher-applied',
            isSidebarOpen: $syncsSidebar ? ($this->layout !== 'sidebar-collapsed') : null,
        );
    }
}
