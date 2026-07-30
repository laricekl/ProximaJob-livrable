<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CV classique - {{ $fullName }}</title>
    <style>
        @page { margin: 10mm 17mm; size: A4; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: {{ $fontFamily }};
            font-size: 9.4pt;
            line-height: 1.24;
            color: #181818;
            background: #fff;
        }
        .header {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #222;
            text-align: center;
            page-break-inside: avoid;
        }
        .name {
            margin: 0 0 5px;
            font-size: 22pt;
            font-weight: 700;
            letter-spacing: 1.8px;
            text-transform: uppercase;
        }
        .target {
            margin-bottom: 8px;
            color: {{ $accent }};
            font-size: 10pt;
            font-weight: 700;
            letter-spacing: .8px;
        }
        .contact { color: #444; font-size: 8.8pt; }
        .contact span { padding: 0 5px; }
        .cv-section { margin-bottom: 7px; }
        .section-title {
            margin: 0 0 8px;
            color: #111;
            font-size: 10.5pt;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            border-bottom: 1px solid #777;
            padding-bottom: 3px;
        }
        .cv-item {
            display: table;
            width: 100%;
            margin-bottom: 4px;
            page-break-inside: avoid;
        }
        .item-period {
            display: table-cell;
            width: 23%;
            padding-right: 12px;
            color: #444;
            font-size: 8.5pt;
            font-style: italic;
            vertical-align: top;
        }
        .item-body { display: table-cell; width: 77%; vertical-align: top; }
        .item-title { margin: 0 0 1px; font-size: 9.7pt; }
        .item-subtitle { margin-bottom: 3px; color: #333; font-size: 8.8pt; }
        .item-description { margin: 0; color: #303030; font-size: 8.8pt; }
        .professional-summary {
            margin: 0 0 8px;
            padding: 9px 11px;
            border-left: 2px solid {{ $accent }};
            color: #333;
            font-style: italic;
            background: #f7f7f7;
        }
        .software-line { margin: 0 0 7px; }
        .skill-list { margin-left: 13px; }
        .skill-item { display: list-item; margin-bottom: 1px; }
        .language-list { line-height: 1.4; }
        .language-item { margin-right: 14px; white-space: nowrap; }
        .extras-grid {
            width: 100%;
            margin-top: 5px;
            border-collapse: collapse;
            table-layout: fixed;
            page-break-inside: avoid;
        }
        .extras-grid td { width: 50%; vertical-align: top; }
        .extras-grid td:first-child { padding-right: 13px; }
        .extras-grid td:last-child { padding-left: 13px; border-left: 1px solid #c8c8c8; }
        .extras-title {
            margin: 0 0 5px;
            font-size: 9.4pt;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .extras-line { margin-bottom: 3px; font-size: 8.6pt; }
        .footer {
            position: fixed;
            right: 0;
            bottom: -8mm;
            color: #777;
            font-size: 7.5pt;
        }
        .footer:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="footer">Page </div>
    <header class="header">
        <h1 class="name">{{ $fullName }}</h1>
        @if($targetTitle !== '')<div class="target">{{ $targetTitle }}</div>@endif
        <div class="contact">
            @if(!empty($location))<span>{{ $location }}</span>@endif
            @if(!empty($telephone))<span>{{ $telephone }}</span>@endif
            @if(!empty($email))<span>{{ $email }}</span>@endif
        </div>
    </header>

    @if($options['section_order'] === 'skills_first')
        @include('cv.partials.template-skills')
        @include('cv.partials.template-experiences')
    @else
        @include('cv.partials.template-experiences')
        @include('cv.partials.template-skills')
    @endif
    @include('cv.partials.template-education')

    @if($perfectionnements->isNotEmpty())
        <section class="cv-section">
            <h2 class="section-title">Perfectionnement</h2>
            @foreach($perfectionnements as $item)
                @if(!empty($item['formation']))
                    <article class="cv-item">
                        <div class="item-period">{{ $item['annee'] ?? '' }}</div>
                        <div class="item-body">
                            <h3 class="item-title">{{ $item['formation'] }}</h3>
                            @if(!empty($item['etablissement']))<div class="item-subtitle">{{ $item['etablissement'] }}</div>@endif
                        </div>
                    </article>
                @endif
            @endforeach
        </section>
    @endif

    @if($langues->isNotEmpty() || $benevolats->isNotEmpty())
        <table class="extras-grid">
            <tr>
                <td>
                    @if($langues->isNotEmpty())
                        <h2 class="extras-title">Langues</h2>
                        @foreach($langues as $langue)
                            @if(!empty($langue['nom']))
                                <div class="extras-line">
                                    <strong>{{ $langue['nom'] }}</strong>
                                    @if(!empty($langue['niveau'])) - {{ $langue['niveau'] }} @endif
                                </div>
                            @endif
                        @endforeach
                    @endif
                </td>
                <td>
                    @if($benevolats->isNotEmpty())
                        <h2 class="extras-title">Engagement</h2>
                        @foreach($benevolats as $item)
                            @if(!empty($item['role']))
                                <div class="extras-line"><strong>{{ $item['role'] }}</strong></div>
                                @if(!empty($item['organisation']))<div class="extras-line">{{ $item['organisation'] }}</div>@endif
                            @endif
                        @endforeach
                    @endif
                </td>
            </tr>
        </table>
    @endif
</body>
</html>
