<nav class="pagination" aria-label="Pagination">
    <p class="muted">
        @if ($paginator->total() > 0)
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        @else
            No records
        @endif
    </p>

    @if ($paginator->hasPages())
        <div class="pagination__pages">
            <button
                type="button"
                class="pagination__page"
                aria-label="Previous page"
                wire:click="previousPage('{{ $paginator->getPageName() }}')"
                @disabled($paginator->onFirstPage())
            >
                <x-icon name="chevron-left" size="sm" />
            </button>

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination__gap">{{ $element }}</span>
                @else
                    @foreach ($element as $page => $url)
                        <button
                            type="button"
                            class="pagination__page"
                            wire:key="page-{{ $page }}"
                            wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                            aria-label="Page {{ $page }}"
                            @if ($page === $paginator->currentPage()) aria-current="page" @endif
                        >{{ $page }}</button>
                    @endforeach
                @endif
            @endforeach

            <button
                type="button"
                class="pagination__page"
                aria-label="Next page"
                wire:click="nextPage('{{ $paginator->getPageName() }}')"
                @disabled(! $paginator->hasMorePages())
            >
                <x-icon name="chevron-right" size="sm" />
            </button>
        </div>
    @endif
</nav>
