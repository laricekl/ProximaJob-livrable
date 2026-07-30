<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CV portfolio - {{ $fullName }}</title>
    <style>
        @page { margin: 0; size: A4; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #1f2930; font-family: {{ $fontFamily }}; font-size: 9pt; line-height: 1.3; }
        .side-bg { position: fixed; z-index: -1; top: 0; left: 0; bottom: 0; width: 34%; background: #1d2830; }
        .layout { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .side { width: 34%; padding: 15mm 9mm 13mm; color: #fff; vertical-align: top; }
        .main { width: 66%; padding: 15mm 13mm 13mm; vertical-align: top; }
        .portrait { width: 30mm; height: 30mm; margin: 0 auto 10mm; overflow: hidden; border: 3px solid {{ $accent }}; border-radius: 4px; background: {{ $accent }}; text-align: center; font-size: 20pt; line-height: 28mm; font-weight: 700; }
        .portrait img { width: 30mm; height: 30mm; object-fit: cover; }
        .side-title { margin: 0 0 7px; color: {{ $accent }}; font-size: 8.7pt; letter-spacing: 1.5px; text-transform: uppercase; }
        .side-block { margin-bottom: 18px; }
        .contact-line { margin-bottom: 6px; font-size: 8.2pt; word-wrap: break-word; }
        .side .section-title { color: {{ $accent }}; font-size: 8.7pt; letter-spacing: 1.5px; text-transform: uppercase; }
        .side .professional-summary, .side .software-line { font-size: 8.1pt; }
        .side .skill-item { display: block; margin-bottom: 5px; font-size: 8pt; }
        .name { margin: 0; font-size: 27pt; line-height: 1; letter-spacing: -1px; }
        .target { margin: 7px 0 18px; color: {{ $accent }}; font-size: 10pt; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .main .cv-section { margin-bottom: 13px; }
        .main .section-title { margin: 0 0 8px; color: #1d2830; font-size: 10pt; text-transform: uppercase; letter-spacing: 1.2px; }
        .main .section-title:after { content: ""; display: block; width: 35px; margin-top: 4px; border-top: 3px solid {{ $accent }}; }
        .cv-item { margin-bottom: 8px; page-break-inside: avoid; }
        .item-period { color: {{ $accent }}; font-size: 7.8pt; font-weight: 700; }
        .item-title { margin: 1px 0; font-size: 9.6pt; }
        .item-subtitle { color: #69757d; font-size: 8.2pt; }
        .item-description { margin: 3px 0 0; color: #465159; font-size: 8.4pt; }
        .main .skills-section { display: none; }
        .language-item { display: block; margin-bottom: 5px; font-size: 8pt; }
        .footer { position: fixed; right: 12mm; bottom: 5mm; color: #9aa1a5; font-size: 7pt; }
        .footer:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="side-bg"></div><div class="footer">Page </div>
    <table class="layout"><tr>
        <td class="side">
            <div class="portrait">@if($includePhoto)<img src="{{ $photo_data }}" alt="">@else{{ $initials }}@endif</div>
            <div class="side-block"><h2 class="side-title">Contact</h2>
                @if(!empty($email))<div class="contact-line">{{ $email }}</div>@endif
                @if(!empty($telephone))<div class="contact-line">{{ $telephone }}</div>@endif
                @if(!empty($location))<div class="contact-line">{{ $location }}</div>@endif
            </div>
            @include('cv.partials.template-skills')
            @if($langues->isNotEmpty())<div class="side-block"><h2 class="side-title">Langues</h2>
                @foreach($langues as $langue) @if(!empty($langue['nom']))<span class="language-item"><strong>{{ $langue['nom'] }}</strong>@if(!empty($langue['niveau'])) - {{ $langue['niveau'] }}@endif</span>@endif @endforeach
            </div>@endif
        </td>
        <td class="main">
            <h1 class="name">{{ $fullName }}</h1>
            @if($targetTitle !== '')<div class="target">{{ $targetTitle }}</div>@endif
            @include('cv.partials.template-experiences')
            @include('cv.partials.template-education')
            @if($perfectionnements->isNotEmpty() || $benevolats->isNotEmpty()) @include('cv.partials.template-additional') @endif
        </td>
    </tr></table>
</body>
</html>
