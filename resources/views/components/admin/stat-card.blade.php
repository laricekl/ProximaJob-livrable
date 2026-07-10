@props([
  'label',
  'value',
  'hint' => null,
])

<div {{ $attributes->merge(['class' => 'card-glow rounded-2xl p-5']) }}>
  <p class="text-xs font-bold uppercase tracking-widest text-outline">{{ $label }}</p>
  <p class="mt-2 text-3xl font-bold text-primary">{{ $value }}</p>
  @if ($hint)
    <p class="mt-1 text-xs font-semibold text-success">{{ $hint }}</p>
  @endif
</div>
