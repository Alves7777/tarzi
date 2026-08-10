<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;
use RuntimeException;

class PrintTableAction extends Action
{
    /**
     * @var array<PrintColumn>|Closure
     */
    private array|Closure $printColumns = [];

    private string|Closure|null $printTitle = null;

    public static function getDefaultName(): ?string
    {
        return 'print';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('table-output.actions.print'))
            ->icon('heroicon-o-printer')
            ->color('gray')
            ->action(function (): void {
                $livewire = $this->getLivewire();

                if (! $livewire instanceof HasTable) {
                    throw new RuntimeException('The print action must be attached to a Filament table.');
                }

                $query = $livewire->getFilteredSortedTableQuery();

                if ($query === null) {
                    throw new RuntimeException('The table does not expose an Eloquent query for printing.');
                }

                $html = $this->printView(
                    title: $this->getPrintTitle(),
                    columns: $this->getPrintColumns(),
                    records: $query->cursor(),
                )->render();

                $livewire->dispatch('print-table', html: $html);
            });
    }

    /**
     * @param  array<PrintColumn>|Closure  $columns
     */
    public function columns(array|Closure $columns): static
    {
        $this->printColumns = $columns;

        return $this;
    }

    public function title(string|Closure $title): static
    {
        $this->printTitle = $title;

        return $this;
    }

    /**
     * @return array<PrintColumn>
     */
    public function getPrintColumns(): array
    {
        return $this->evaluate($this->printColumns);
    }

    public function getPrintTitle(): string
    {
        return (string) $this->evaluate($this->printTitle);
    }

    /**
     * @param  array<PrintColumn>  $columns
     * @param  iterable<mixed>  $records
     */
    private function printView(string $title, array $columns, iterable $records): View
    {
        return view('filament.actions.print-table', [
            'title' => $title,
            'columns' => $columns,
            'records' => $records,
        ]);
    }
}
