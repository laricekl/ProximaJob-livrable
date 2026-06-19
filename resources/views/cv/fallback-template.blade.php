<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV - {{ $cvProfile->prenom }} {{ $cvProfile->nom }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2c5aa0;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2c5aa0;
            margin: 0;
            font-size: 28px;
        }
        .header .contact {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h2 {
            color: #2c5aa0;
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 5px;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .personalization-note {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #2c5aa0;
            margin-bottom: 20px;
            font-style: italic;
        }
        .experience-item, .formation-item {
            margin-bottom: 15px;
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
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 14px;
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
            @if($cvProfile->ville)
                <span>📍 {{ $cvProfile->ville }}{{ $cvProfile->province ? ', ' . $cvProfile->province : '' }}</span>
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
        <p>{{ $cvProfile->objectif_professionnel }}</p>
    </div>
    @endif

    <!-- Expériences professionnelles -->
    @if($cvProfile->experiences && $cvProfile->experiences->count() > 0)
    <div class="section">
        <h2>Expériences Professionnelles</h2>
        @foreach($cvProfile->experiences as $experience)
        <div class="experience-item">
            <div class="item-header">
                <span>{{ $experience->poste }}</span>
                <span>{{ $experience->periode }}</span>
            </div>
            <div class="company">{{ $experience->entreprise }}</div>
            @if($experience->description)
            <p>{{ $experience->description }}</p>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Formations -->
    @if($cvProfile->formations && $cvProfile->formations->count() > 0)
    <div class="section">
        <h2>Formation</h2>
        @foreach($cvProfile->formations as $formation)
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
        @if($cvProfile->competences->where('type', 'specifique')->count() > 0)
        <div style="margin-bottom: 15px;">
            <strong>Techniques :</strong>
            <div class="skills-list">
                @foreach($cvProfile->competences->where('type', 'specifique') as $competence)
                <span class="skill-tag">{{ $competence->description }}</span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Compétences générales -->
        @if($cvProfile->competences->where('type', 'generale')->count() > 0)
        <div style="margin-bottom: 15px;">
            <strong>Transversales :</strong>
            <div class="skills-list">
                @foreach($cvProfile->competences->where('type', 'generale') as $competence)
                <span class="skill-tag">{{ $competence->description }}</span>
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
    @if($cvProfile->langues && $cvProfile->langues->count() > 0)
    <div class="section">
        <h2>Langues</h2>
        <div class="languages">
            @foreach($cvProfile->langues as $langue)
            <div class="language-item">
                <strong>{{ $langue->nom }}:</strong> 
                <span>{{ $langue->niveau }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Certifications et perfectionnements -->
    @if($cvProfile->perfectionnements && $cvProfile->perfectionnements->count() > 0)
    <div class="section">
        <h2>Certifications & Perfectionnements</h2>
        @foreach($cvProfile->perfectionnements as $perfectionnement)
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
    @if($cvProfile->benevolats && $cvProfile->benevolats->count() > 0)
    <div class="section">
        <h2>Engagements & Bénévolat</h2>
        @foreach($cvProfile->benevolats as $benevolat)
        <div class="experience-item">
            <div class="item-header">
                <span>{{ $benevolat->role }}</span>
                <span>{{ $benevolat->periode }}</span>
            </div>
            <div class="company">{{ $benevolat->organisation }}</div>
            @if($benevolat->description)
            <p>{{ $benevolat->description }}</p>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Message de fallback -->
    <div class="no-print" style="text-align: center; margin-top: 30px; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px;">
        <small>CV généré automatiquement - Version simplifiée</small>
    </div>
</body>
</html>