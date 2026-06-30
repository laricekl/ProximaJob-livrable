@if ($paginator->hasPages())
  <nav role="navigation" aria-label="Pagination" class="flex flex-wrap items-center justify-end gap-2">
    @if ($paginator->onFirstPage())
      <span class="inline-flex h-10 items-center rounded-xl border border-outline-variant/20 px-4 text-sm font-semibold text-on-surface-variant/40">
        Precedent
      </span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex h-10 items-center rounded-xl border border-outline-variant/20 px-4 text-sm font-semibold text-on-surface-variant transition hover:border-secondary-container/40 hover:text-secondary-container">
        Precedent
      </a>
    @endif

    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl border border-outline-variant/20 px-3 text-sm font-semibold text-on-surface-variant/50">
          {{ $element }}
        </span>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span aria-current="page" class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl bg-secondary-container px-3 text-sm font-bold text-white shadow-[0_8px_20px_rgba(234,88,12,0.25)]">
              {{ $page }}
            </span>
          @else
            <a href="{{ $url }}" class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl border border-outline-variant/20 px-3 text-sm font-semibold text-on-surface-variant transition hover:border-secondary-container/40 hover:text-secondary-container">
              {{ $page }}
            </a>
          @endif
        @endforeach
      @endif
    @endforeach

    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex h-10 items-center rounded-xl border border-outline-variant/20 px-4 text-sm font-semibold text-on-surface-variant transition hover:border-secondary-container/40 hover:text-secondary-container">
        Suivant
      </a>
    @else
      <span class="inline-flex h-10 items-center rounded-xl border border-outline-variant/20 px-4 text-sm font-semibold text-on-surface-variant/40">
        Suivant
      </span>
    @endif
  </nav>
@endif
