@if($experiences->isNotEmpty())
    <div class="cv-section">
        <div class="cv-section-title">EXPÉRIENCES DE TRAVAIL</div>
        @foreach($experiences as $experience)
            @if(!empty($experience['poste']))
                <div class="cv-item">
                    <div class="cv-dates">{{ $experience['periode'] ?? '' }}</div>
                    <div class="cv-content">
                        <div class="cv-job-title">{{ $experience['poste'] }}</div>
                        @if(!empty($experience['entreprise']))
                            <div class="cv-company">{{ $experience['entreprise'] }}</div>
                        @endif
                        @if(!empty($experience['description']))
                            <div class="cv-company">{{ $limitText($experience['description'], 220) }}</div>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif
