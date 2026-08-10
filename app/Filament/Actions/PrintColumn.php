<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PrintColumn
{
    private string|Closure|null $label = null;

    private ?Closure $formatStateUsing = null;

    final public function __construct(private string $name) {}

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function label(string|Closure $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function formatStateUsing(?Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function getLabel(): string
    {
        $label = value($this->label);

        return $label ?? (string) str($this->name)->afterLast('.')->headline();
    }

    public function getState(mixed $record): string
    {
        $state = data_get($record, $this->name);

        if ($this->formatStateUsing !== null) {
            $state = ($this->formatStateUsing)($state, $record);
        }

        return match (true) {
            $state instanceof Htmlable => trim(strip_tags($state->toHtml())),
            $state instanceof Carbon => $state->toDateTimeString(),
            $state instanceof Collection => $state->implode(', '),
            is_array($state) => implode(', ', array_map(
                fn (mixed $value): string => is_scalar($value) ? (string) $value : (string) json_encode($value),
                $state,
            )),
            is_bool($state) => $state ? __('table-output.values.yes') : __('table-output.values.no'),
            $state === null => '',
            is_scalar($state) => (string) $state,
            default => (string) json_encode($state),
        };
    }
}
