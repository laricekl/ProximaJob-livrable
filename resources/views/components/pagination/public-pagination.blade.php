@if ($paginator->hasPages())
  <nav aria-label="Pagination des offres" class="flex flex-col gap-4 rounded-2xl border border-primary/5 bg-white px-5 py-4 shadow-[0_10px_30px_rgba(0,0,0,0.03)] sm:flex-row sm:items-center sm:justify-between">
    <p class="text-sm text-on-surface-variant">
      Affichage de <span class="font-semibold text-primary">{{ $paginator->firstItem() }}</span>
      a <span class="font-semibold text-primary">{{ $paginator->lastItem() }}</span>
      sur <span class="font-semibold text-primary">{{ $paginator->total() }}</span> offres
    </p>

    <div class="flex flex-wrap items-center justify-center gap-2 sm:justify-end">
      @if ($paginator->onFirstPage())
        <span data-pagination-control class="inline-flex h-10 items-center rounded-xl border border-outline-variant/20 px-4 text-sm font-semibold text-on-surface-variant/40">
          Precedent
        </span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}" data-pagination-control class="inline-flex h-10 items-center rounded-xl border border-outline-variant/20 px-4 text-sm font-semibold text-on-surface-variant transition hover:border-secondary-container/40 hover:text-secondary-container">
          Precedent
        </a>
      @endif

      @foreach ($elements as $element)
        @if (is_string($element))
          <span data-pagination-control class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl border border-outline-variant/20 px-3 text-sm font-semibold text-on-surface-variant/50">
            {{ $element }}
          </span>
        @endif

        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span data-pagination-control aria-current="page" class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl bg-secondary-container px-3 text-sm font-bold text-white shadow-[0_8px_20px_rgba(234,88,12,0.25)]">
                {{ $page }}
              </span>
            @else
              <a href="{{ $url }}" data-pagination-control class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl border border-outline-variant/20 px-3 text-sm font-semibold text-on-surface-variant transition hover:border-secondary-container/40 hover:text-secondary-container">
                {{ $page }}
              </a>
            @endif
          @endforeach
        @endif
      @endforeach

      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" data-pagination-control class="inline-flex h-10 items-center rounded-xl border border-outline-variant/20 px-4 text-sm font-semibold text-on-surface-variant transition hover:border-secondary-container/40 hover:text-secondary-container">
          Suivant
        </a>
      @else
        <span data-pagination-control class="inline-flex h-10 items-center rounded-xl border border-outline-variant/20 px-4 text-sm font-semibold text-on-surface-variant/40">
          Suivant
        </span>
      @endif
    </div>
  </nav>
@endif
