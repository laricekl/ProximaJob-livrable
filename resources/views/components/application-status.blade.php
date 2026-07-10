@props(['status'])

@php
$labels = [
    'en_attente' => 'En attente',
    'accepted'   => 'Acceptée',
    'rejected'   => 'Refusée',
    'entretien'  => 'Entretien',
    'retenue'    => 'Retenue',
];

$icons = [
    'en_attente' => 'hourglass_empty',
    'accepted'   => 'check_circle',
    'rejected'   => 'cancel',
    'entretien'  => 'calendar_today',
    'retenue'    => 'bookmark',
];

$colors = [
    'en_attente' => 'bg-warning-light text-warning-dark',
    'accepted'   => 'bg-success-light text-success-dark',
    'rejected'   => 'bg-error-light text-error-dark',
    'entretien'  => 'bg-info-light text-info-dark',
    'retenue'    => 'bg-surface-container-low text-on-surface-variant',
];

$label = $labels[$status] ?? ucfirst($status);
$icon  = $icons[$status] ?? 'help';
$color = $colors[$status] ?? 'bg-surface-container-low text-on-surface-variant';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-2xs font-bold {$color}"]) }}>
  <span class="material-symbols-outlined text-[14px]">{{ $icon }}</span>
  {{ $label }}
</span>
