@php
  $currentLocale = app()->getLocale();
  $locales = [
    'fr' => 'FR',
    'en' => 'EN',
  ];
@endphp

<div class="language-switcher inline-flex items-center rounded-full border border-outline-variant/30 bg-white/85 p-1 shadow-sm backdrop-blur" data-language-switcher>
  @foreach ($locales as $locale => $label)
    <button
      type="button"
      class="language-switcher__option rounded-full px-3 py-1.5 text-xs font-black tracking-wider transition-all {{ $currentLocale === $locale ? 'bg-secondary-container text-white shadow-sm' : 'text-on-surface-variant hover:bg-surface-container hover:text-primary' }}"
      data-locale="{{ $locale }}"
      aria-pressed="{{ $currentLocale === $locale ? 'true' : 'false' }}"
    >
      {{ $label }}
    </button>
  @endforeach
</div>

@once
  <script>
    document.addEventListener('click', async (event) => {
      const button = event.target.closest('[data-language-switcher] [data-locale]');
      if (!button || button.getAttribute('aria-pressed') === 'true') return;

      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      button.disabled = true;

      try {
        const response = await fetch('{{ route('set.language') }}', {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...(token ? { 'X-CSRF-TOKEN': token } : {}),
          },
          body: JSON.stringify({ locale: button.dataset.locale }),
        });

        if (!response.ok) throw new Error('Language switch failed');
        window.location.reload();
      } catch (error) {
        button.disabled = false;
        console.error(error);
      }
    });
  </script>
@endonce
