<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CV technique - {{ $fullName }}</title>
    <style>
        @page { margin: 0; size: A4; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #18232c; font-family: {{ $fontFamily }}; font-size: 9pt; line-height: 1.3; }
        .top { padding: 14mm 15mm 10mm; background: #17242e; color: #fff; }
        .name { margin: 0; font-size: 24pt; letter-spacing: -.5px; }
        .target { margin-top: 4px; color: {{ $accent }}; font-weight: 700; letter-spacing: 1.4px; text-transform: uppercase; }
        .contact { margin-top: 10px; color: #c8d1d7; font-size: 8.4pt; }
        .contact span { margin-right: 14px; }
        .body { padding: 10mm 15mm 13mm; }
        .cv-section { margin-bottom: 12px; }
        .section-title { margin: 0 0 7px; padding: 5px 8px; border-left: 4px solid {{ $accent }}; background: #edf1f3; font-size: 9.5pt; letter-spacing: 1.2px; text-transform: uppercase; }
        .professional-summary { margin: 0 0 7px; color: #39474f; }
        .software-line { margin: 6px 0; font-family: DejaVu Sans Mono, monospace; font-size: 8.2pt; }
        .skill-item { display: inline-block; margin: 3px 4px 0 0; padding: 4px 7px; background: #e7eef1; color: #263943; font-size: 8pt; font-weight: 700; }
        .cv-item { display: table; width: 100%; margin-bottom: 8px; page-break-inside: avoid; }
        .item-period { display: table-cell; width: 20%; color: {{ $accent }}; font-size: 8pt; font-weight: 700; vertical-align: top; }
        .item-body { display: table-cell; width: 80%; vertical-align: top; }
        .item-title { margin: 0 0 1px; font-size: 9.5pt; }
        .item-subtitle { margin-bottom: 2px; color: #58656d; font-size: 8.3pt; }
        .item-description { margin: 0; color: #3e4b53; font-size: 8.4pt; }
        .language-item { margin-right: 12px; }
        .footer { position: fixed; right: 15mm; bottom: 5mm; color: #8b9499; font-size: 7pt; }
        .footer:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="footer">Page </div>
    <header class="top">
        <h1 class="name">{{ $fullName }}</h1>
        @if($targetTitle !== '')<div class="target">{{ $targetTitle }}</div>@endif
        <div class="contact">
            @if(!empty($email))<span>{{ $email }}</span>@endif
            @if(!empty($telephone))<span>{{ $telephone }}</span>@endif
            @if(!empty($location))<span>{{ $location }}</span>@endif
        </div>
    </header>
    <main class="body">
        @include('cv.partials.template-skills')
        @include('cv.partials.template-experiences')
        @include('cv.partials.template-education')
        @include('cv.partials.template-additional')
    </main>
</body>
</html>
