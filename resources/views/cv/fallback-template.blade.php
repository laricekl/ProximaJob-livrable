<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV - {{ $cvProfile->prenom }} {{ $cvProfile->nom }}</title>
    <style>
        @page {
            margin: 16mm;
            size: A4;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.4;
            color: #333;
            max-width: 210mm;
            margin: 0 auto;
            padding: 0;
            background: #fff;
            font-size: 11pt;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2c5aa0;
            padding-bottom: 14px;
            margin-bottom: 18px;
            page-break-inside: avoid;
        }
        .header h1 {
            color: #2c5aa0;
            margin: 0;
            font-size: 23px;
        }
        .header .contact {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .section h2 {
            color: #2c5aa0;
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-size: 15px;
        }
        .personalization-note {
            background: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #2c5aa0;
            margin-bottom: 14px;
            font-style: italic;
        }
        .experience-item, .formation-item {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .item-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }
        .company, .school {
            color: #555;
        }
        .skills-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .skill-tag {
            background: #e9ecef;
            padding: 4px 9px;
            border-radius: 15px;
            font-size: 11px;
        }
        .languages {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .language-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    @php
        $limitText = fn ($text, $limit = 360) => \Illuminate\Support\Str::limit((string) $text, $limit, '...');
        $experiences = $cvProfile->experiences?->take(5) ?? collect();
        $formations = $cvProfile->formations?->take(4) ?? collect();
        $competencesSpecifiques = $cvProfile->competences?->where('type', 'specifique')->take(8) ?? collect();
        $competencesGenerales = $cvProfile->competences?->where('type', 'generale')->take(8) ?? collect();
        $perfectionnements = $cvProfile->perfectionnements?->take(4) ?? collect();
        $langues = $cvProfile->langues?->take(5) ?? collect();
        $benevolats = $cvProfile->benevolats?->take(3) ?? collect();
    @endphp

    <!-- En-tête -->
    <div class="header">
        <h1>{{ $cvProfile->prenom }} {{ $cvProfile->nom }}</h1>
        <div class="contact">
            @if($cvProfile->email)
                <span>📧 {{ $cvProfile->email }}</span>
            @endif
            @if($cvProfile->telephone)
                <span>📞 {{ $cvProfile->telephone }}</span>
            @endif
        </div>
    </div>

    <!-- Note de personnalisation -->
    @if(isset($personalized_note))
    <div class="personalization-note">
        {{ $personalized_note }}
    </div>
    @endif

    <!-- Profil professionnel -->
    @if($cvProfile->objectif_professionnel)
    <div class="section">
        <h2>Profil Professionnel</h2>
        <p>{{ $limitText($cvProfile->objectif_professionnel, 420) }}</p>
    </div>
    @endif

    <!-- Expériences professionnelles -->
    @if($experiences->isNotEmpty())
    <div class="section">
        <h2>Expériences Professionnelles</h2>
        @foreach($experiences as $experience)
        <div class="experience-item">
            <div class="item-header">
                <span>{{ $experience->poste }}</span>
                <span>{{ $experience->periode }}</span>
            </div>
            <div class="company">{{ $experience->entreprise }}</div>
            @if($experience->description)
            <p>{{ $limitText($experience->description) }}</p>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Formations -->
    @if($formations->isNotEmpty())
    <div class="section">
        <h2>Formation</h2>
        @foreach($formations as $formation)
        <div class="formation-item">
            <div class="item-header">
                <span>{{ $formation->diplome }}</span>
                <span>{{ $formation->periode }}</span>
            </div>
            <div class="school">{{ $formation->etablissement }}</div>
            @if($formation->description)
            <p>{{ $formation->description }}</p>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Compétences -->
    <div class="section">
        <h2>Compétences</h2>
        
        <!-- Compétences spécifiques -->
        @if($competencesSpecifiques->isNotEmpty())
        <div style="margin-bottom: 15px;">
            <strong>Techniques :</strong>
            <div class="skills-list">
                @foreach($competencesSpecifiques as $competence)
                <span class="skill-tag">{{ $limitText($competence->description, 120) }}</span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Compétences générales -->
        @if($competencesGenerales->isNotEmpty())
        <div style="margin-bottom: 15px;">
            <strong>Transversales :</strong>
            <div class="skills-list">
                @foreach($competencesGenerales as $competence)
                <span class="skill-tag">{{ $limitText($competence->description, 120) }}</span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Logiciels -->
        @if($cvProfile->logiciels)
        <div style="margin-bottom: 15px;">
            <strong>Logiciels :</strong>
            <div class="skills-list">
                @foreach(explode(',', $cvProfile->logiciels) as $logiciel)
                <span class="skill-tag">{{ trim($logiciel) }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Langues -->
    @if($langues->isNotEmpty())
    <div class="section">
        <h2>Langues</h2>
        <div class="languages">
            @foreach($langues as $langue)
            <div class="language-item">
                <strong>{{ $langue->nom }}:</strong> 
                <span>{{ $langue->niveau }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Certifications et perfectionnements -->
    @if($perfectionnements->isNotEmpty())
    <div class="section">
        <h2>Certifications & Perfectionnements</h2>
        @foreach($perfectionnements as $perfectionnement)
        <div class="experience-item">
            <div class="item-header">
                <span>{{ $perfectionnement->formation }}</span>
                <span>{{ $perfectionnement->annee }}</span>
            </div>
            <div class="company">{{ $perfectionnement->etablissement }}</div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Bénévolat -->
    @if($benevolats->isNotEmpty())
    <div class="section">
        <h2>Engagements & Bénévolat</h2>
        @foreach($benevolats as $benevolat)
        <div class="experience-item">
            <div class="item-header">
                <span>{{ $benevolat->role }}</span>
                <span>{{ $benevolat->periode }}</span>
            </div>
            <div class="company">{{ $benevolat->organisation }}</div>
            @if($benevolat->description)
            <p>{{ $limitText($benevolat->description) }}</p>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</body>
</html>
