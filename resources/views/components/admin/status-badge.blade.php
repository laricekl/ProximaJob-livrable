@props([
  'label',
  'color' => 'bg-slate-100 text-slate-700',
])

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2 py-0.5 text-2xs font-bold {$color}"]) }}>
  {{ $label }}
</span>
