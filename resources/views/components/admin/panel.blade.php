@props([
  'title' => null,
  'padding' => 'p-6',
  'headerClass' => '',
  'bodyClass' => '',
])

<div {{ $attributes->merge(['class' => 'card-glow overflow-hidden rounded-2xl']) }}>
  @if ($title)
    <div class="border-b border-outline-variant/10 px-6 py-4 {{ $headerClass }}">
      <h3 class="font-bold font-serif text-primary">{{ $title }}</h3>
    </div>
  @endif

  <div class="{{ $padding }} {{ $bodyClass }}">
    {{ $slot }}
  </div>
</div>
