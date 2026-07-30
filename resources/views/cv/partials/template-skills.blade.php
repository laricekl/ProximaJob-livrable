@if(!empty($professionalSummary) || !empty($logiciels) || $competencesList->isNotEmpty())
    <section class="cv-section skills-section">
        <h2 class="section-title">Compétences</h2>
        <div class="section-content">
            @if(!empty($professionalSummary))
                <p class="professional-summary">{{ $professionalSummary }}</p>
            @endif
            @if(!empty($logiciels))
                <div class="software-line"><strong>Outils :</strong> {{ $logiciels }}</div>
            @endif
            @if($competencesList->isNotEmpty())
                <div class="skill-list">
                    @foreach($competencesList as $competence)
                        @if(!empty($competence['description']))
                            <span class="skill-item">{{ $limitText($competence['description'], 150) }}</span>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endif
