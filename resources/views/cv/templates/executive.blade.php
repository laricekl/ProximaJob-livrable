<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CV exécutif - {{ $fullName }}</title>
    <style>
        @page { margin: 0 0 13mm; size: A4; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: {{ $fontFamily }};
            font-size: {{ $density['font'] }};
            line-height: {{ $density['line'] }};
            color: #202833;
            background: #fff;
        }
        .hero {
            padding: 16mm 16mm 12mm;
            color: #fff;
            background: #17212b;
            page-break-inside: avoid;
        }
        .hero-accent { width: 55px; height: 5px; margin-bottom: 13px; background: {{ $accent }}; }
        .name { margin: 0; font-size: 25pt; line-height: 1.05; letter-spacing: .3px; }
        .target {
            margin-top: 7px;
            color: #dce3e9;
            font-size: 10.5pt;
            font-weight: 700;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }
        .contact { margin-top: 14px; color: #c3ccd4; font-size: 8.5pt; }
        .contact span { margin-right: 14px; }
        .content { padding: 12mm 16mm 0; }
        .executive-intro {
            width: 100%;
            margin-bottom: 18px;
            border-collapse: collapse;
            table-layout: fixed;
            page-break-inside: avoid;
        }
        .summary-cell {
            width: 61%;
            padding: 0 14px 0 0;
            vertical-align: top;
        }
        .expertise-cell {
            width: 39%;
            padding: 0 0 0 14px;
            border-left: 1px solid #dce1e5;
            vertical-align: top;
        }
        .eyebrow {
            margin: 0 0 6px;
            color: {{ $accent }};
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }
        .summary-text { margin: 0; color: #3b4650; font-size: 9.3pt; }
        .expertise-list { margin: 0; padding-left: 15px; }
        .expertise-list li { margin-bottom: 4px; font-size: 8.6pt; }
        .cv-section { margin-bottom: {{ $density['section'] }}; }
        .section-title {
            margin: 0 0 10px;
            padding: 0 0 5px 8px;
            border-left: 3px solid {{ $accent }};
            border-bottom: 1px solid #cbd2d8;
            color: #17212b;
            font-size: 11pt;
            letter-spacing: 1.3px;
            text-transform: uppercase;
        }
        .cv-item {
            display: table;
            width: 100%;
            margin-bottom: {{ $density['item'] }};
            page-break-inside: avoid;
        }
        .item-period {
            display: table-cell;
            width: 22%;
            padding-right: 12px;
            color: {{ $accent }};
            font-size: 8.3pt;
            font-weight: 700;
            vertical-align: top;
        }
        .item-body { display: table-cell; width: 78%; vertical-align: top; }
        .item-title { margin: 0 0 2px; color: #17212b; font-size: 10pt; }
        .item-subtitle { margin-bottom: 4px; color: #53606b; font-size: 8.7pt; font-weight: 700; }
        .item-description { margin: 0; color: #3f4b55; font-size: 8.8pt; }
        .skills-section { margin-top: 3px; }
        .skills-section .professional-summary { display: none; }
        .software-line { margin-bottom: 7px; }
        .skill-list { display: table; width: 100%; }
        .skill-item {
            display: inline-block;
            margin: 0 5px 5px 0;
            padding: 3px 7px;
            border: 1px solid #d2d8dd;
            color: #34414b;
            font-size: 8.1pt;
            background: #f5f7f8;
        }
        .language-item { margin-right: 14px; }
        .footer {
            position: fixed;
            right: 16mm;
            bottom: 5mm;
            color: #7b858d;
            font-size: 7.5pt;
        }
        .footer:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="footer">Page </div>
    <header class="hero">
        <div class="hero-accent"></div>
        <h1 class="name">{{ $fullName }}</h1>
        @if($targetTitle !== '')<div class="target">{{ $targetTitle }}</div>@endif
        <div class="contact">
            @if(!empty($location))<span>{{ $location }}</span>@endif
            @if(!empty($telephone))<span>{{ $telephone }}</span>@endif
            @if(!empty($email))<span>{{ $email }}</span>@endif
        </div>
    </header>

    <main class="content">
        @if(!empty($professionalSummary) || $competencesList->isNotEmpty())
            <table class="executive-intro">
                <tr>
                    <td class="summary-cell">
                        <h2 class="eyebrow">Profil professionnel</h2>
                        <p class="summary-text">{{ $professionalSummary ?: 'Professionnel expérimenté orienté résultats et amélioration continue.' }}</p>
                    </td>
                    <td class="expertise-cell">
                        <h2 class="eyebrow">Expertises clés</h2>
                        <ul class="expertise-list">
                            @foreach($competencesList->take(5) as $competence)
                                @if(!empty($competence['description']))
                                    <li>{{ $limitText($competence['description'], 90) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </td>
                </tr>
            </table>
        @endif

        @include('cv.partials.template-experiences')

        @if($options['section_order'] === 'skills_first')
            @include('cv.partials.template-skills')
        @endif
        @include('cv.partials.template-education')
        @if($options['section_order'] === 'experience_first')
            @include('cv.partials.template-skills')
        @endif
        @include('cv.partials.template-additional')
    </main>
</body>
</html>
