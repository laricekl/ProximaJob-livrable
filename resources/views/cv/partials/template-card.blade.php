@php
  $previewStyles = [
    'modern' => ['bg-[#2f5f8f]', 'grid-cols-[34%_66%]'],
    'classic' => ['bg-[#fffdf9]', 'grid-cols-1'],
    'executive' => ['bg-[#1f2937]', 'grid-cols-2'],
    'minimal' => ['bg-white', 'grid-cols-1'],
    'technical' => ['bg-[#17242e]', 'grid-cols-[42%_58%]'],
    'creative' => ['bg-[#8a334b]', 'grid-cols-[25%_75%]'],
    'signature' => ['bg-[#f4efe9]', 'grid-cols-[30%_70%]'],
    'portfolio' => ['bg-[#1d2830]', 'grid-cols-[38%_62%]'],
  ];
  [$previewBg, $previewGrid] = $previewStyles[$templateKey];
@endphp
<div data-template-card data-category="{{ $template['category'] }}" data-ats="{{ $template['ats'] }}">
  <input type="radio" name="template_style" id="{{ $inputIdPrefix ?? 'template' }}-{{ $templateKey }}" value="{{ $templateKey }}" class="peer sr-only" data-supports-photo="{{ $template['supports_photo'] ? '1' : '0' }}" @checked(old('template_style', $selectedTemplate ?? 'modern') === $templateKey) />
  <label for="{{ $inputIdPrefix ?? 'template' }}-{{ $templateKey }}" class="block h-full cursor-pointer rounded-2xl border-2 border-outline-variant/20 bg-white p-3 transition-all hover:-translate-y-0.5 hover:border-secondary-container/40 peer-checked:border-secondary-container peer-checked:ring-4 peer-checked:ring-secondary-container/10">
    <span class="mb-3 block aspect-[4/3] overflow-hidden rounded-xl border border-outline-variant/20 bg-white shadow-sm">
      <span class="grid h-full {{ $previewGrid }}">
        <span class="block {{ $previewBg }} p-3">
          @if($template['supports_photo'])
            <span class="mx-auto mb-2 block h-9 w-9 rounded-full border-2 border-white/70 bg-white/40"></span>
          @endif
          <span class="block h-2 w-4/5 rounded bg-white/80"></span>
          <span class="mt-2 block h-1 w-full rounded bg-white/50"></span>
          <span class="mt-1 block h-1 w-3/4 rounded bg-white/50"></span>
        </span>
        @if($previewGrid !== 'grid-cols-1')
          <span class="block p-3">
            <span class="block h-3 w-4/5 rounded bg-primary"></span>
            <span class="mt-1 block h-1.5 w-1/2 rounded bg-secondary-container"></span>
            <span class="mt-4 block h-1 w-full rounded bg-outline-variant"></span>
            <span class="mt-1 block h-1 w-5/6 rounded bg-outline-variant"></span>
            <span class="mt-3 block h-1 w-full rounded bg-outline-variant"></span>
          </span>
        @else
          <span class="-mt-12 block p-3 pt-14">
            <span class="block h-px w-full bg-outline-variant"></span>
            <span class="mt-3 block h-1 w-full rounded bg-outline-variant"></span>
            <span class="mt-2 block h-1 w-4/5 rounded bg-outline-variant"></span>
          </span>
        @endif
      </span>
    </span>
    <span class="flex items-center justify-between gap-2">
      <span class="text-sm font-bold text-primary">{{ $template['label'] }}</span>
      <span class="rounded-full bg-secondary-container/10 px-2 py-0.5 text-2xs font-bold text-secondary-container">{{ $template['badge'] }}</span>
    </span>
    <span class="mt-1 block text-xs leading-5 text-on-surface-variant">{{ $template['description'] }}</span>
  </label>
</div>
