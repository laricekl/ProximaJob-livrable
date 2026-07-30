<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CV minimaliste - {{ $fullName }}</title>
    <style>
        @page { margin: {{ $density['margin'] }} 18mm; size: A4; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #20252a; font-family: {{ $fontFamily }}; font-size: {{ $density['font'] }}; line-height: {{ $density['line'] }}; }
        .header { margin-bottom: 22px; }
        .name { margin: 0; font-size: 26pt; font-weight: 400; letter-spacing: -.7px; }
        .target { margin-top: 5px; color: {{ $accent }}; font-size: 10pt; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; }
        .contact { margin-top: 12px; color: #657078; font-size: 8.7pt; }
        .contact span { margin-right: 14px; }
        .cv-section { margin-bottom: {{ $density['section'] }}; }
        .section-title { margin: 0 0 9px; color: {{ $accent }}; font-size: 9pt; letter-spacing: 2px; text-transform: uppercase; }
        .section-content { border-top: 1px solid #dfe3e5; padding-top: 9px; }
        .cv-item { display: table; width: 100%; margin-bottom: {{ $density['item'] }}; page-break-inside: avoid; }
        .item-period { display: table-cell; width: 20%; color: #7b858b; font-size: 8pt; vertical-align: top; }
        .item-body { display: table-cell; width: 80%; vertical-align: top; }
        .item-title { margin: 0 0 2px; font-size: 10pt; }
        .item-subtitle { margin-bottom: 3px; color: #586167; font-size: 8.7pt; }
        .item-description, .professional-summary { margin: 0; color: #465056; font-size: 8.8pt; }
        .software-line { margin: 7px 0; }
        .skill-item { display: inline-block; margin: 3px 5px 0 0; padding: 3px 7px; border: 1px solid #d9dfe2; font-size: 8pt; }
        .language-item { margin-right: 14px; white-space: nowrap; }
        .footer { position: fixed; right: 0; bottom: -8mm; color: #9aa1a5; font-size: 7pt; }
        .footer:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="footer">Page </div>
    <header class="header">
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
</body>
</html>
