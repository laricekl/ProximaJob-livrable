<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV - {{ $prenom }} {{ $nom }}</title>
    @php
        $options = array_merge([
            'template_style' => 'modern',
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
            'classic' => 'Georgia, Times, serif',
        ][$options['font_style']] ?? 'Arial, sans-serif';
        $density = [
            'airy' => ['font' => '11pt', 'line' => '1.48', 'section' => '18px', 'item' => '12px', 'margin' => '17mm'],
            'balanced' => ['font' => '10.5pt', 'line' => '1.35', 'section' => '14px', 'item' => '9px', 'margin' => '15mm'],
            'compact' => ['font' => '10pt', 'line' => '1.24', 'section' => '10px', 'item' => '7px', 'margin' => '13mm'],
        ][$options['density']] ?? ['font' => '10.5pt', 'line' => '1.35', 'section' => '14px', 'item' => '9px', 'margin' => '15mm'];
        $styleIsClassic = $options['template_style'] === 'classic';
        $styleIsExecutive = $options['template_style'] === 'executive';
    @endphp
    <style>
        @page {
            margin: {{ $density['margin'] }};
            size: A4;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: {{ $fontFamily }};
            font-size: {{ $density['font'] }};
            line-height: {{ $density['line'] }};
            color: #000;
        }
        
        .cv-container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        .cv-header {
            text-align: center;
            margin-bottom: {{ $styleIsExecutive ? '24px' : '18px' }};
            padding-bottom: {{ $styleIsClassic ? '0' : '10px' }};
            border-bottom: {{ $styleIsClassic ? '0' : '2px solid '.$accent }};
            page-break-inside: avoid;
        }
        
        .cv-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
            color: {{ $styleIsClassic ? '#000' : $accent }};
        }
        
        .cv-contact {
            font-size: 10pt;
            margin: 2px 0;
        }
        
        .personalization-note {
            font-size: 9pt;
            font-style: italic;
            text-align: center;
            margin-bottom: 15px;
            color: #666;
        }
        
        .cv-section {
            margin-bottom: {{ $density['section'] }};
            page-break-inside: avoid;
        }
        
        .cv-section-title {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 8px;
            text-transform: uppercase;
            border-bottom: {{ $styleIsClassic ? '1px solid #000' : '1.5px solid '.$accent }};
            padding-bottom: 2px;
            color: {{ $styleIsClassic ? '#000' : $accent }};
        }
        
        .cv-item {
            display: table;
            width: 100%;
            margin-bottom: {{ $density['item'] }};
            page-break-inside: avoid;
        }
        
        .cv-dates {
            display: table-cell;
            width: 25%;
            font-weight: bold;
            vertical-align: top;
            padding-right: 15px;
        }
        
        .cv-content {
            display: table-cell;
            width: 75%;
            vertical-align: top;
        }
        
        .cv-job-title {
            font-weight: bold;
            margin-bottom: 2px;
            color: {{ $styleIsExecutive ? '#111827' : '#000' }};
        }
        
        .cv-company {
            font-size: 10pt;
            margin-bottom: 4px;
            color: #333;
        }
        
        .cv-competences-list {
            font-size: 10pt;
            line-height: 1.5;
        }
        
        .cv-competence-item {
            margin-bottom: 8px;
            line-height: 1.4;
        }
        
        .cv-skills-section {
            font-size: 10pt;
            margin-bottom: 8px;
            line-height: 1.4;
        }
        
        .cv-skills-section strong {
            font-weight: bold;
        }
        
        .cv-languages {
            font-size: 10pt;
        }
    </style>
</head>
<body>
    @php
        $limitText = fn ($text, $limit = 360) => \Illuminate\Support\Str::limit((string) $text, $limit, '...');
        $visibleSections = collect($options['sections'] ?? []);
        $experiences = collect($experiences ?? [])->take((int) $limits['experiences']);
        $formations = collect($formations ?? [])->take((int) $limits['formations']);
        $competencesList = collect($competences ?? [])->take((int) $limits['competences']);
        $perfectionnements = $visibleSections->contains('perfectionnements') ? collect($perfectionnements ?? [])->take((int) $limits['perfectionnements']) : collect();
        $langues = $visibleSections->contains('languages') ? collect($langues ?? [])->take((int) $limits['langues']) : collect();
        $benevolats = $visibleSections->contains('benevolats') ? collect($benevolats ?? [])->take((int) $limits['benevolats']) : collect();
        $logiciels = $limitText($logiciels ?? '', 260);
        $langues_competences = $limitText($langues_competences ?? '', 420);
    @endphp
    <div class="cv-container">
        <!-- En-tête -->
        <div class="cv-header">
            <div class="cv-name">{{ strtoupper($nom ?? '') }} {{ strtoupper($prenom ?? '') }}</div>
            
            @if(!empty($telephone))
                <div class="cv-contact">{{ $telephone }}</div>
            @endif
            @if(!empty($email))
                <div class="cv-contact">Courriel : {{ $email }}</div>
            @endif
        </div>

        @if($options['section_order'] === 'experience_first')
            @include('cv.partials.personalized-experiences', ['experiences' => $experiences])
            @include('cv.partials.personalized-skills', ['langues_competences' => $langues_competences, 'logiciels' => $logiciels, 'competencesList' => $competencesList, 'visibleSections' => $visibleSections, 'limitText' => $limitText])
        @else
            @include('cv.partials.personalized-skills', ['langues_competences' => $langues_competences, 'logiciels' => $logiciels, 'competencesList' => $competencesList, 'visibleSections' => $visibleSections, 'limitText' => $limitText])
            @include('cv.partials.personalized-experiences', ['experiences' => $experiences])
        @endif

        <!-- Formation -->
        @if($formations->isNotEmpty())
            <div class="cv-section">
                <div class="cv-section-title">FORMATION</div>
                @foreach($formations as $formation)
                    @if(!empty($formation['diplome']))
                        <div class="cv-item">
                            <div class="cv-dates">{{ $formation['periode'] ?? '' }}</div>
                            <div class="cv-content">
                                <div class="cv-job-title">{{ $formation['diplome'] }}</div>
                                @if(!empty($formation['etablissement']))
                                    <div class="cv-company">{{ $formation['etablissement'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Perfectionnement -->
        @if($perfectionnements->isNotEmpty())
            <div class="cv-section">
                <div class="cv-section-title">PERFECTIONNEMENT</div>
                @foreach($perfectionnements as $perfectionnement)
                    @if(!empty($perfectionnement['formation']))
                        <div class="cv-item">
                            <div class="cv-dates">{{ $perfectionnement['annee'] ?? '' }}</div>
                            <div class="cv-content">
                                <div class="cv-job-title">{{ $perfectionnement['formation'] }}</div>
                                @if(!empty($perfectionnement['etablissement']))
                                    <div class="cv-company">{{ $perfectionnement['etablissement'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Langues -->
        @if($langues->isNotEmpty())
            <div class="cv-section">
                <div class="cv-section-title">LANGUES</div>
                <div class="cv-languages">
                    @php
                        $languesList = [];
                        foreach($langues as $langue) {
                            if(!empty($langue['nom'])) {
                                $languesList[] = $langue['nom'] . (!empty($langue['niveau']) ? ' : ' . $langue['niveau'] : '');
                            }
                        }
                    @endphp
                    {{ implode(', ', $languesList) }}
                </div>
            </div>
        @endif

        <!-- Activités bénévoles -->
        @if($benevolats->isNotEmpty())
            <div class="cv-section">
                <div class="cv-section-title">ACTIVITÉS BÉNÉVOLES</div>
                @foreach($benevolats as $benevolat)
                    @if(!empty($benevolat['role']))
                        <div class="cv-item">
                            <div class="cv-dates">{{ $benevolat['periode'] ?? '' }}</div>
                            <div class="cv-content">
                                <div class="cv-job-title">{{ $benevolat['role'] }}</div>
                                @if(!empty($benevolat['organisation']))
                                    <div class="cv-company">{{ $benevolat['organisation'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>
