<?php

declare(strict_types=1);

namespace App\Models\Concerns;

/**
 * Persists the Filament UI switcher preferences on the model.
 *
 * Only used when `ui-switcher.driver` is set to `database`. The column is cast
 * to an array by the model and written with `forceFill()` so it does not need
 * to be mass assignable.
 */
trait HasUiPreferences
{
    /**
     * @return array<string, mixed>
     */
    public function getUiPreferences(): array
    {
        $preferences = $this->getAttribute($this->getUiPreferencesColumn());

        return is_array($preferences) ? $preferences : [];
    }

    /**
     * @param  array<string, mixed>  $preferences
     */
    public function setUiPreferences(array $preferences): void
    {
        $this->forceFill([$this->getUiPreferencesColumn() => $preferences])->save();
    }

    public function getUiPreferencesColumn(): string
    {
        return (string) config('ui-switcher.database_column', 'ui_preferences');
    }
}
