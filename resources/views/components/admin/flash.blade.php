@if (session('success'))
  <div {{ $attributes->merge(['class' => 'flex items-center gap-2 rounded-2xl border border-success-light bg-success-light px-4 py-3 text-sm text-success-deep']) }}>
    <span class="material-symbols-outlined text-lg">check_circle</span>
    <span>{{ session('success') }}</span>
  </div>
@endif

@if (session('error'))
  <div {{ $attributes->merge(['class' => 'flex items-center gap-2 rounded-2xl border border-error-light bg-error-light px-4 py-3 text-sm text-error-deep']) }}>
    <span class="material-symbols-outlined text-lg">error</span>
    <span>{{ session('error') }}</span>
  </div>
@endif

@if (session('status'))
  <div {{ $attributes->merge(['class' => 'flex items-center gap-2 rounded-2xl border border-info-light bg-info-light px-4 py-3 text-sm text-info-dark']) }}>
    <span class="material-symbols-outlined text-lg">info</span>
    <span>{{ session('status') }}</span>
  </div>
@endif
