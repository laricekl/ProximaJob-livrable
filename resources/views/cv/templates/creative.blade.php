<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CV créatif - {{ $fullName }}</title>
    <style>
        @page { margin: 0; size: A4; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #20232a; font-family: {{ $fontFamily }}; font-size: 9pt; line-height: 1.32; }
        .accent-bg { position: fixed; top: 0; left: 0; width: 38mm; height: 100%; background: {{ $accent }}; }
        .wrap { margin-left: 38mm; padding: 15mm 15mm 13mm 12mm; }
        .header { margin: -15mm -15mm 17px -12mm; padding: 17mm 15mm 11mm 12mm; background: #f0ebe5; }
        .eyebrow { color: {{ $accent }}; font-size: 8pt; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
        .name { margin: 5px 0 0; font-size: 28pt; line-height: 1; }
        .target { margin-top: 7px; color: #54515a; font-size: 10pt; }
        .contact { margin-top: 10px; color: #69656d; font-size: 8pt; }
        .contact span { margin-right: 10px; }
        .cv-section { margin-bottom: 13px; }
        .section-title { margin: 0 0 8px; color: {{ $accent }}; font-size: 11pt; text-transform: uppercase; letter-spacing: 1px; }
        .section-title:after { content: ""; display: block; width: 28px; margin-top: 4px; border-top: 3px solid {{ $accent }}; }
        .cv-item { display: table; width: 100%; margin-bottom: 8px; page-break-inside: avoid; }
        .item-period { display: table-cell; width: 21%; color: {{ $accent }}; font-size: 7.8pt; font-weight: 700; vertical-align: top; }
        .item-body { display: table-cell; width: 79%; vertical-align: top; }
        .item-title { margin: 0; font-size: 9.7pt; }
        .item-subtitle { color: #6a6670; font-size: 8.3pt; }
        .item-description, .professional-summary { margin: 3px 0 0; color: #48454e; font-size: 8.4pt; }
        .software-line { margin: 7px 0; }
        .skill-item { display: inline-block; margin: 3px 3px 0 0; padding: 3px 7px; color: #fff; background: {{ $accent }}; font-size: 7.8pt; }
        .language-item { margin-right: 12px; }
        .footer { position: fixed; right: 12mm; bottom: 5mm; color: #999; font-size: 7pt; }
        .footer:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="accent-bg"></div><div class="footer">Page </div>
    <main class="wrap">
        <header class="header">
            <div class="eyebrow">Curriculum vitae</div>
            <h1 class="name">{{ $fullName }}</h1>
            @if($targetTitle !== '')<div class="target">{{ $targetTitle }}</div>@endif
            <div class="contact">
                @if(!empty($email))<span>{{ $email }}</span>@endif
                @if(!empty($telephone))<span>{{ $telephone }}</span>@endif
                @if(!empty($location))<span>{{ $location }}</span>@endif
            </div>
        </header>
        @if($options['section_order'] === 'skills_first') @include('cv.partials.template-skills') @endif
        @include('cv.partials.template-experiences')
        @if($options['section_order'] === 'experience_first') @include('cv.partials.template-skills') @endif
        @include('cv.partials.template-education')
        @include('cv.partials.template-additional')
    </main>
</body>
</html>
