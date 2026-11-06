<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * A private reference page for the design system. It is only reachable in the
 * local and testing environments.
 */
#[Title('Style Reference')]
class Styleguide extends Component
{
    use WithPagination;

    private const int ROW_COUNT = 42;

    private const int PER_PAGE = 5;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string')]
    public string $description = '';

    #[Validate('required|in:1,2,3,4')]
    public string $priority = '';

    public bool $active = true;

    public string $sort = 'name';

    public string $direction = 'asc';

    public function mount(): void
    {
        abort_unless(app()->environment('local', 'testing'), 404);
    }

    public function save(): void
    {
        $this->validate();

        $this->reset('name', 'email', 'description', 'priority');
        $this->dispatch('close-dialog', name: 'sample-form');
        $this->toast('success', 'Sample saved.');
    }

    public function confirmDelete(): void
    {
        $this->toast('error', 'Sample deleted.');
    }

    public function toast(string $type, string $message): void
    {
        $this->dispatch('toast', type: $type, message: $message);
    }

    public function sortBy(string $field): void
    {
        $this->direction = $this->sort === $field && $this->direction === 'asc' ? 'desc' : 'asc';
        $this->sort = $field;
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.styleguide', ['rows' => $this->rows()]);
    }

    /**
     * @return LengthAwarePaginator<int, array{id: int, name: string, status: string}>
     */
    private function rows(): LengthAwarePaginator
    {
        $rows = collect(range(1, self::ROW_COUNT))
            ->map(fn (int $id): array => [
                'id' => $id,
                'name' => 'Sample record '.str_pad((string) $id, 2, '0', STR_PAD_LEFT),
                'status' => $id % 3 === 0 ? 'Resolved' : 'Open',
            ])
            ->sortBy($this->sort, descending: $this->direction === 'desc')
            ->values();

        $page = $this->getPage();

        return new LengthAwarePaginator(
            $rows->forPage($page, self::PER_PAGE)->values(),
            self::ROW_COUNT,
            self::PER_PAGE,
            $page,
        );
    }
}
