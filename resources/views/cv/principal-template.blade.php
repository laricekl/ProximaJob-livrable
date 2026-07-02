<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CV principal - {{ trim(($cvProfile->prenom ?? '').' '.($cvProfile->nom ?? '')) }}</title>
    <style>
        @page {
            margin: 18mm;
            size: A4;
        }

        body {
            color: #1f2933;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11.5px;
            line-height: 1.5;
            margin: 0;
        }

        h1, h2, h3, p {
            margin: 0;
        }

        .header {
            border-bottom: 2px solid #355f8d;
            padding-bottom: 16px;
            text-align: center;
        }

        .name {
            color: #17324d;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 0;
            line-height: 1.18;
        }

        .contact {
            color: #4b5563;
            font-size: 11px;
            margin-top: 8px;
        }

        .section {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            border-bottom: 1px solid #d7dee8;
            color: #355f8d;
            font-size: 13.5px;
            font-weight: 700;
            letter-spacing: 0;
            padding-bottom: 4px;
            text-transform: uppercase;
        }

        .item {
            margin-top: 11px;
            page-break-inside: avoid;
        }

        .item-head {
            display: table;
            width: 100%;
        }

        .item-title {
            color: #17324d;
            display: table-cell;
            font-weight: 700;
            padding-right: 12px;
            width: 70%;
        }

        .item-date {
            color: #64748b;
            display: table-cell;
            font-size: 10.5px;
            font-weight: 700;
            text-align: right;
            width: 30%;
        }

        .muted {
            color: #52616f;
            font-size: 11px;
            font-weight: 600;
            margin-top: 2px;
        }

        .text {
            color: #334155;
            margin-top: 5px;
            white-space: pre-line;
        }

        .pill {
            background: #eef4f8;
            border: 1px solid #dbe7ef;
            border-radius: 9px;
            color: #244761;
            display: inline-block;
            font-size: 10.5px;
            font-weight: 700;
            margin: 6px 5px 0 0;
            padding: 4px 8px;
        }
    </style>
</head>
<body>
    @php
        $fullName = trim(($cvProfile->prenom ?? '').' '.($cvProfile->nom ?? '')) ?: 'CV candidat';
        $contact = collect([
            $cvProfile->email,
            $cvProfile->telephone,
        ])->filter()->implode(' | ');
        $limitText = fn ($text, $limit = 420) => \Illuminate\Support\Str::limit((string) $text, $limit, '...');
        $competenceTags = $cvProfile->competences->pluck('description')->filter()->take(12);
        $softwareTags = collect(explode(',', (string) $cvProfile->logiciels))->map(fn ($value) => trim($value))->filter()->take(10);
        $experiences = $cvProfile->experiences->take(5);
        $formations = $cvProfile->formations->take(4);
        $perfectionnements = $cvProfile->perfectionnements->take(4);
        $langues = $cvProfile->langues->take(5);
        $benevolats = $cvProfile->benevolats->take(3);
    @endphp

    <div class="header">
        <h1 class="name">{{ $fullName }}</h1>
        @if ($contact)
            <p class="contact">{{ $contact }}</p>
        @endif
    </div>

    @if ($cvProfile->langues_competences || $competenceTags->isNotEmpty() || $softwareTags->isNotEmpty())
        <div class="section">
            <h2 class="section-title">Competences</h2>
            @if ($cvProfile->langues_competences)
                <p class="text">{{ $limitText($cvProfile->langues_competences, 520) }}</p>
            @endif
            @foreach ($competenceTags as $competence)
                <span class="pill">{{ $competence }}</span>
            @endforeach
            @foreach ($softwareTags as $software)
                <span class="pill">{{ $software }}</span>
            @endforeach
        </div>
    @endif

    @if ($experiences->isNotEmpty())
        <div class="section">
            <h2 class="section-title">Experience professionnelle</h2>
            @foreach ($experiences as $experience)
                <div class="item">
                    <div class="item-head">
                        <h3 class="item-title">{{ $experience->poste }}</h3>
                        <p class="item-date">{{ $experience->periode }}</p>
                    </div>
                    @if ($experience->entreprise)
                        <p class="muted">{{ $experience->entreprise }}</p>
                    @endif
                    @if ($experience->description)
                        <p class="text">{{ $limitText($experience->description) }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if ($formations->isNotEmpty())
        <div class="section">
            <h2 class="section-title">Formation</h2>
            @foreach ($formations as $formation)
                <div class="item">
                    <div class="item-head">
                        <h3 class="item-title">{{ $formation->diplome }}</h3>
                        <p class="item-date">{{ $formation->periode }}</p>
                    </div>
                    @if ($formation->etablissement)
                        <p class="muted">{{ $formation->etablissement }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if ($perfectionnements->isNotEmpty())
        <div class="section">
            <h2 class="section-title">Perfectionnement</h2>
            @foreach ($perfectionnements as $perfectionnement)
                <div class="item">
                    <div class="item-head">
                        <h3 class="item-title">{{ $perfectionnement->formation }}</h3>
                        <p class="item-date">{{ $perfectionnement->annee }}</p>
                    </div>
                    @if ($perfectionnement->etablissement)
                        <p class="muted">{{ $perfectionnement->etablissement }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if ($langues->isNotEmpty())
        <div class="section">
            <h2 class="section-title">Langues</h2>
            @foreach ($langues as $langue)
                <p class="text"><strong>{{ $langue->nom }}</strong>@if ($langue->niveau) : {{ $langue->niveau }} @endif</p>
            @endforeach
        </div>
    @endif

    @if ($benevolats->isNotEmpty())
        <div class="section">
            <h2 class="section-title">Activites benevoles</h2>
            @foreach ($benevolats as $benevolat)
                <div class="item">
                    <div class="item-head">
                        <h3 class="item-title">{{ $benevolat->role }}</h3>
                        <p class="item-date">{{ $benevolat->periode }}</p>
                    </div>
                    @if ($benevolat->organisation)
                        <p class="muted">{{ $benevolat->organisation }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</body>
</html>
