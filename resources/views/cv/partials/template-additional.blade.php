@if($perfectionnements->isNotEmpty())
    <section class="cv-section additional-section">
        <h2 class="section-title">Perfectionnement</h2>
        <div class="section-content">
            @foreach($perfectionnements as $item)
                @if(!empty($item['formation']))
                    <article class="cv-item compact-item">
                        <div class="item-period">{{ $item['annee'] ?? '' }}</div>
                        <div class="item-body">
                            <h3 class="item-title">{{ $item['formation'] }}</h3>
                            @if(!empty($item['etablissement']))
                                <div class="item-subtitle">{{ $item['etablissement'] }}</div>
                            @endif
                        </div>
                    </article>
                @endif
            @endforeach
        </div>
    </section>
@endif

@if($langues->isNotEmpty())
    <section class="cv-section languages-section">
        <h2 class="section-title">Langues</h2>
        <div class="section-content language-list">
            @foreach($langues as $langue)
                @if(!empty($langue['nom']))
                    <span class="language-item">
                        <strong>{{ $langue['nom'] }}</strong>
                        @if(!empty($langue['niveau'])) - {{ $langue['niveau'] }} @endif
                    </span>
                @endif
            @endforeach
        </div>
    </section>
@endif

@if($benevolats->isNotEmpty())
    <section class="cv-section volunteer-section">
        <h2 class="section-title">Engagement</h2>
        <div class="section-content">
            @foreach($benevolats as $item)
                @if(!empty($item['role']))
                    <article class="cv-item compact-item">
                        <div class="item-period">{{ $item['periode'] ?? '' }}</div>
                        <div class="item-body">
                            <h3 class="item-title">{{ $item['role'] }}</h3>
                            @if(!empty($item['organisation']))
                                <div class="item-subtitle">{{ $item['organisation'] }}</div>
                            @endif
                        </div>
                    </article>
                @endif
            @endforeach
        </div>
    </section>
@endif
