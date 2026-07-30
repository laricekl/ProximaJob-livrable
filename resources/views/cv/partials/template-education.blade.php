@if($formations->isNotEmpty())
    <section class="cv-section education-section">
        <h2 class="section-title">Formation</h2>
        <div class="section-content">
            @foreach($formations as $formation)
                @if(!empty($formation['diplome']))
                    <article class="cv-item education-item">
                        <div class="item-period">{{ $formation['periode'] ?? '' }}</div>
                        <div class="item-body">
                            <h3 class="item-title">{{ $formation['diplome'] }}</h3>
                            @if(!empty($formation['etablissement']))
                                <div class="item-subtitle">{{ $formation['etablissement'] }}</div>
                            @endif
                        </div>
                    </article>
                @endif
            @endforeach
        </div>
    </section>
@endif
