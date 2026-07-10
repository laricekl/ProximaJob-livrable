@props([
  'route',
  'placeholder' => 'Rechercher...',
  'search' => '',
  'resetRoute' => null,
])

<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
  <form method="GET" action="{{ $route }}" class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
    <div class="relative flex-1 sm:max-w-sm">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
      <input
        type="text"
        name="search"
        value="{{ $search }}"
        class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 pl-10 pr-4 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30"
        placeholder="{{ $placeholder }}"
      />
    </div>
    <button type="submit" class="rounded-xl bg-secondary-container px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-secondary">Filtrer</button>
    @if ($search)
      <a href="{{ $resetRoute ?? $route }}" class="rounded-xl border border-outline-variant/20 px-4 py-2.5 text-sm font-semibold transition-colors hover:bg-surface-container-low">Réinitialiser</a>
    @endif
  </form>
  {{ $slot }}
</div>
