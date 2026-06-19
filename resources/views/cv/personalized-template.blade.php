<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV - {{ $prenom }} {{ $nom }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }
        
        .cv-container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        .cv-header {
            text-align: center;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .cv-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
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
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .cv-section-title {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }
        
        .cv-item {
            display: table;
            width: 100%;
            margin-bottom: 12px;
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
    <div class="cv-container">
        <!-- En-tête -->
        <div class="cv-header">
            <div class="cv-name">{{ strtoupper($nom ?? '') }} {{ strtoupper($prenom ?? '') }}</div>
            
            @if(!empty($personalization_note))
                <div class="personalization-note">{{ $personalization_note }}</div>
            @endif
            
            @if(!empty($adresse))
                <div class="cv-contact">{{ $adresse }}</div>
            @endif
            @if(!empty($ville) || !empty($province) || !empty($code_postal))
                <div class="cv-contact">
                    {{ $ville ?? '' }}{{ !empty($province) ? ' (' . $province . ')' : '' }}{{ !empty($code_postal) ? ' ' . $code_postal : '' }}
                </div>
            @endif
            @if(!empty($telephone))
                <div class="cv-contact">{{ $telephone }}</div>
            @endif
            @if(!empty($email))
                <div class="cv-contact">Courriel : {{ $email }}</div>
            @endif
        </div>

        <!-- Compétences en premier -->
        @if(!empty($langues_competences) || !empty($logiciels) || (!empty($competences) && count($competences) > 0))
            <div class="cv-section">
                <div class="cv-section-title">COMPÉTENCES</div>
                <div class="cv-competences-list">
                    @if(!empty($langues_competences))
                        <div class="cv-skills-section">• <strong>Langues :</strong> {{ $langues_competences }}</div>
                    @endif
                    @if(!empty($logiciels))
                        <div class="cv-skills-section">• <strong>Logiciels :</strong> {{ $logiciels }}</div>
                    @endif
                    @if(!empty($competences) && count($competences) > 0)
                        @foreach($competences as $competence)
                            @if(!empty($competence['description']))
                                <div class="cv-competence-item">• {!! nl2br(htmlspecialchars($competence['description'])) !!}</div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        @endif

        <!-- Expériences de travail -->
        @if(!empty($experiences) && count($experiences) > 0)
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
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Formation -->
        @if(!empty($formations) && count($formations) > 0)
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
        @if(!empty($perfectionnements) && count($perfectionnements) > 0)
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
        @if(!empty($langues) && count($langues) > 0)
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
        @if(!empty($benevolats) && count($benevolats) > 0)
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