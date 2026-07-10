@props([
  'label',
  'color' => 'bg-surface-container text-on-surface',
])

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2 py-0.5 text-2xs font-bold {$color}"]) }}>
  {{ $label }}
</span>
