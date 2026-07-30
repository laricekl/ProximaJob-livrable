@if($experiences->isNotEmpty())
    <section class="cv-section experience-section">
        <h2 class="section-title">Expérience professionnelle</h2>
        <div class="section-content">
            @foreach($experiences as $experience)
                @if(!empty($experience['poste']))
                    <article class="cv-item experience-item">
                        <div class="item-period">{{ $experience['periode'] ?? '' }}</div>
                        <div class="item-body">
                            <h3 class="item-title">{{ $experience['poste'] }}</h3>
                            @if(!empty($experience['entreprise']))
                                <div class="item-subtitle">{{ $experience['entreprise'] }}</div>
                            @endif
                            @if(!empty($experience['description']))
                                <p class="item-description">{{ $limitText($experience['description'], 260) }}</p>
                            @endif
                        </div>
                    </article>
                @endif
            @endforeach
        </div>
    </section>
@endif
