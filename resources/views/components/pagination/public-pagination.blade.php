@if ($paginator->hasPages())
  @php
    $window = 2; // 2 de chaque cote = 5 pages visibles
    $current = $paginator->currentPage();
    $last = $paginator->lastPage();
    $start = max(1, $current - $window);
    $end = min($last, $current + $window);
    if ($end - $start < $window * 2) {
      $start = max(1, $end - $window * 2);
      $end = min($last, $start + $window * 2);
      $start = max(1, $end - $window * 2);
    }
  @endphp
  <nav aria-label="Pagination des résultats" class="flex w-full max-w-full flex-col items-start justify-between gap-3 overflow-hidden rounded-2xl border border-primary/5 bg-white px-4 py-3 sm:flex-row sm:items-center sm:px-5">
    <p class="text-xs text-on-surface-variant">
      Affichage de <span class="font-semibold text-primary">{{ $paginator->firstItem() }}</span>
      à <span class="font-semibold text-primary">{{ $paginator->lastItem() }}</span>
      sur <span class="font-semibold text-primary">{{ $paginator->total() }}</span> résultats
    </p>

    <div class="flex w-full max-w-full items-center gap-1 overflow-x-auto pb-1 sm:w-auto sm:pb-0">
      {{-- Prev --}}
      @if ($paginator->onFirstPage())
        <span data-pagination-control class="inline-flex h-8 flex-shrink-0 items-center rounded-xl border border-outline-variant/20 px-3 text-xs font-semibold text-on-surface-variant/40">Préc.</span>
      @else
        <a data-pagination-control href="{{ $paginator->previousPageUrl() }}" class="inline-flex h-8 flex-shrink-0 items-center rounded-xl border border-outline-variant/20 px-3 text-xs font-semibold text-on-surface-variant transition hover:border-secondary-container/40 hover:text-secondary-container">Préc.</a>
      @endif

      {{-- First page + ... --}}
      @if ($start > 1)
        <a href="{{ $paginator->url(1) }}" class="inline-flex h-8 min-w-8 items-center justify-center rounded-xl border border-outline-variant/20 px-2 text-xs font-semibold text-on-surface-variant transition hover:border-secondary-container/40 hover:text-secondary-container">1</a>
        @if ($start > 2)
          <span class="inline-flex h-8 min-w-8 items-center justify-center text-xs text-on-surface-variant/50">...</span>
        @endif
      @endif

      {{-- Page numbers --}}
      @for ($page = $start; $page <= $end; $page++)
        @if ($page == $current)
          <span data-pagination-control aria-current="page" class="inline-flex h-8 min-w-8 items-center justify-center rounded-xl bg-secondary-container px-2 text-xs font-bold text-white shadow-lg shadow-secondary-container/25">{{ $page }}</span>
        @else
          <a data-pagination-control href="{{ $paginator->url($page) }}" class="inline-flex h-8 min-w-8 items-center justify-center rounded-xl border border-outline-variant/20 px-2 text-xs font-semibold text-on-surface-variant transition hover:border-secondary-container/40 hover:text-secondary-container">{{ $page }}</a>
        @endif
      @endfor

      {{-- Last page + ... --}}
      @if ($end < $last)
        @if ($end < $last - 1)
          <span class="inline-flex h-8 min-w-8 items-center justify-center text-xs text-on-surface-variant/50">...</span>
        @endif
        <a href="{{ $paginator->url($last) }}" class="inline-flex h-8 min-w-8 items-center justify-center rounded-xl border border-outline-variant/20 px-2 text-xs font-semibold text-on-surface-variant transition hover:border-secondary-container/40 hover:text-secondary-container">{{ $last }}</a>
      @endif

      {{-- Next --}}
      @if ($paginator->hasMorePages())
        <a data-pagination-control href="{{ $paginator->nextPageUrl() }}" class="inline-flex h-8 flex-shrink-0 items-center rounded-xl border border-outline-variant/20 px-3 text-xs font-semibold text-on-surface-variant transition hover:border-secondary-container/40 hover:text-secondary-container">Suiv.</a>
      @else
        <span data-pagination-control class="inline-flex h-8 flex-shrink-0 items-center rounded-xl border border-outline-variant/20 px-3 text-xs font-semibold text-on-surface-variant/40">Suiv.</span>
      @endif
    </div>
  </nav>
@endif
