@props(['status'])

@php
$labels = [
    'en_attente' => 'En attente',
    'accepted'   => 'Acceptée',
    'rejected'   => 'Refusée',
    'entretien'  => 'Entretien',
    'retenue'    => 'Retenue',
];

$colors = [
    'en_attente' => 'bg-amber-100 text-amber-700',
    'accepted'   => 'bg-green-100 text-green-700',
    'rejected'   => 'bg-red-100 text-red-700',
    'entretien'  => 'bg-blue-100 text-blue-700',
    'retenue'    => 'bg-purple-100 text-purple-700',
];

$label = $labels[$status] ?? ucfirst($status);
$color = $colors[$status] ?? 'bg-slate-100 text-slate-700';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold {$color}"]) }}>
  {{ $label }}
</span>
