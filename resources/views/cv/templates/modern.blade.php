<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CV moderne - {{ $fullName }}</title>
    <style>
        @page { margin: 0; size: A4; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: {{ $fontFamily }};
            font-size: {{ $density['font'] }};
            line-height: {{ $density['line'] }};
            color: #26323b;
            background: #ffffff;
        }
        .sidebar-background {
            position: fixed;
            z-index: -1;
            top: 0;
            bottom: 0;
            left: 0;
            width: 31%;
            background: {{ $accent }};
        }
        .layout { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .sidebar {
            width: 31%;
            vertical-align: top;
            padding: 18mm 9mm 15mm 11mm;
            color: #ffffff;
            background: transparent;
        }
        .main {
            width: 69%;
            vertical-align: top;
            padding: 17mm 14mm 15mm 13mm;
        }
        .name {
            margin: 0;
            font-size: 25pt;
            line-height: 1.05;
            font-weight: 700;
            letter-spacing: -0.6px;
            color: #17212b;
        }
        .target {
            margin-top: 7px;
            font-size: 11pt;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: {{ $accent }};
        }
        .header-rule {
            width: 42px;
            height: 4px;
            margin: 13px 0 19px;
            background: {{ $accent }};
        }
        .sidebar-title {
            margin: 0 0 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid rgba(255,255,255,.48);
            font-size: 9pt;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }
        .sidebar-block { margin-bottom: 22px; page-break-inside: avoid; }
        .contact-line { margin-bottom: 7px; font-size: 8.8pt; word-wrap: break-word; }
        .sidebar .cv-section { margin-bottom: 20px; page-break-inside: avoid; }
        .sidebar .section-title {
            margin: 0 0 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid rgba(255,255,255,.48);
            color: #ffffff;
            font-size: 9pt;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }
        .sidebar .professional-summary,
        .sidebar .software-line { margin: 0 0 9px; font-size: 8.6pt; }
        .sidebar .skill-item {
            display: block;
            margin-bottom: 5px;
            padding-left: 9px;
            position: relative;
            font-size: 8.6pt;
        }
        .sidebar .skill-item:before { content: "•"; position: absolute; left: 0; }
        .sidebar .language-item { display: block; margin-bottom: 5px; font-size: 8.5pt; }
        .main .cv-section { margin-bottom: {{ $density['section'] }}; }
        .main .section-title {
            margin: 0 0 9px;
            padding-bottom: 4px;
            border-bottom: 2px solid {{ $accent }};
            color: #17212b;
            font-size: 10.5pt;
            letter-spacing: 1.2px;
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
            width: 25%;
            padding-right: 9px;
            color: {{ $accent }};
            font-size: 8.2pt;
            font-weight: 700;
            vertical-align: top;
        }
        .item-body { display: table-cell; width: 75%; vertical-align: top; }
        .item-title { margin: 0 0 2px; color: #17212b; font-size: 10pt; }
        .item-subtitle { margin-bottom: 3px; color: #5b6670; font-size: 8.8pt; font-weight: 700; }
        .item-description { margin: 0; color: #48545e; font-size: 8.7pt; }
        .main .skills-section { display: none; }
        .main .languages-section { display: none; }
        .footer {
            position: fixed;
            right: 14mm;
            bottom: 6mm;
            color: #8b949c;
            font-size: 7.5pt;
        }
        .footer:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="sidebar-background"></div>
    <div class="footer">Page </div>
    <table class="layout">
        <tr>
            <td class="sidebar">
                <div class="sidebar-block">
                    <h2 class="sidebar-title">Coordonnées</h2>
                    @if(!empty($email))<div class="contact-line">{{ $email }}</div>@endif
                    @if(!empty($telephone))<div class="contact-line">{{ $telephone }}</div>@endif
                    @if(!empty($location))<div class="contact-line">{{ $location }}</div>@endif
                </div>

                @include('cv.partials.template-skills')

                @if($langues->isNotEmpty())
                    <section class="cv-section languages-section">
                        <h2 class="section-title">Langues</h2>
                        @foreach($langues as $langue)
                            @if(!empty($langue['nom']))
                                <span class="language-item">
                                    <strong>{{ $langue['nom'] }}</strong>
                                    @if(!empty($langue['niveau'])) - {{ $langue['niveau'] }} @endif
                                </span>
                            @endif
                        @endforeach
                    </section>
                @endif
            </td>
            <td class="main">
                <header>
                    <h1 class="name">{{ $fullName }}</h1>
                    @if($targetTitle !== '')<div class="target">{{ $targetTitle }}</div>@endif
                    <div class="header-rule"></div>
                </header>

                @if($options['section_order'] === 'skills_first')
                    @include('cv.partials.template-skills')
                @endif
                @include('cv.partials.template-experiences')
                @if($options['section_order'] === 'experience_first')
                    @include('cv.partials.template-skills')
                @endif
                @include('cv.partials.template-education')

                @if($perfectionnements->isNotEmpty())
                    <section class="cv-section additional-section">
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

                @if($benevolats->isNotEmpty())
                    <section class="cv-section volunteer-section">
                        <h2 class="section-title">Engagement</h2>
                        @foreach($benevolats as $item)
                            @if(!empty($item['role']))
                                <article class="cv-item">
                                    <div class="item-period">{{ $item['periode'] ?? '' }}</div>
                                    <div class="item-body">
                                        <h3 class="item-title">{{ $item['role'] }}</h3>
                                        @if(!empty($item['organisation']))<div class="item-subtitle">{{ $item['organisation'] }}</div>@endif
                                    </div>
                                </article>
                            @endif
                        @endforeach
                    </section>
                @endif
            </td>
        </tr>
    </table>
</body>
</html>
