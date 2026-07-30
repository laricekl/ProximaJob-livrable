@php
    $options = array_merge([
        'template_style' => 'modern',
        'include_photo' => false,
        'accent_color' => 'blue',
        'font_style' => 'sober',
        'density' => 'balanced',
        'section_order' => 'skills_first',
        'page_limit' => 2,
        'sections' => ['software', 'languages', 'perfectionnements', 'benevolats'],
    ], $cv_options ?? []);

    $limits = array_merge([
        'experiences' => 5,
        'formations' => 4,
        'competences' => 8,
        'perfectionnements' => 4,
        'langues' => 5,
        'benevolats' => 3,
    ], $cv_limits ?? []);

    $accent = [
        'blue' => '#2f5f8f',
        'green' => '#2f7d5c',
        'bordeaux' => '#8a334b',
        'anthracite' => '#343a40',
        'petrol' => '#28666e',
    ][$options['accent_color']] ?? '#2f5f8f';

    $fontFamily = [
        'sober' => 'Arial, sans-serif',
        'modern' => 'DejaVu Sans, Arial, sans-serif',
        'classic' => 'DejaVu Serif, Georgia, serif',
    ][$options['font_style']] ?? 'Arial, sans-serif';

    $density = [
        'airy' => ['font' => '10.4pt', 'line' => '1.48', 'section' => '18px', 'item' => '12px', 'margin' => '15mm'],
        'balanced' => ['font' => '9.8pt', 'line' => '1.36', 'section' => '14px', 'item' => '9px', 'margin' => '13mm'],
        'compact' => ['font' => '9.2pt', 'line' => '1.25', 'section' => '10px', 'item' => '7px', 'margin' => '11mm'],
    ][$options['density']] ?? ['font' => '9.8pt', 'line' => '1.36', 'section' => '14px', 'item' => '9px', 'margin' => '13mm'];

    $limitText = fn ($text, $limit = 360) => \Illuminate\Support\Str::limit(trim((string) $text), $limit, '...');
    $visibleSections = collect($options['sections'] ?? []);
    $experiences = collect($experiences ?? [])->take((int) $limits['experiences']);
    $formations = collect($formations ?? [])->take((int) $limits['formations']);
    $competencesList = collect($competences ?? [])->take((int) $limits['competences']);
    $perfectionnements = $visibleSections->contains('perfectionnements')
        ? collect($perfectionnements ?? [])->take((int) $limits['perfectionnements'])
        : collect();
    $langues = $visibleSections->contains('languages')
        ? collect($langues ?? [])->take((int) $limits['langues'])
        : collect();
    $benevolats = $visibleSections->contains('benevolats')
        ? collect($benevolats ?? [])->take((int) $limits['benevolats'])
        : collect();
    $logiciels = $visibleSections->contains('software') ? $limitText($logiciels ?? '', 260) : '';
    $professionalSummary = $limitText($langues_competences ?? '', 520);
    $fullName = trim(($prenom ?? '').' '.($nom ?? '')) ?: 'Candidat ProximaJob';
    $location = collect([$ville ?? null, $province ?? null])->filter()->implode(', ');
    $targetTitle = trim((string) ($offer_title ?? ''));
    $templateStyle = array_key_exists($options['template_style'], config('cv_templates', []))
        ? $options['template_style'] : 'modern';
    $template = config('cv_templates.'.$templateStyle, config('cv_templates.modern'));
    $includePhoto = (bool) $options['include_photo']
        && (bool) ($template['supports_photo'] ?? false)
        && !empty($photo_data);
    $initials = mb_strtoupper(mb_substr($prenom ?? '', 0, 1).mb_substr($nom ?? '', 0, 1));
@endphp

@include($template['view'])
