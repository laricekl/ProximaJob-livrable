<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CV signature - {{ $fullName }}</title>
    <style>
        @page { margin: 12mm 15mm; size: A4; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #292929; font-family: {{ $fontFamily }}; font-size: 9.2pt; line-height: 1.32; }
        .header-table { width: 100%; margin-bottom: 16px; border-collapse: collapse; }
        .portrait-cell { width: 28mm; vertical-align: middle; }
        .portrait { width: 24mm; height: 24mm; overflow: hidden; border-radius: 50%; background: {{ $accent }}; color: #fff; text-align: center; font-size: 18pt; line-height: 24mm; font-weight: 700; }
        .portrait img { width: 24mm; height: 24mm; object-fit: cover; }
        .identity { padding-left: 5mm; vertical-align: middle; border-left: 1px solid #d7d0c8; }
        .name { margin: 0; color: #2e2925; font-family: DejaVu Serif, serif; font-size: 25pt; font-weight: 400; }
        .target { margin-top: 4px; color: {{ $accent }}; font-size: 9pt; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; }
        .contact { margin-top: 8px; color: #746d67; font-size: 8.2pt; }
        .contact span { margin-right: 12px; }
        .cv-section { margin-bottom: 12px; }
        .section-title { margin: 0 0 8px; padding-bottom: 4px; border-bottom: 1px solid #d9d1ca; color: {{ $accent }}; font-family: DejaVu Serif, serif; font-size: 11pt; font-weight: 400; letter-spacing: .6px; }
        .cv-item { display: table; width: 100%; margin-bottom: 7px; page-break-inside: avoid; }
        .item-period { display: table-cell; width: 21%; color: #8a817a; font-size: 8pt; vertical-align: top; }
        .item-body { display: table-cell; width: 79%; vertical-align: top; }
        .item-title { margin: 0; font-size: 9.6pt; }
        .item-subtitle { color: {{ $accent }}; font-size: 8.3pt; }
        .item-description, .professional-summary { margin: 3px 0 0; color: #4e4945; font-size: 8.5pt; }
        .software-line { margin: 7px 0; }
        .skill-item { display: inline-block; margin: 3px 4px 0 0; padding: 3px 7px; border: 1px solid #d9d1ca; font-size: 8pt; }
        .language-item { margin-right: 12px; }
        .footer { position: fixed; right: 0; bottom: -7mm; color: #aaa29c; font-size: 7pt; }
        .footer:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="footer">Page </div>
    <table class="header-table"><tr>
        <td class="portrait-cell"><div class="portrait">@if($includePhoto)<img src="{{ $photo_data }}" alt="">@else{{ $initials }}@endif</div></td>
        <td class="identity">
            <h1 class="name">{{ $fullName }}</h1>
            @if($targetTitle !== '')<div class="target">{{ $targetTitle }}</div>@endif
            <div class="contact">
                @if(!empty($email))<span>{{ $email }}</span>@endif
                @if(!empty($telephone))<span>{{ $telephone }}</span>@endif
                @if(!empty($location))<span>{{ $location }}</span>@endif
            </div>
        </td>
    </tr></table>
    @if($options['section_order'] === 'skills_first') @include('cv.partials.template-skills') @endif
    @include('cv.partials.template-experiences')
    @if($options['section_order'] === 'experience_first') @include('cv.partials.template-skills') @endif
    @include('cv.partials.template-education')
    @include('cv.partials.template-additional')
</body>
</html>
