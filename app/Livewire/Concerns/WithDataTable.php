<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Enums\ExportFormat;
use App\Support\TableExporter;
use App\Support\TableFilter;
use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Search, sort, filter, pagination, and export for a table of records.
 *
 * Every public property here can be set by the client, so each one is checked
 * against what the table declares before it reaches a query.
 */
trait WithDataTable
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $sort = '';

    #[Url(except: 'asc')]
    public string $direction = 'asc';

    #[Url(except: '10')]
    public string $perPage = '10';

    /** @var array<string, mixed> */
    #[Url(except: [])]
    public array $filters = [];

    /**
     * The owner-scoped query behind the table.
     *
     * @return Builder<*>
     */
    abstract protected function query(): Builder;

    /**
     * @return list<string>
     */
    abstract protected function searchColumns(): array;

    /**
     * @return list<string>
     */
    abstract protected function sortColumns(): array;

    abstract protected function defaultSort(): string;

    protected function defaultDirection(): string
    {
        return 'asc';
    }

    /**
     * @return array<string, Closure(mixed): (string|int|float|bool|null)>
     */
    abstract protected function exportColumns(): array;

    abstract protected function exportName(): string;

    /**
     * @return array<string, TableFilter>
     */
    protected function tableFilters(): array
    {
        return [];
    }

    public function sortBy(string $column): void
    {
        if (! in_array($column, $this->sortColumns(), true)) {
            return;
        }

        $this->direction = $this->activeSort() === $column && $this->activeDirection() === 'asc' ? 'desc' : 'asc';
        $this->sort = $column;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilters(): void
    {
        $this->filters = array_filter($this->filters, fn (mixed $value): bool => $value !== '');
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function export(string $format): StreamedResponse
    {
        return app(TableExporter::class)->download(
            $this->filteredQuery(),
            $this->exportColumns(),
            $this->exportName(),
            ExportFormat::tryFrom($format) ?? abort(404),
        );
    }

    /**
     * The values a table's view needs. Livewire gives public properties priority
     * over view data, so the checked sort values need names of their own.
     *
     * @return array<string, mixed>
     */
    protected function tableData(): array
    {
        return [
            'rows' => $this->rows(),
            'activeSort' => $this->activeSort(),
            'activeDirection' => $this->activeDirection(),
            'tableFilters' => $this->tableFilters(),
            'perPageOptions' => self::perPageOptions(),
        ];
    }

    /**
     * @return LengthAwarePaginator<int, *>
     */
    private function rows(): LengthAwarePaginator
    {
        $query = $this->filteredQuery();

        return $query
            ->orderBy($this->activeSort(), $this->activeDirection())
            ->orderBy($query->getModel()->getQualifiedKeyName())
            ->paginate($this->activePerPage());
    }

    /**
     * @return Builder<*>
     */
    private function filteredQuery(): Builder
    {
        $query = $this->query();
        $term = trim($this->search);

        if ($term !== '') {
            $query->where(function (Builder $group) use ($term): void {
                $grammar = $group->getQuery()->getGrammar();
                $pattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $term).'%';

                foreach ($this->searchColumns() as $column) {
                    $group->orWhereRaw($grammar->wrap($column)." LIKE ? ESCAPE '!'", [$pattern]);
                }
            });
        }

        foreach ($this->tableFilters() as $key => $filter) {
            $filter->apply($query, $this->filters[$key] ?? null);
        }

        return $query;
    }

    private function activeSort(): string
    {
        return in_array($this->sort, $this->sortColumns(), true) ? $this->sort : $this->defaultSort();
    }

    private function activeDirection(): string
    {
        if ($this->sort === '' || ! in_array($this->sort, $this->sortColumns(), true)) {
            return $this->defaultDirection();
        }

        return $this->direction === 'desc' ? 'desc' : 'asc';
    }

    private function activePerPage(): int
    {
        return in_array((int) $this->perPage, self::perPageOptions(), true) ? (int) $this->perPage : 10;
    }

    /**
     * @return list<int>
     */
    private static function perPageOptions(): array
    {
        return [10, 25, 50, 100];
    }
}
