@php
    $showCompetenceSummary = $visibleSections->contains('languages') && !empty($langues_competences);
@endphp

@if($showCompetenceSummary || ($visibleSections->contains('software') && !empty($logiciels)) || $competencesList->isNotEmpty())
    <div class="cv-section">
        <div class="cv-section-title">COMPÉTENCES</div>
        <div class="cv-competences-list">
            @if($showCompetenceSummary)
                <div class="cv-skills-section">• <strong>Résumé :</strong> {{ $langues_competences }}</div>
            @endif
            @if($visibleSections->contains('software') && !empty($logiciels))
                <div class="cv-skills-section">• <strong>Logiciels :</strong> {{ $logiciels }}</div>
            @endif
            @foreach($competencesList as $competence)
                @if(!empty($competence['description']))
                    <div class="cv-competence-item">• {!! nl2br(htmlspecialchars($limitText($competence['description'], 260))) !!}</div>
                @endif
            @endforeach
        </div>
    </div>
@endif
