@props([
  'label',
  'name' => null,
  'required' => false,
])

<div {{ $attributes->merge() }}>
  <label @if($name) for="{{ $name }}" @endif class="mb-1.5 block text-sm font-semibold text-primary">
    {{ $label }}
    @if ($required)
      <span class="text-secondary-container">*</span>
    @endif
  </label>
  {{ $slot }}
</div>
