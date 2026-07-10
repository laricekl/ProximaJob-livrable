@extends('layouts.guest')
@section('title', 'Accueil')

@section('styles')
  <style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }

    .nav-link::after {
      content: '';
      position: absolute;
      width: 0;
      height: 1px;
      bottom: -4px;
      left: 50%;
      background: currentColor;
      transition: all .3s;
      transform: translateX(-50%);
    }

    .nav-link:hover::after {
      width: 100%;
    }

    .premium-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .premium-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    body {
      background-color: var(--pj-bg);
      background-image:
        url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n' x='0' y='0'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
      background-size: cover;
      background-position: center;
      position: relative;
    }

    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      background:
        linear-gradient(rgba(176, 177, 192, 0.4), rgba(240, 242, 245, 0.4)),
        radial-gradient(at 10% 10%, rgba(235, 132, 60, 0.06) 0px, transparent 40%),
        radial-gradient(at 90% 90%, rgba(36, 98, 183, 0.04) 0px, transparent 40%);
      pointer-events: none;
    }

    /* Subtle dot grid overlay */
    body::after {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      background-image: radial-gradient(var(--pj-grid-dot) 1px, transparent 1px);
      background-size: 32px 32px;
      pointer-events: none;
    }

    /* ── Ambient Floating Orbs ── */
    .ambient-orbs {
      position: fixed;
      top: 0; left: 0;
      width: 100vw; height: 100vh;
      z-index: -1;
      pointer-events: none;
      overflow: hidden;
    }
    .ambient-orb {
      position: absolute;
      border-radius: 50%;
      will-change: transform;
      animation: orb-float var(--dur, 30s) ease-in-out infinite;
      opacity: 0.82;
    }
    .ambient-orb--1 {
      width: 500px; height: 500px;
      background: radial-gradient(circle, rgba(var(--pj-accent-rgb),0.08) 0%, transparent 65%);
      top: -15%; left: -10%;
      --dur: 35s;
    }
    .ambient-orb--2 {
      width: 450px; height: 450px;
      background: radial-gradient(circle, rgba(var(--pj-info-rgb),0.06) 0%, transparent 65%);
      bottom: -10%; right: -8%;
      --dur: 38s;
    }
    @keyframes orb-float {
      0%   { transform: translate(0, 0) scale(1); }
      33%  { transform: translate(60px, -40px) scale(1.06); }
      66%  { transform: translate(-30px, 30px) scale(0.96); }
      100% { transform: translate(0, 0) scale(1); }
    }
    @media (prefers-reduced-motion: reduce) {
      .ambient-orb { animation: none; }
    }

    /* ── Hero Staggered Reveal ── */
    .hero-reveal {
      opacity: 0;
      transform: translateY(12px);
      animation: hero-fade-in 0.38s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .hero-reveal--1 { animation-delay: 0.02s; }
    .hero-reveal--2 { animation-delay: 0.08s; }
    .hero-reveal--3 { animation-delay: 0.12s; }
    .hero-reveal--4 { animation-delay: 0.16s; }
    .hero-reveal--5 { animation-delay: 0.2s; }
    @keyframes hero-fade-in {
      from { opacity: 0; transform: translateY(12px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Search Bar Focus Glow ── */
    .search-glass {
      transition: box-shadow 0.4s ease, border-color 0.4s ease;
    }
    .search-glass:focus-within {
      box-shadow: 0 0 0 4px rgba(var(--pj-accent-rgb),0.10), 0 20px 50px rgba(0,0,0,0.08);
      border-color: rgba(var(--pj-accent-rgb),0.25);
    }

    /* ── CTA Pulse ── */
    .cta-pulse {
      position: relative;
    }
    .cta-pulse::after {
      content: "";
      position: absolute;
      inset: -4px;
      border-radius: 16px;
      background: rgba(var(--pj-accent-rgb),0.12);
      z-index: -1;
      animation: cta-soft-pulse 2.6s ease-in-out infinite;
    }
    @keyframes cta-soft-pulse {
      0%, 100% { transform: scale(1); opacity: 0.4; }
      50%      { transform: scale(1.04); opacity: 0.1; }
    }

    /* ── Global Refinements ── */
    html {
      scroll-behavior: smooth;
    }
    body {
      overflow-x: hidden;
    }
    ::selection {
      background: rgba(var(--pj-accent-rgb),0.2);
      color: #191c1e;
    }

    /* ── Filter Panel ── */
    #filter-panel {
      overflow: hidden;
      transition: max-height 0.35s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.3s ease, margin 0.35s cubic-bezier(0.22, 1, 0.36, 1);
    }
    #filter-panel.hidden {
      max-height: 0;
      opacity: 0;
      margin-top: 0;
      pointer-events: none;
    }
    #filter-panel:not(.hidden) {
      max-height: 200px;
      opacity: 1;
      margin-top: 0.5rem;
    }

    .home-deferred {
      content-visibility: auto;
      contain-intrinsic-size: 900px;
    }

    @media (hover: hover) and (pointer: fine) {
      .spotlight-card-3d,
      .card-3d-inner {
        transition-duration: 0.38s;
      }

      .spotlight-scene:hover .spotlight-card-3d {
        transform: rotate3d(0.55, 0.75, 0, 14deg);
      }

      .card-3d-parent:hover .card-3d-inner {
        transform: rotate3d(0.55, 0.75, 0, 14deg);
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .hero-reveal,
      .cta-pulse::after {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
      }

      .spotlight-card-3d,
      .card-3d-inner,
      .spotlight-content,
      .spotlight-cta,
      #spotlight2-text {
        transition: none !important;
        transform: none !important;
      }
    }
  </style>
  <style>
    /* ── Pricing · One-card mobile ── */
    @media (max-width: 767px) {
      .pricing-grid {
        position: relative;
        border-radius: 16px;
        background: #fff;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        gap: 0;
        display: flex !important;
        flex-direction: row;
        grid-template-columns: none !important;
      }
      .card-3d-parent {
        position: relative;
        z-index: 2;
        height: auto;
        max-width: none;
        perspective: none;
        filter: none;
        margin: 0;
        flex: 1;
        min-width: 0;
      }
      .card-3d-parent + .card-3d-parent {
        border-left: 1px solid rgba(0,0,0,0.06);
        border-top: none;
      }
      .card-3d-inner {
        height: 100%;
        border-radius: 0;
        transform: none !important;
        box-shadow: none;
        background: transparent !important;
        display: flex;
        flex-direction: column;
      }
      .card-3d-logo,
      .card-3d-glass {
        display: none;
      }
      .card-3d-inner > .absolute {
        position: absolute;
        top: -4px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 11px;
        white-space: nowrap;
        display: block;
        text-align: center;
        z-index: 5;
        border-radius: 999px;
      }
      .card-3d-content {
        padding: 16px 10px 14px;
        transform: none !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        text-align: center;
        flex: 1;
      }
      .card-3d-content .text-4xl {
        font-size: 1.2rem;
      }
      .card-3d-content ul {
        display: block;
        margin: 2px 0;
        text-align: left;
        width: 100%;
      }
      .card-3d-content ul li {
        font-size: 12px;
        padding: 1px 0;
        display: flex;
        align-items: center;
        gap: 3px;
      }
      .card-3d-content ul .material-symbols-outlined {
        font-size: 11px;
      }
      .card-3d-content .mt-4,
      .card-3d-content .mt-5,
      .card-3d-content .mt-1 {
        margin: 0;
      }
      .card-3d-content a {
        margin: auto 0 0 0 !important;
        width: 100%;
        padding: 12px 16px;
        font-size: 13px;
        text-align: center;
        border-radius: 999px;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .card-3d-content > :first-child {
        font-weight: 700;
        font-size: 11px;
      }

      /* ── Spotlight mobile ── */
      .spotlight-scene {
        perspective: none;
        filter: none;
      }
      .spotlight-card-3d {
        transform: none !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        border-radius: 20px;
      }
      .spotlight-orbit-logo,
      .spotlight-glass {
        display: none;
      }
      .spotlight-content {
        flex-direction: column !important;
        gap: 12px !important;
        padding: 16px !important;
        min-height: auto !important;
        transform: none !important;
        align-items: flex-start !important;
      }
      .spotlight-content .flex.items-center.gap-6 {
        gap: 12px;
      }
      .spotlight-content .w-16 {
        width: 42px;
        height: 42px;
      }
      .spotlight-content .text-4xl {
        font-size: 24px;
      }
      #spotlight2-text {
        width: auto !important;
      }
      #spotlight2-text .space-y-1 > * {
        margin: 0;
      }
      #spotlight2-text h3 {
        font-size: 16px;
        line-height: 1.2;
      }
      #spotlight2-text .text-sm {
        font-size: 12px;
      }
      #spotlight2-text .text-\[10px\] {
        font-size: 12px;
      }
      .spotlight-content a {
        padding: 10px 20px;
        font-size: 11px;
        align-self: stretch;
        text-align: center;
        transform: none !important;
      }

      /* ── Categories mobile ── */
      .cat-grid-mobile {
        gap: 8px;
      }
      .cat-grid-mobile > a {
        padding: 10px 12px !important;
        border-radius: 12px;
        display: grid !important;
        grid-template-columns: 36px 1fr;
        gap: 1px 10px;
      }
      .cat-grid-mobile > a > :first-child {
        grid-row: 1 / 4;
        align-self: center;
      }
      .cat-grid-mobile .w-12 {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        margin-bottom: 0 !important;
        flex-shrink: 0;
      }
      .cat-grid-mobile h3 {
        font-size: 13px;
        margin-bottom: 0;
        line-height: 1.2;
      }
      .cat-grid-mobile .text-sm {
        font-size: 10px;
        line-height: 1.3;
      }
      .cat-grid-mobile .mb-4 {
        margin-bottom: 0;
      }
      .cat-grid-mobile .flex.items-center.gap-2 {
        font-size: 10px;
        gap: 4px;
      }

      /* ── Jobs mobile ── */
      .jobs-grid-mobile {
        gap: 7px;
      }
      .jobs-grid-mobile > a {
        padding: 10px 12px !important;
        border-radius: 12px;
        flex-direction: row !important;
        align-items: center !important;
        gap: 8px;
      }
      .jobs-grid-mobile .w-10 {
        width: 34px;
        height: 34px;
        border-radius: 8px;
      }
      .jobs-grid-mobile .text-xl {
        font-size: 17px;
      }
      .jobs-grid-mobile h4 {
        font-size: 13px;
      }
      .jobs-grid-mobile .text-xs {
        font-size: 10px;
      }
      .jobs-grid-mobile .mt-3 {
        margin-top: 0;
      }
      .jobs-grid-mobile .px-3 {
        padding: 2px 7px;
        font-size: 7px;
      }
      .jobs-grid-mobile .opacity-0 {
        display: none;
      }
      .jobs-grid-mobile .absolute.inset-0 {
        display: none;
      }
      .jobs-grid-mobile .flex.items-center.gap-4 {
        gap: 8px;
      }
        }
  </style>
@endsection

@section('content')
  <div class="ambient-orbs" aria-hidden="true">
    <div class="ambient-orb ambient-orb--1"></div>
    <div class="ambient-orb ambient-orb--2"></div>
  </div>

  <main class="flex-grow pt-24 md:pt-32">
    <section class="pt-8 md:pt-16 pb-16 md:pb-20 px-4 md:px-10 relative overflow-visible">
      <div class="max-w-5xl mx-auto text-center mt-6 md:mt-10 relative">

        <!-- MASCOTTE en Absolute Overlay -->
        <div class="absolute top-20 -left-32 z-30 pointer-events-none hidden lg:block">
          <svg height="200" id="robot-svg" viewbox="0 0 320 320" width="200" xmlns="http://www.w3.org/2000/svg"
            style="filter: drop-shadow(0 40px 70px rgba(0,0,0,0.15)); will-change: transform;">
            <defs>
              <!-- ── Clips yeux ── -->
              <clipPath id="cl">
                <circle cx="30" cy="30" r="25"></circle>
              </clipPath>
              <clipPath id="cr">
                <circle cx="30" cy="30" r="25"></circle>
              </clipPath>
              <!-- ── Chassis gradient ── -->
              <radialGradient cx="36%" cy="28%" id="chassisGrad" r="68%">
                <stop offset="0%" stop-color="#ffffff"></stop>
                <stop offset="70%" stop-color="#e2e5ee"></stop>
                <stop offset="100%" stop-color="#cdd1de"></stop>
              </radialGradient>
              <!-- ── Faceplate gradient ── aux couleurs de ProximaJob (Bleu nuit profond) -->
              <radialGradient cx="50%" cy="30%" id="fpGrad" r="70%">
                <stop offset="0%" stop-color="#0b1b36"></stop>
                <stop offset="100%" stop-color="#020814"></stop>
              </radialGradient>
              <!-- ── Sclère gradient ── moins blanc, plus organique -->
              <radialGradient cx="42%" cy="32%" id="scleraGrad" r="65%">
                <stop offset="0%" stop-color="#e8eaf0"></stop>
                <stop offset="60%" stop-color="#d8dbe8"></stop>
                <stop offset="100%" stop-color="#c8ccd8"></stop>
              </radialGradient>
              <!-- ── Iris gradient ── Bleu vibrant ProximaJob -->
              <radialGradient cx="38%" cy="34%" id="irisGrad" r="62%">
                <stop offset="0%" stop-color="#6bb5f5"></stop>
                <stop offset="40%" stop-color="#2a85d6"></stop>
                <stop offset="100%" stop-color="#0a65c0"></stop>
              </radialGradient>
              <!-- ── Ombre contact sous paupière ── -->
              <radialGradient cx="50%" cy="0%" id="lidShadow" r="100%">
                <stop offset="0%" stop-color="#000000" stop-opacity="0.45"></stop>
                <stop offset="100%" stop-color="#000000" stop-opacity="0"></stop>
              </radialGradient>
              <!-- ── Ombre orbite ── cercle d'occlusion autour de l'oeil -->
              <radialGradient cx="50%" cy="50%" id="orbitShadow" r="50%">
                <stop offset="72%" stop-color="#000000" stop-opacity="0"></stop>
                <stop offset="100%" stop-color="#000000" stop-opacity="0.35"></stop>
              </radialGradient>
              <!-- ── Glow antenne ── -->
              <filter height="400%" id="antennaGlow" width="400%" x="-150%" y="-150%">
                <feGaussianBlur result="g" stdDeviation="3.5"></feGaussianBlur>
                <feMerge>
                  <feMergeNode in="g"></feMergeNode>
                  <feMergeNode in="SourceGraphic"></feMergeNode>
                </feMerge>
              </filter>
              <!-- ── Glitch ── -->
              <filter id="glitch" width="110%" x="-5%">
                <feColorMatrix result="r" type="matrix" values="1 0 0 0 0  0 0 0 0 0  0 0 0 1 0">
                </feColorMatrix>
                <feOffset dx="4" in="r" result="rOff"></feOffset>
                <feColorMatrix in="SourceGraphic" result="gb" type="matrix"
                  values="0 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 1 0"></feColorMatrix>
                <feMerge>
                  <feMergeNode in="rOff"></feMergeNode>
                  <feMergeNode in="gb"></feMergeNode>
                </feMerge>
              </filter>
              <!-- ── Stroke chassis dégradé (Couleur Antenne) ── -->
              <linearGradient id="strokeGrad" x1="0" x2="0" y1="0" y2="1">
                <stop offset="0%" stop-color="#FFAD77"></stop>
                <stop offset="100%" stop-color="var(--pj-accent)"></stop>
              </linearGradient>
              <!-- ── Micro-texture faceplate (bruit très subtil) ── -->
              <filter height="100%" id="noise" width="100%" x="0%" y="0%">
                <feTurbulence baseFrequency="0.65" numOctaves="3" result="n" stitchTiles="stitch" type="fractalNoise">
                </feTurbulence>
                <feColorMatrix in="n" result="ng" type="saturate" values="0"></feColorMatrix>
                <feBlend in="SourceGraphic" in2="ng" mode="overlay" result="b"></feBlend>
                <feComposite in="b" in2="SourceGraphic" operator="in"></feComposite>
              </filter>
            </defs>
            <!-- ══ CHASSIS ══════════════════════════════════════════════ -->
            <rect fill="url(#chassisGrad)" height="236" rx="56" stroke="url(#strokeGrad)" stroke-width="1.5" width="236"
              x="42" y="44"></rect>
            <!-- Reflet haut-gauche dynamique -->
            <ellipse id="chassis-ref" cx="108" cy="70" fill="white" opacity="0.16" rx="50" ry="20"></ellipse>
            <!-- ══ ANTENNE ════════════════════════════════════════════════ -->
            <rect fill="#d2d6e2" height="26" rx="3" stroke="#bfc4d4" stroke-width="1" width="16" x="152" y="18"></rect>
            <circle cx="160" cy="13" fill="var(--pj-accent)" filter="url(#antennaGlow)" id="antenna-tip" r="6"></circle>
            <circle cx="160" cy="13" fill="none" id="antenna-ring" opacity="0" r="11" stroke="var(--pj-accent)" stroke-width="1">
            </circle>
            <!-- ══ FACEPLATE ══════════════════════════════════════════════ -->
            <rect fill="url(#fpGrad)" height="126" id="faceplate" rx="28" stroke="#0a152b" stroke-width="1.2"
              width="196" x="62" y="84"></rect>
            <!-- Micro-texture overlay -->
            <rect fill="#ffffff" filter="url(#noise)" height="126" opacity="0.012" rx="28" width="196" x="62" y="84">
            </rect>
            <!-- Occlusion bords internes faceplate -->
            <rect fill="none" height="126" rx="28" stroke="#000000" stroke-opacity="0.18" stroke-width="6" width="196"
              x="62" y="84"></rect>
            <!-- Légère variation lumineuse locale autour des yeux -->
            <ellipse cx="160" cy="148" fill="#0a0c14" opacity="0.3" rx="90" ry="45"></ellipse>
            <!-- ══ GROUPE PARALLAXE VISAGE ════════════════════════════════ -->
            <g id="face-elements" style="will-change: transform; transition: transform 0.1s ease-out;">
              <!-- ══ SOURCILS ═══════════════════════════════════════════════ -->
              <path d="M 94 110 Q 116 104 138 110" fill="none" id="brow-left" opacity="0.38" stroke="#ffffff"
                stroke-linecap="round" stroke-width="1.8"></path>
              <path d="M 94 111 Q 116 105 138 111" fill="none" opacity="0.2" stroke="#000000" stroke-linecap="round"
                stroke-width="2.5"></path>
              <path d="M 182 110 Q 204 104 226 110" fill="none" id="brow-right" opacity="0.38" stroke="#ffffff"
                stroke-linecap="round" stroke-width="1.8"></path>
              <path d="M 182 111 Q 204 105 226 111" fill="none" opacity="0.2" stroke="#000000" stroke-linecap="round"
                stroke-width="2.5"></path>
              <!-- ══ OEIL GAUCHE ════════════════════════════════════════════ -->
              <g id="left-eye" transform="translate(91, 114)">
                <circle cx="30" cy="30" fill="url(#orbitShadow)" opacity="0.9" r="25"></circle>
                <circle cx="30" cy="30" fill="url(#scleraGrad)" r="25"></circle>
                <circle cx="30" cy="30" fill="url(#irisGrad)" id="iris-left" r="15"></circle>
                <circle cx="30" cy="30" fill="#05070a" id="pupil-left" r="9"></circle>
                <circle cx="36" cy="22" fill="white" id="ref1-l" opacity="0.55" r="4"></circle>
                <circle cx="36" cy="22" fill="white" id="ref1-l-blur" opacity="0.12" r="6"></circle>
                <circle cx="24" cy="37" fill="white" id="ref2-l" opacity="0.18" r="1.8"></circle>
                <rect fill="url(#lidShadow)" height="18" opacity="0.8" width="50" x="5" y="5"></rect>
                <circle cx="30" cy="30" fill="url(#orbitShadow)" r="25"></circle>
                <g clip-path="url(#cl)">
                  <path d="M 5 5 Q 30 -30 55 5 L 55 26 Q 30 30 5 26 Z" fill="#0e1118" id="eyelid-left"></path>
                </g>
              </g>
              <!-- ══ OEIL DROIT ═════════════════════════════════════════════ -->
              <g id="right-eye" transform="translate(171, 114)">
                <circle cx="30" cy="30" fill="url(#orbitShadow)" opacity="0.9" r="25"></circle>
                <circle cx="30" cy="30" fill="url(#scleraGrad)" r="25"></circle>
                <circle cx="30" cy="30" fill="url(#irisGrad)" id="iris-right" r="15"></circle>
                <circle cx="30" cy="30" fill="#05070a" id="pupil-right" r="9"></circle>
                <circle cx="36" cy="22" fill="white" id="ref1-r" opacity="0.55" r="4"></circle>
                <circle cx="36" cy="22" fill="white" id="ref1-r-blur" opacity="0.12" r="6"></circle>
                <circle cx="24" cy="37" fill="white" id="ref2-r" opacity="0.18" r="1.8"></circle>
                <rect fill="url(#lidShadow)" height="18" opacity="0.8" width="50" x="5" y="5"></rect>
                <circle cx="30" cy="30" fill="url(#orbitShadow)" r="25"></circle>
                <g clip-path="url(#cr)">
                  <path d="M 5 5 Q 30 -30 55 5 L 55 26 Q 30 30 5 26 Z" fill="#0e1118" id="eyelid-right"></path>
                </g>
              </g>
              <!-- ══ BOUCHE ════════════════════════════════════════════════ -->
              <path d="M 136 197 Q 160 205 184 197" fill="none" opacity="0.3" stroke="#000000" stroke-linecap="round"
                stroke-width="2.5"></path>
              <path d="M 136 196 Q 160 204 184 196" fill="none" id="mouth" opacity="0.45" stroke="#c8ccd8"
                stroke-linecap="round" stroke-width="1.4"></path>
              <!-- ══ CAPTEURS JOUES ═════════════════════════════════════════ -->
              <circle cx="80" cy="184" fill="none" id="cheek-l" opacity="0.35" r="3.5" stroke="#b0b4c4"
                stroke-width="1">
              </circle>
              <circle cx="240" cy="184" fill="none" id="cheek-r" opacity="0.35" r="3.5" stroke="#b0b4c4"
                stroke-width="1">
              </circle>
            </g>
            <!-- ══ LOGO PROXIMAJOB (Vectoriel Haute Fidélité) ════════════════════════════ -->
            <g id="brand-logo">
              <g transform="translate(62, 228) scale(0.045)">
                <path fill="var(--pj-accent)" opacity="1.000000" stroke="none"
                  d=" M880.065308,413.355713 C878.680298,422.513702 877.891785,431.810883 875.793884,440.802551 C870.387085,463.975372 862.498901,486.346313 851.941040,507.700775 C834.946411,542.074158 811.925720,571.841492 783.885132,598.010986 C752.048523,627.723328 715.671997,649.717041 674.828308,664.410889 C653.523315,672.075562 631.562988,677.062805 609.047485,679.933472 C583.472839,683.194214 557.904907,683.669067 532.352295,680.797119 C504.046814,677.615723 476.243958,671.654602 449.843597,660.789124 C434.403503,654.434448 419.648499,646.415222 404.529144,638.540771 C406.108856,635.856567 407.744690,633.776611 409.701111,631.567505 C414.702820,626.689453 419.383942,621.940491 424.381775,617.073364 C429.657715,612.119080 434.684998,607.350464 439.556519,602.427612 C444.186493,597.748901 448.653595,592.909058 453.138550,588.110962 C453.084778,588.081970 453.136230,588.192810 453.462891,588.114502 C455.943512,585.759277 458.097443,583.482361 460.178223,581.156372 C460.105103,581.107300 460.203156,581.253540 460.527344,581.169556 C465.992767,575.796997 471.134003,570.508362 476.296936,565.217773 C476.318634,565.215881 476.346588,565.251099 476.664185,565.157654 C479.404663,562.875732 481.827515,560.687195 484.250366,558.498657 C493.928040,562.630371 503.605713,566.762024 513.681824,571.372803 C516.433960,572.798706 518.755432,573.838318 521.149109,574.669983 C524.511047,575.838196 527.895874,576.975891 531.344055,577.831970 C532.076233,578.013672 533.123291,576.927002 534.025269,576.424744 C534.462036,576.417542 534.898865,576.410339 535.652161,576.820374 C536.294983,577.817627 536.621216,578.397705 537.222717,579.261597 C539.994080,579.374390 542.490173,579.203308 545.002991,578.835571 C545.019653,578.638855 545.028137,578.244202 545.028137,578.244202 C545.463684,578.227539 545.899292,578.210815 546.647339,578.575073 C546.973145,579.295532 546.986572,579.635071 547.089844,580.371521 C547.840027,581.168701 548.498535,581.915161 549.160767,581.918335 C564.147888,581.991028 579.135681,582.018555 594.122131,581.904297 C595.082031,581.896973 596.032532,580.674316 597.205078,579.892090 C597.631836,579.329407 597.841064,578.891602 598.050232,578.453796 C598.473328,578.350098 598.896362,578.246399 599.652832,578.402588 C599.986267,578.662476 600.029968,579.059998 600.302795,579.342163 C603.400574,579.408997 606.225586,579.193604 609.307129,578.791626 C613.902649,577.715271 618.278931,576.968628 622.557678,575.849304 C623.853333,575.510376 624.879150,574.139893 626.028503,573.241821 C626.439819,573.091370 626.851135,572.940918 627.805603,573.074097 C631.268433,574.462524 633.353149,573.314819 634.969482,570.903503 C635.291504,570.816223 635.613525,570.728882 636.561646,570.754272 C638.860168,569.374756 642.840515,573.109375 643.040649,568.282593 C643.421570,568.069458 643.802551,567.856262 644.690063,567.907227 C646.148926,567.584717 647.101196,566.997925 648.053406,566.411194 C648.439270,566.192627 648.825073,565.974060 649.736755,566.009644 C651.190552,565.627380 652.118530,564.990845 653.046509,564.354248 C653.446960,564.167603 653.847412,563.980957 654.750244,564.045410 C657.195129,563.705444 660.077271,565.003357 660.631409,561.621033 C660.631409,561.621033 660.617859,561.812378 661.054565,561.830200 C665.332397,559.592590 669.173462,557.337158 673.014465,555.081726 C694.360901,543.345581 714.030457,529.396301 730.965820,511.733551 C754.440735,487.250305 771.444031,458.931915 782.082458,426.734070 C787.999390,408.826141 791.022461,390.394562 793.032471,371.548035 C793.803345,372.609192 794.105774,373.790009 794.408203,374.970795 C794.601135,374.940308 794.794128,374.909790 794.987122,374.879303 C794.987122,373.925842 794.987061,372.972412 794.987122,372.018951 C794.989685,304.842560 794.992432,237.666199 794.992615,170.489822 C794.992676,169.493668 795.128113,168.446655 794.851440,167.528549 C794.752747,167.201294 793.617310,167.186539 792.955139,167.029129 C790.988708,163.209885 787.663879,162.466965 783.742065,162.790039 C781.773438,162.952240 779.773987,162.740967 777.195801,162.202698 C775.509583,161.469925 774.416382,161.027512 773.322571,161.026138 C744.528931,160.990051 715.735229,160.981903 686.941772,161.059372 C685.653503,161.062836 684.367432,161.914902 683.080383,162.371689 C681.270264,162.525482 679.459900,162.815018 677.649963,162.812897 C645.836182,162.775742 614.021973,162.775269 582.208862,162.597855 C571.392029,162.537521 560.576843,161.719696 549.762390,161.750778 C508.536133,161.869278 471.945374,175.230560 439.454712,200.474136 C410.409058,223.041183 388.973328,251.464783 374.676666,285.123444 C361.961151,315.059631 356.563538,346.465637 357.776215,379.092163 C358.939667,410.394775 367.024048,439.668518 381.819458,467.144073 C383.468781,470.206940 383.375214,472.040558 380.889893,474.478424 C356.876923,498.033325 332.968109,521.694458 309.015594,545.311035 C308.228638,546.086975 307.292938,546.711975 305.817810,547.896057 C301.115997,540.265686 296.462616,533.059204 292.151123,525.653625 C278.038757,501.414093 267.518799,475.612762 260.612396,448.491760 C251.167099,411.400635 248.704590,373.748138 252.638809,335.581848 C255.054794,312.144318 259.810394,289.297699 267.478119,267.107849 C278.792816,234.363953 295.118469,204.247772 316.581451,176.991043 C347.456390,137.781769 385.311615,107.197266 430.343109,85.670685 C456.411469,73.209106 483.852844,64.765266 512.334778,60.046471 C543.538391,54.876770 574.910645,54.318916 606.383545,57.751438 C650.501221,62.563026 691.470520,76.761200 729.753296,98.893661 C763.145508,118.198715 791.452637,143.544601 815.609131,173.453262 C840.508728,204.281921 859.096436,238.687881 870.377075,276.703308 C874.750183,291.440582 877.039490,306.796173 880.219604,322.580109 C880.353455,323.804535 880.545776,324.318970 880.738037,324.833405 C880.911438,325.296295 881.084900,325.759186 881.002380,326.702087 C880.864136,327.469879 880.981812,327.757660 881.099548,328.045410 C881.343323,329.882172 881.587097,331.718903 881.614868,334.059814 C881.511292,334.955383 881.623657,335.346802 881.735962,335.738220 C882.095337,336.951935 882.454834,338.165649 882.583191,340.000305 C882.468506,341.024048 882.584961,341.426819 882.701416,341.829590 C882.876587,342.615479 883.051697,343.401367 882.826904,344.878967 C882.303101,360.146637 882.168518,374.722473 882.095215,389.298676 C882.092407,389.862000 882.677917,390.428314 882.989258,390.993256 C883.027649,391.793671 883.066040,392.594086 882.806763,393.893616 C882.510559,394.837067 882.512207,395.281342 882.513794,395.725647 C882.485229,396.664062 882.456726,397.602509 881.984985,399.034424 C881.545349,400.251740 881.549011,400.975555 881.552673,401.699371 C881.573730,403.241058 881.594727,404.782745 881.180969,406.817505 C880.853088,407.887939 880.960022,408.465363 881.066956,409.042755 C881.095581,409.538727 881.124268,410.034698 880.788757,411.086731 C880.304871,412.213776 880.185120,412.784729 880.065308,413.355713 " />
                <path fill="#2462B7" opacity="1.000000" stroke="none"
                  d=" M409.380493,631.696655 C407.744690,633.776611 406.108856,635.856567 404.275269,638.224915 C397.229065,634.229858 390.162659,630.255676 383.571075,625.608032 C358.678711,608.056763 336.482971,587.600769 317.815552,563.440796 C317.418304,562.926636 317.108429,562.344971 316.589264,561.530396 C324.816101,553.282715 332.994934,545.090637 341.165649,536.890503 C399.571259,478.274933 457.967529,419.650024 516.383789,361.045074 C537.554504,339.806030 558.766052,318.607849 579.971191,297.403198 C586.142029,291.232483 586.108521,287.637695 579.870605,281.434601 C564.413269,266.063477 548.895264,250.751709 533.581299,235.238754 C525.128235,226.675919 527.123596,215.609192 537.756287,210.829605 C539.354919,210.110962 541.279480,209.831406 543.053589,209.830292 C611.547485,209.787094 680.041382,209.799713 748.535278,209.810562 C749.194641,209.810669 749.854004,209.885941 751.042847,209.958389 C751.146912,211.372406 751.340942,212.786880 751.342224,214.201538 C751.399963,279.695496 751.340515,345.189911 751.616272,410.682922 C751.649170,418.497559 748.935425,423.932434 742.155518,427.101624 C734.981689,430.454987 728.341797,428.541260 722.922607,423.172546 C708.615173,408.998230 694.500732,394.629150 680.294678,380.352386 C673.452942,373.476562 670.121033,373.491943 663.235718,380.328369 C604.128174,439.016327 545.018616,497.702148 485.917603,556.396667 C485.451660,556.859436 485.095428,557.432617 484.469177,558.226440 C481.827515,560.687195 479.404663,562.875732 476.567871,564.796814 C476.463989,561.984314 476.774078,559.439209 476.953339,557.967957 C480.736145,555.546753 484.801727,553.721436 487.864227,550.832581 C495.699158,543.441895 502.971741,535.459167 510.682404,527.931580 C521.584595,517.288208 532.794006,506.958557 543.649841,496.269104 C550.899170,489.130890 557.610168,481.447388 564.827820,474.275299 C582.326416,456.887207 599.885010,439.557037 617.599304,422.389160 C634.099182,406.398315 649.012024,388.789124 667.199097,374.482361 670.855957,371.605621 673.030518,370.863434 676.277344,373.835327 C681.052917,378.206665 686.241455,382.238708 690.402161,387.138641 C694.591492,392.072388 700.378967,395.033417 704.306641,400.402557 C708.672363,406.370514 714.557922,411.243256 719.890625,416.481964 C724.050659,420.568542 727.668335,424.835297 734.697327,424.998077 C743.278381,425.196747 748.940491,420.936737 749.291260,411.752197 C749.713196,398.178741 749.844788,384.902924 749.911377,371.626801 C749.914124,371.070435 749.310364,370.511047 748.989990,369.514282 C748.990112,366.733490 748.989441,364.391510 749.290771,361.751831 C749.734924,360.645935 750.000427,359.837921 750.001343,359.029572 C750.022949,339.894104 750.034912,320.758575 749.970459,301.623291 C749.966370,300.397919 749.330688,299.174683 748.989746,297.529114 C748.989990,295.421936 748.989380,293.736115 749.286865,291.756256 C749.386230,287.291290 749.187500,283.120300 748.987976,278.724792 C748.987244,278.500244 748.988708,278.051178 749.289062,277.752197 C749.708435,265.511017 749.840515,253.568863 749.905029,241.626343 C749.908020,241.069046 749.307922,240.508499 748.989563,239.518341 C748.989868,237.075241 748.989258,235.063293 749.288940,232.752609 C749.388977,226.284866 749.188721,220.115860 748.881348,213.701935 C748.513428,212.991501 748.252563,212.525955 748.096741,211.769592 C747.093872,211.327209 745.986023,211.043381 744.878052,211.042999 C678.138367,211.020187 611.398621,211.013794 544.658936,211.061539 C543.102905,211.062653 541.547302,211.697769 539.928772,212.386276 C531.935852,217.191681 531.937256,217.191391 533.916443,226.600510 C533.984253,226.922974 534.041748,227.279129 533.976807,227.593246 C533.043457,232.103165 536.023376,234.559769 538.813049,237.271027 C547.882874,246.086044 556.959717,254.899429 565.813477,263.929504 C573.554443,271.824646 580.997864,280.011719 589.633789,289.198578 C580.997864,280.011719 573.554443,271.824646 565.813477,263.929504 C556.959717,254.899429 547.882874,246.086044 538.813049,237.271027 C536.023376,234.559769 533.043457,232.103165 533.976807,227.593246 C534.041748,227.279129 533.984253,226.922974 533.916443,226.600510 C531.937256,217.191391 531.935852,217.191681 540.398438,212.379333 C609.951050,212.035660 678.971375,212.048035 747.991699,212.060425 z" />
                <path fill="#E8790E" opacity="1.000000" stroke="none"
                  d=" M792.917542,167.494080 C793.617310,167.186539 794.752747,167.201294 794.851440,167.528549 C795.128113,168.446655 794.992676,169.493668 794.992615,170.489822 C794.992432,237.666199 794.989685,304.842560 794.987122,372.018951 C794.987061,372.972412 794.987122,373.925842 794.987122,374.879303 C794.794128,374.909790 794.601135,374.940308 794.408203,374.970795 C794.105774,373.790009 793.803345,372.609192 793.230164,371.199615 C792.923340,370.533417 792.887268,370.095978 793.099609,369.251953 C793.408813,368.732330 793.549500,368.563141 793.516418,368.516174 C793.378113,368.319519 793.187683,368.159546 793.016418,367.986084 C792.964355,326.585297 792.898804,285.184479 792.865173,243.783691 C792.844666,218.508820 792.873352,193.233932 792.917542,167.494080 z" />
                <path fill="var(--pj-accent)" opacity="1.000000" stroke="none"
                  d=" M606.929199,578.773376 C606.853699,578.931213 606.676453,578.939270 606.233765,578.926758 C606.254761,578.812012 606.541138,578.717834 606.929199,578.773376 z" />
                <path fill="var(--pj-accent)" opacity="1.000000" stroke="none"
                  d=" M539.924805,578.766968 C539.854736,578.928040 539.678589,578.941467 539.237305,578.945190 C539.254395,578.830200 539.536560,578.724731 539.924805,578.766968 z" />
                <path fill="#2A64B9" opacity="1.000000" stroke="none"
                  d=" M450.469269,589.451721 C450.523682,589.419556 450.414856,589.483887 450.469269,589.451721 z" />
              </g>
              <text x="112" y="251" font-family="'Inter', sans-serif" font-weight="700" font-size="20" fill="#023467"
                letter-spacing="-0.3">ProximaJob</text>
            </g>
            <!-- ══ DÉTAILS BAS ════════════════════════════════════════════ -->
            <rect fill="#c0c4d4" height="2.5" opacity="0.35" rx="1.2" width="46" x="137" y="226"></rect>
            <rect fill="#c0c4d4" height="2" opacity="0.2" rx="1" width="38" x="141" y="231"></rect>
            <rect fill="#c0c4d4" height="1.5" opacity="0.12" rx="0.7" width="28" x="146" y="236"></rect>
          </svg>
        </div>

        <h1 class="hero-reveal hero-reveal--1 text-4xl md:text-6xl font-bold font-serif text-primary leading-tight tracking-tight">
          Votre recherche d'emploi,<br />
          <span class="text-secondary-container">propulsée par l'IA.</span>
        </h1>
        <p class="hero-reveal hero-reveal--2 text-base md:text-lg text-on-surface-variant max-w-2xl mx-auto mt-4 md:mt-6 leading-relaxed">
          Déposez votre CV et laissez ProximaJob analyser votre profil pour vous connecter aux meilleures opportunités
          adaptées à vos compétences.
        </p>

        <!-- Search Bar -->
        <div class="hero-reveal hero-reveal--3 mt-6 md:mt-10 max-w-5xl mx-auto search-glass relative z-10">

          <!-- MOBILE: Compact single-row -->
          <form method="GET" action="{{ route('offres') }}" class="flex md:hidden items-center gap-1 bg-white/80 backdrop-blur-xl rounded-2xl shadow-2xl p-1.5 border border-white/50">
            <div class="flex-1 flex items-center min-w-0 px-3 py-1.5">
              <span class="material-symbols-outlined text-outline mr-2 text-sm flex-shrink-0">search</span>
              <input name="search" value="{{ request('search') }}" class="w-full bg-transparent border-none focus:ring-0 text-on-surface placeholder-outline text-sm" placeholder="Titre du poste, mots-clés..." type="text" />
            </div>
            <button id="filter-toggle" type="button" class="w-10 h-10 flex items-center justify-center rounded-xl text-outline hover:text-secondary-container hover:bg-secondary-container/5 transition-colors flex-shrink-0 min-w-[44px] min-h-[44px]" aria-label="Filtres">
              <span class="material-symbols-outlined text-xl">tune</span>
            </button>
            <button type="submit" class="bg-secondary-container text-white font-bold px-4 py-2.5 rounded-xl hover:bg-secondary transition-all shadow flex items-center justify-center gap-1 cta-pulse text-sm flex-shrink-0 min-w-[44px] min-h-[44px]">
              <span class="material-symbols-outlined text-lg">arrow_forward</span>
            </button>
          </form>

          <!-- MOBILE: Filter panel -->
          <form method="GET" action="{{ route('offres') }}" id="filter-panel" class="md:hidden bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl p-3 border border-white/50 hidden">
            <div class="flex items-center px-3 py-2 mb-2 border-b border-outline-variant/20">
              <span class="material-symbols-outlined text-outline mr-2 text-sm">location_on</span>
              <input name="localisation" value="{{ request('localisation') }}" class="w-full bg-transparent border-none focus:ring-0 text-on-surface placeholder-outline text-sm" placeholder="Localisation" type="text" />
            </div>
            <div class="flex items-center px-3 py-2">
              <span class="material-symbols-outlined text-outline mr-2 text-sm">category</span>
              <select name="categories[]" class="w-full bg-transparent border-none focus:ring-0 text-on-surface text-sm">
                <option value="">Toutes les catégories</option>
                @foreach ($categoriesWithCount as $category)
                  <option value="{{ $category->id }}" @selected(in_array($category->id, (array) request('categories', [])))>{{ $category->nom }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="mt-3 w-full bg-secondary-container text-white font-bold px-4 py-3 rounded-xl hover:bg-secondary transition-all shadow text-sm">Rechercher</button>
          </form>

          <!-- DESKTOP: Multi-field row -->
          <form method="GET" action="{{ route('offres') }}" class="hidden md:flex items-center gap-2 bg-white/80 backdrop-blur-xl rounded-2xl shadow-2xl p-2 border border-white/50">
            <div class="flex-1 flex items-center px-4 py-2 border-r border-outline-variant/30">
              <span class="material-symbols-outlined text-outline mr-3 text-base">search</span>
              <input name="search" value="{{ request('search') }}" class="w-full bg-transparent border-none focus:ring-0 text-on-surface placeholder-outline text-base" placeholder="Titre du poste, mots-clés..." type="text" />
            </div>
            <div class="flex-1 flex items-center px-4 py-2 border-r border-outline-variant/30">
              <span class="material-symbols-outlined text-outline mr-3 text-base">location_on</span>
              <input name="localisation" value="{{ request('localisation') }}" class="w-full bg-transparent border-none focus:ring-0 text-on-surface placeholder-outline text-base" placeholder="Localisation" type="text" />
            </div>
            <div class="flex-1 flex items-center px-4 py-2">
              <span class="material-symbols-outlined text-outline mr-3 text-base">category</span>
              <select name="categories[]" class="w-full bg-transparent border-none focus:ring-0 text-on-surface text-base">
                <option value="">Toutes les catégories</option>
                @foreach ($categoriesWithCount as $category)
                  <option value="{{ $category->id }}" @selected(in_array($category->id, (array) request('categories', [])))>{{ $category->nom }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="bg-secondary-container text-white font-bold px-8 py-4 rounded-xl hover:bg-secondary transition-all shadow flex items-center justify-center gap-2 cta-pulse text-base flex-shrink-0">
              Rechercher <span class="material-symbols-outlined text-lg">arrow_forward</span>
            </button>
          </form>
        </div>

        <!-- Stats -->
        <div class="hero-reveal hero-reveal--4 mt-6 md:mt-10 max-w-xl mx-auto grid grid-cols-3 gap-2 md:gap-4">
          <div class="bg-white/60 backdrop-blur-sm rounded-2xl px-3 md:px-6 py-3 md:py-4 text-center border border-white/50 shadow-sm">
            <p class="text-xl md:text-3xl font-bold text-primary font-serif">1 200<span class="text-secondary-container">+</span></p>
            <p class="text-[10px] md:text-xs text-on-surface-variant/60 uppercase tracking-widest mt-1">Emplois</p>
          </div>
          <div class="bg-white/60 backdrop-blur-sm rounded-2xl px-3 md:px-6 py-3 md:py-4 text-center border border-white/50 shadow-sm">
            <p class="text-xl md:text-3xl font-bold text-primary font-serif">32<span class="text-secondary-container">k</span></p>
            <p class="text-[10px] md:text-xs text-on-surface-variant/60 uppercase tracking-widest mt-1">Utilisateurs</p>
          </div>
          <div class="bg-white/60 backdrop-blur-sm rounded-2xl px-3 md:px-6 py-3 md:py-4 text-center border border-white/50 shadow-sm">
            <p class="text-xl md:text-3xl font-bold text-primary font-serif">170<span class="text-secondary-container">+</span></p>
            <p class="text-[10px] md:text-xs text-on-surface-variant/60 uppercase tracking-widest mt-1">Entreprises</p>
          </div>
        </div>

        <!-- SPOTLIGHT PARTENAIRES · Slide -->
        <div class="hero-reveal hero-reveal--5 mt-6 max-w-4xl mx-auto spotlight-scene">
          <div class="relative group">
            <div class="absolute -inset-4 bg-gradient-to-r from-secondary-container/30 via-secondary/20 to-primary/10 rounded-[2.5rem] blur-2xl opacity-30 group-hover:opacity-60 transition duration-1000"></div>

            <div class="rounded-[2rem] relative spotlight-card-3d" style="background: linear-gradient(135deg, #e0dbd2, #cec7b8);">

              <div class="spotlight-orbit-logo" aria-hidden="true">
                <span class="spotlight-orbit spotlight-orbit--1"></span>
                <span class="spotlight-orbit spotlight-orbit--2"></span>
                <span class="spotlight-orbit spotlight-orbit--3"></span>
              </div>

              <div class="spotlight-glass"></div>

              <div class="spotlight-content flex flex-col md:flex-row items-center justify-between gap-8 p-6 md:p-10 w-full overflow-hidden" style="min-height: 160px;">
                <div class="flex items-center gap-6 text-left flex-1">
                  <div class="w-16 h-16 md:w-20 md:h-20 bg-white rounded-2xl shadow-inner flex items-center justify-center p-3 border border-primary/5 flex-shrink-0" id="spotlight2-logo-container">
                    <span class="material-symbols-outlined text-4xl text-secondary-container/30 transition-opacity duration-300" id="spotlight2-logo">workspace_premium</span>
                  </div>

                  <div class="overflow-hidden flex-shrink min-w-0">
                    <div id="spotlight2-text" class="space-y-1 transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] w-full sm:w-[280px]" style="transform: translateX(0);">
                      <div class="flex items-center gap-3">
                        <span class="px-2 py-0.5 rounded bg-secondary-container/10 text-secondary-container text-[10px] font-black uppercase tracking-wider border border-secondary-container/20">Partenaire Élite</span>
                        <span class="text-[10px] text-primary/40 font-medium italic">Solution recrutement</span>
                      </div>
                      <h3 class="text-xl md:text-2xl font-bold text-primary" id="spotlight2-name">ProximaJob Entreprise</h3>
                      <p class="text-primary/60 text-sm italic font-medium" id="spotlight2-quote">"La plateforme tout-en-un pour trouver, evaluer et recruter les meilleurs talents."</p>
                    </div>
                  </div>
                </div>

                <a href="{{ route('offres.publies') }}" id="spotlight2-link" class="whitespace-nowrap bg-primary text-white text-xs font-bold uppercase tracking-widest px-8 py-4 rounded-full hover:bg-secondary-container transition-all shadow-lg hover:shadow-secondary-container/20 active:scale-95 spotlight-cta flex-shrink-0">
                  Decouvrir
                </a>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- VALUE PROP -->
    <section class="reveal home-deferred py-12 md:py-24 px-4 md:px-10 bg-white">
      <div class="max-w-6xl mx-auto flex flex-col lg:flex-row items-center gap-8 md:gap-16">
        <div class="w-full lg:w-3/5 relative">
          <div
            class="relative rounded-2xl overflow-hidden shadow-2xl transform -rotate-1 hover:rotate-0 transition-transform duration-700">
            <img src="{{ asset('img/votre-hero.png') }}" alt="Professionnelle ProximaJob"
              class="w-full h-[280px] sm:h-[380px] md:h-[450px] object-cover" loading="eager" decoding="async" fetchpriority="high" />
            <div
              class="absolute bottom-3 sm:bottom-6 left-3 sm:left-6 right-3 sm:right-6 bg-white/20 backdrop-blur-xl border border-white/30 p-3 sm:p-5 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center text-white">
                  <span class="material-symbols-outlined text-sm">verified</span>
                </div>
                <div>
                  <p class="font-semibold text-white text-base">Sarah Chen</p>
                  <p class="text-white/80 text-xs">Directrice Marketing, TechNova</p>
                </div>
              </div>
              <p class="mt-2 text-white/90 italic text-sm">"ProximaJob a identifié des opportunités que je n'aurais
                jamais trouvées seule."</p>
            </div>
          </div>
        </div>
        <div class="w-full lg:w-2/5 space-y-6 text-left">
          <span
            class="inline-block px-3 py-1 rounded-full bg-primary-container text-white text-[10px] md:text-xs uppercase tracking-widest">L'Excellence
            au service de votre carrière</span>
          <h2 class="text-2xl md:text-4xl font-bold font-serif text-primary leading-tight">
            Bien plus qu'un portail,<br /><span class="text-secondary-container italic">votre concierge dédié.</span>
          </h2>
          <p class="text-on-surface-variant text-sm md:text-lg leading-relaxed">Notre algorithme prédictif analyse votre
            trajectoire, vos soft skills et votre potentiel pour vous proposer des postes qui correspondent à votre
            vision.</p>
          <div class="grid grid-cols-2 gap-4">
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-secondary-container mt-1">auto_awesome</span>
              <div>
                <h4 class="font-semibold text-primary text-sm">Matching de Précision</h4>
                <p class="text-on-surface-variant text-sm">Réduction de 70% du temps de recherche.</p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-secondary-container mt-1">shield_person</span>
              <div>
                <h4 class="font-semibold text-primary text-sm">Accès Exclusif</h4>
                <p class="text-on-surface-variant text-sm">Offres non publiées sur le marché public.</p>
              </div>
            </div>
          </div>
          <a href="{{ route('offres') }}"
            class="inline-flex items-center gap-3 bg-primary text-white px-8 py-4 rounded-full hover:bg-slate-800 transition-all shadow-xl group">
            <span class="font-semibold">Découvrir votre potentiel</span>
            <span class="material-symbols-outlined group-hover:translate-x-2 transition-transform">arrow_forward</span>
          </a>
        </div>
      </div>
    </section>

    <!-- CATEGORIES -->
    <section class="home-deferred py-12 md:py-20 px-4 md:px-10 bg-surface-container-low/50">
      <div class="max-w-6xl mx-auto">
        <div class="reveal flex flex-col md:flex-row justify-between items-baseline mb-8 md:mb-12 gap-3 md:gap-4">
          <div>
            <h2 class="text-2xl md:text-3xl font-bold font-serif text-primary">Explorer par <span
                class="text-secondary-container">Catégorie</span></h2>
            <p class="text-on-surface-variant text-sm md:text-base mt-1 md:mt-2">Découvrez les secteurs qui recrutent activement aujourd'hui.</p>
          </div>
          <a href="{{ route('offres') }}"
            class="flex items-center gap-2 text-sm font-semibold text-secondary-container hover:underline group py-2 min-h-[44px]">
            Afficher toutes les offres <span
              class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">east</span>
          </a>
        </div>
        <div class="cat-grid-mobile grid grid-cols-1 md:grid-cols-3 gap-6">
          <a href="{{ route('offres') }}"
            class="reveal group bg-white rounded-2xl p-5 md:p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-outline-variant/30" style="transition-delay: 0s">
            <div
              class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-primary-fixed flex items-center justify-center mb-4 md:mb-6 group-hover:scale-110 transition-transform">
              <span class="material-symbols-outlined text-xl md:text-2xl" style="font-variation-settings:'FILL' 1">router</span>
            </div>
            <h3 class="font-bold text-lg md:text-xl text-primary mb-2">Télécommunications</h3>
            <p class="text-on-surface-variant text-sm mb-4">Ingénieurs réseaux, techniciens fibre, chefs de projet
              télécom.</p>
            <div class="flex items-center gap-2 text-sm text-secondary-container font-semibold">
              <span>420 postes ouverts</span><span class="material-symbols-outlined text-lg">north_east</span>
            </div>
          </a>
          <a href="{{ route('offres') }}"
            class="reveal group bg-white rounded-2xl p-5 md:p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-outline-variant/30" style="transition-delay: 0.1s">
            <div
              class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-secondary-fixed flex items-center justify-center mb-4 md:mb-6 group-hover:scale-110 transition-transform">
              <span class="material-symbols-outlined text-xl md:text-2xl" style="font-variation-settings:'FILL' 1">hotel</span>
            </div>
            <h3 class="font-bold text-lg md:text-xl text-primary mb-2">Hôtels & Tourisme</h3>
            <p class="text-on-surface-variant text-sm mb-4">Réceptionnistes, directeurs d'hébergement, guides
              touristiques.</p>
            <div class="flex items-center gap-2 text-sm text-secondary-container font-semibold">
              <span>156 postes ouverts</span><span class="material-symbols-outlined text-lg">north_east</span>
            </div>
          </a>
          <a href="{{ route('offres') }}"
            class="reveal group bg-white rounded-2xl p-5 md:p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-outline-variant/30" style="transition-delay: 0.2s">
            <div
              class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-tertiary-fixed flex items-center justify-center mb-4 md:mb-6 group-hover:scale-110 transition-transform">
              <span class="material-symbols-outlined text-xl md:text-2xl" style="font-variation-settings:'FILL' 1">school</span>
            </div>
            <h3 class="font-bold text-lg md:text-xl text-primary mb-2">Éducation</h3>
            <p class="text-on-surface-variant text-sm mb-4">Enseignants, formateurs, personnel administratif scolaire.
            </p>
            <div class="flex items-center gap-2 text-sm text-secondary-container font-semibold">
              <span>89 postes ouverts</span><span class="material-symbols-outlined text-lg">north_east</span>
            </div>
          </a>
        </div>
      </div>
    </section>

    <!-- RECENT JOBS -->
    <section class="home-deferred py-12 md:py-20 px-4 md:px-10 bg-white">
      <div class="max-w-4xl mx-auto">
        <div class="reveal text-center mb-8 md:mb-12">
          <h2 class="text-2xl md:text-3xl font-bold font-serif text-primary">Dernières opportunités</h2>
          <p class="text-on-surface-variant text-sm md:text-base mt-2">Découvrez les postes récemment publiés par nos entreprises
            partenaires.</p>
        </div>
        <div class="jobs-grid-mobile space-y-4">
          @forelse ($offres->take(3) as $index => $offre)
          <a href="{{ route('job_infos', $offre) }}"
            class="reveal group flex flex-col sm:flex-row sm:items-center justify-between p-4 md:p-6 bg-white rounded-2xl border border-primary/5 hover:border-secondary-container/30 hover:shadow-[0_15px_40px_rgba(0,0,0,0.04)] transition-all duration-500 cursor-pointer relative overflow-hidden" style="transition-delay: 0s">
            <div
              class="absolute inset-0 bg-gradient-to-r from-secondary-container/0 to-secondary-container/[0.02] opacity-0 group-hover:opacity-100 transition-opacity">
            </div>
            <div class="flex items-center gap-4 md:gap-5 relative z-10">
              <div
                class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl bg-secondary-container/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                <span class="material-symbols-outlined text-secondary-container text-xl md:text-2xl">{{ ['cloud_done', 'architecture', 'account_balance'][$index] ?? 'work' }}</span>
              </div>
              <div>
                <h4 class="font-bold text-base md:text-lg text-primary group-hover:text-secondary-container transition-colors">
                  {{ $offre->poste ?: $offre->titre }}</h4>
                <p class="text-on-surface-variant/70 text-xs md:text-sm font-medium">{{ $offre->entreprise->company_name ?? 'Entreprise' }} • {{ $offre->localisation ?: 'Localisation a confirmer' }}</p>
              </div>
            </div>
            <div class="mt-3 sm:mt-0 flex items-center gap-3 md:gap-4 relative z-10">
              <span
                class="px-3 md:px-4 py-1 md:py-1.5 bg-green-50 text-green-700 text-[10px] md:text-[11px] font-black uppercase tracking-widest rounded-full">{{ $offre->type->nom ?? 'Offre' }}</span>
              <div
                class="flex items-center gap-1 text-secondary-container font-bold text-sm opacity-0 group-hover:opacity-100 translate-x-4 group-hover:translate-x-0 transition-all duration-500">
                Détails <span class="material-symbols-outlined text-lg">arrow_right_alt</span>
              </div>
            </div>
          </a>
          @empty
          <div class="rounded-2xl border border-dashed border-outline-variant/30 bg-surface-container-low p-6 text-sm text-on-surface-variant">
            Aucune opportunite recente n'est disponible pour le moment.
          </div>
          @endforelse
        </div>
        <div class="mt-10 text-center">
          <a href="{{ route('offres') }}"
            class="px-6 md:px-8 py-3 rounded-full border border-primary text-primary text-sm md:text-base font-semibold hover:bg-slate-50 transition-colors inline-block min-h-[44px]">Parcourir
            toutes les offres</a>
        </div>
      </div>
    </section>

    <!-- PRICING · Cartes 3D -->
    <section class="home-deferred py-12 md:py-20 px-4 md:px-10">
      <div class="max-w-5xl mx-auto">
        <div class="reveal text-center mb-10 md:mb-16">
          <h2 class="text-2xl md:text-4xl font-bold font-serif text-primary leading-tight mb-4">Choisissez votre forfait</h2>
          <p class="text-on-surface-variant text-sm md:text-base max-w-2xl mx-auto">Des solutions adaptees pour accelerer votre transition professionnelle.</p>
        </div>

        <div class="pricing-grid-wrapper relative">
        <div class="pricing-grid grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">

          <!-- Gratuit -->
          <div class="card-3d-parent reveal" style="transition-delay: 0s">
            <div class="card-3d-inner" style="--card-grad: linear-gradient(135deg, #f5f0eb, #e8e0d5);">
              <div class="card-3d-logo" aria-hidden="true">
                <span class="card-3d-orbit card-3d-orbit--1"></span>
                <span class="card-3d-orbit card-3d-orbit--2"></span>
                <span class="card-3d-orbit card-3d-orbit--3"></span>
              </div>
              <div class="card-3d-glass"></div>
              <div class="card-3d-content text-center">
                <p class="text-xs font-bold text-outline uppercase tracking-wide mb-1">Gratuit</p>
                <div class="flex items-baseline justify-center gap-0.5">
                  <p class="text-4xl font-bold text-primary">0<span class="text-lg text-outline">$</span></p>
                  <p class="text-xs text-outline">/mois</p>
                </div>
                <ul class="mt-4 space-y-2 text-xs text-outline text-left">
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Acces aux offres</li>
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Candidature simple</li>
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline-variant">close</span> CV personnalise IA</li>
                </ul>
                <a href="{{ route('register') }}" class="block mt-5 w-full py-3 rounded-full text-sm font-semibold min-h-[44px] border border-outline-variant/40 text-primary hover:bg-black/5 transition-colors text-center">Commencer</a>
              </div>
            </div>
          </div>

          <!-- Premium (recommande) -->
          <div class="card-3d-parent reveal" style="transition-delay: 0.15s">
            <div class="card-3d-inner" style="--card-grad: linear-gradient(135deg, #fff5eb, #f5e0cc);">
              <div class="card-3d-logo" aria-hidden="true">
                <span class="card-3d-orbit card-3d-orbit--1" style="background: rgba(255,255,255,0.35);"></span>
                <span class="card-3d-orbit card-3d-orbit--2" style="background: rgba(255,255,255,0.45);"></span>
                <span class="card-3d-orbit card-3d-orbit--3" style="background: rgba(255,255,255,0.58);"></span>
              </div>
              <div class="card-3d-glass"></div>
              <span class="absolute -top-3 left-1/2 -translate-x-1/2 z-20 px-4 py-1 rounded-full text-[11px] font-bold text-white bg-secondary-container shadow-lg">Recommandé</span>
              <div class="card-3d-content text-center">
                <p class="text-xs font-bold text-secondary-container uppercase tracking-wide mb-1">Premium</p>
                <div class="flex items-baseline justify-center gap-0.5">
                  <p class="text-4xl font-bold text-primary">29<span class="text-lg text-outline">$</span></p>
                  <p class="text-xs text-outline">/mois</p>
                </div>
                <ul class="mt-4 space-y-2 text-xs text-outline text-left">
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Candidatures illimitees</li>
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> CV personnalise par IA</li>
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Mise en avant du profil</li>
                </ul>
                <a href="{{ route('register') }}" class="block mt-5 w-full py-3 rounded-full text-sm font-semibold min-h-[44px] text-white shadow-lg transition-all duration-300 hover:shadow-xl text-center" style="background: rgba(var(--pj-accent-rgb),0.88);">Commencer</a>
              </div>
            </div>
          </div>

          <!-- Entreprise -->
          <div class="card-3d-parent reveal" style="transition-delay: 0.3s">
            <div class="card-3d-inner" style="--card-grad: linear-gradient(135deg, #f0f4ff, #dce6f5);">
              <div class="card-3d-logo" aria-hidden="true">
                <span class="card-3d-orbit card-3d-orbit--1" style="background: rgba(255,255,255,0.32);"></span>
                <span class="card-3d-orbit card-3d-orbit--2" style="background: rgba(255,255,255,0.42);"></span>
                <span class="card-3d-orbit card-3d-orbit--3" style="background: rgba(255,255,255,0.55);"></span>
              </div>
              <div class="card-3d-glass"></div>
              <div class="card-3d-content text-center">
                <p class="text-xs font-bold text-secondary-container uppercase tracking-wide mb-1">Entreprise</p>
                <div class="flex items-baseline justify-center gap-0.5">
                  <p class="text-4xl font-bold text-primary">99<span class="text-lg text-outline">$</span></p>
                  <p class="text-xs text-outline">/mois</p>
                </div>
                <ul class="mt-4 space-y-2 text-xs text-outline text-left">
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Tout Premium</li>
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> API dediee</li>
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Multi-employeurs</li>
                </ul>
                <a href="{{ route('entreprise.register') }}" class="block mt-5 w-full py-3 rounded-full text-sm font-semibold min-h-[44px] text-white bg-primary-container hover:bg-primary-container/90 transition-colors shadow-lg text-center">Contacter</a>
              </div>
            </div>
          </div>

        </div>
        </div>
      </div>
    </section>

    @include('partials.pre-footer-cta')
  </main>
@endsection

@section('scripts')
  <script>
      (function () {
        const $ = id => document.getElementById(id);
        const svg = $('robot-svg'); if (!svg) return;
        const eyelidL = $('eyelid-left'), eyelidR = $('eyelid-right');
        const browL = $('brow-left'), browR = $('brow-right'), mouth = $('mouth');
        const antTip = $('antenna-tip'), antRing = $('antenna-ring');
        const faceEl = $('face-elements');
        const EYES = [
          { pu: $('pupil-left'), ir: $('iris-left'), r1: $('ref1-l'), r2: $('ref2-l'), wx: 121, wy: 144, vx: 0, vy: 0 },
          { pu: $('pupil-right'), ir: $('iris-right'), r1: $('ref1-r'), r2: $('ref2-r'), wx: 201, wy: 144, vx: 0, vy: 0 }
        ];
        const STATES = {
          idle: { bL: 'M 94 110 Q 116 104 138 110', bR: 'M 182 110 Q 204 104 226 110', m: 'M 136 196 Q 160 204 184 196', bO: .38, mO: .45, pR: 9 },
          curious: { bL: 'M 94 106 Q 116 100 138 107', bR: 'M 182 107 Q 204 100 226 106', m: 'M 136 198 Q 160 206 184 198', bO: .55, mO: .5, pR: 11 },
          alert: { bL: 'M 94 107 Q 116 103 138 108', bR: 'M 182 108 Q 204 103 226 107', m: 'M 138 199 Q 160 195 182 199', bO: .7, mO: .35, pR: 7 },
          happy: { bL: 'M 94 106 Q 116 94 138 106', bR: 'M 182 106 Q 204 94 226 106', m: 'M 136 196 Q 160 216 184 196', bO: .8, mO: .8, pR: 11 },
        };
        let state = 'idle', lastMove = performance.now(), mouseSpeed = 0, px = 0, py = 0;
        let lastIdleChange = 0, nextIdleInterval = 2000;
        const B = 30, MX = 8;
        let targets = [{ x: B, y: B }, { x: B, y: B }], currents = [{ x: B, y: B }, { x: B, y: B }];
        let bTimer = 0, nBlink = 3e3 + Math.random() * 3e3;
        let bL = { phase: 'open', t: 0 }, bR = { phase: 'open', t: 0 };
        let pR = 9, ft = 0, hRX = 0, hRY = 0;
        const lerp = (a, b, t) => a + (b - a) * t;
        const toSVG = (cx, cy) => { const r = svg.getBoundingClientRect(); return { x: (cx - r.left) * 320 / r.width, y: (cy - r.top) * 320 / r.height }; };
        window.addEventListener('mousemove', e => {
          const dx = e.clientX - px, dy = e.clientY - py;
          mouseSpeed = Math.sqrt(dx * dx + dy * dy); px = e.clientX; py = e.clientY; lastMove = performance.now();
          const pt = toSVG(e.clientX, e.clientY);
          EYES.forEach((eye, i) => {
            const ddx = pt.x - eye.wx, ddy = pt.y - eye.wy, a = Math.atan2(ddy, ddx), d = Math.sqrt(ddx * ddx + ddy * ddy);
            targets[i] = { x: B + Math.cos(a) * MX * Math.min(1, d / 140), y: B + Math.sin(a) * MX * Math.min(1, d / 140) };
          });
          if (mouseSpeed > 28) state = 'alert'; else if (mouseSpeed > 6 && state !== 'happy') state = 'curious';
        });
        const sb = eye => { eye.phase = 'closing'; eye.t = 0; };
        const doBlink = () => { sb(bL); setTimeout(() => sb(bR), 8 + Math.random() * 12); };
        function uBlink(eye, dt, el) {
          const C = 0.035, O = 0.009;
          if (eye.phase === 'closing') { eye.t = Math.min(1, eye.t + C * dt); if (eye.t >= 1) { eye.phase = 'closed'; eye.t = 0; } }
          else if (eye.phase === 'closed') { eye.t += dt; if (eye.t > 15) { eye.phase = 'opening'; eye.t = 0; } }
          else if (eye.phase === 'opening') { eye.t = Math.min(1, eye.t + O * dt); if (eye.t >= 1) { eye.phase = 'open'; eye.t = 0; } }
          const closed = eye.phase === 'closing' ? Math.pow(eye.t, 1.5) : eye.phase === 'closed' ? 1 : eye.phase === 'opening' ? Math.pow(1 - eye.t, 3) : 0;
          const p = 1 - closed;
          el.style.transformOrigin = '30px 0px';
          // On remonte la paupière (Y négatif) quand p=1 pour un air plus "réveillé"
          el.style.transform = 'translateY(' + ((1 - p) * 18 - 8) + 'px) scaleY(' + lerp(2.3, 1, p) + ')';
        }
        let last = performance.now();
        function loop(now) {
          const dt = Math.min(now - last, 50); last = now;
          bTimer += dt; if (bTimer >= nBlink) { bTimer = 0; nBlink = 2800 + Math.random() * 4200; doBlink(); }
          uBlink(bL, dt, eyelidL); uBlink(bR, dt, eyelidR);
          EYES.forEach((eye, i) => {
            const ax = (targets[i].x - currents[i].x) * 0.15, ay = (targets[i].y - currents[i].y) * 0.15;
            eye.vx = (eye.vx + ax) * 0.72; eye.vy = (eye.vy + ay) * 0.72;
            currents[i].x += eye.vx; currents[i].y += eye.vy;
            const cx = currents[i].x, cy = currents[i].y;
            eye.pu.setAttribute('cx', cx); eye.pu.setAttribute('cy', cy);
            eye.r1.setAttribute('cx', cx + 6 - (cx - B) * 0.35); eye.r1.setAttribute('cy', cy - 8 - (cy - B) * 0.35);
            eye.ir.setAttribute('cx', lerp(B, cx, .3)); eye.ir.setAttribute('cy', lerp(B, cy, .3));
          });
          const avgX = (currents[0].x + currents[1].x) / 2 - B, avgY = (currents[0].y + currents[1].y) / 2 - B;
          if (faceEl) faceEl.style.transform = 'translate(' + avgX * 0.6 + 'px,' + avgY * 0.6 + 'px)';
          ft += dt * 0.0008;
          const fy = Math.sin(ft * 0.85) * 4 + Math.sin(ft * 1.9) * 1.2, fr = Math.sin(ft * 0.65) * 0.5;
          hRY = lerp(hRY, (px - window.innerWidth / 2) / (window.innerWidth / 2) * 10, 0.05);
          hRX = lerp(hRX, -(py - window.innerHeight / 2) / (window.innerHeight / 2) * 6, 0.05);
          const cr = $('chassis-ref'); if (cr) { cr.setAttribute('cx', 108 - hRY * 1.8); cr.setAttribute('cy', 70 - hRX * 1.5); }
          svg.style.transform = 'perspective(800px) translateY(' + fy + 'px) rotate(' + fr + 'deg) rotateX(' + hRX + 'deg) rotateY(' + hRY + 'deg)';
          svg.style.transformOrigin = '50% 80%';
          const t = now * .0018;
          if (antTip) { antTip.setAttribute('r', (6 + Math.sin(t) * 1.8).toFixed(1)); }
          if (antRing) { antRing.setAttribute('opacity', ((Math.sin(t * 1.4) * .5 + .5) * 0.4).toFixed(2)); antRing.setAttribute('r', (11 + (1 - (Math.sin(t * 1.4) * .5 + .5)) * 7).toFixed(1)); }
          const s = STATES[state];
          browL.setAttribute('d', s.bL); browL.setAttribute('opacity', s.bO);
          browR.setAttribute('d', s.bR); browR.setAttribute('opacity', s.bO);
          mouth.setAttribute('d', s.m); mouth.setAttribute('opacity', s.mO);
          pR = lerp(pR, s.pR, .04); EYES.forEach(e => e.pu.setAttribute('r', pR.toFixed(1)));

          // --- LOGIQUE IDLE (Regard aléatoire si inactif) ---
          if (now - lastMove > 3000) {
            state = 'idle';
            if (now - lastIdleChange > nextIdleInterval) {
              lastIdleChange = now;
              nextIdleInterval = 1500 + Math.random() * 2500;
              const rx = B + (Math.random() - 0.5) * 12;
              const ry = B + (Math.random() - 0.5) * 12;
              EYES.forEach((_, i) => targets[i] = { x: rx, y: ry });
            }
          } else {
            if (state !== 'happy') {
              if (mouseSpeed > 28) state = 'alert';
              else if (mouseSpeed > 4) state = 'curious';
            }
          }

          requestAnimationFrame(loop);
        }
        requestAnimationFrame(loop);
      })();
  </script>

  <script>
    /* ── Spotlight 2 · Slide Rotation ── */
    (function() {
      const textEl  = document.getElementById('spotlight2-text');
      const nameEl  = document.getElementById('spotlight2-name');
      const quoteEl = document.getElementById('spotlight2-quote');
      const logoEl  = document.getElementById('spotlight2-logo');
      const linkEl  = document.getElementById('spotlight2-link');
      if (!textEl || !nameEl) return;

      const partners2 = [
        { name: 'ProximaJob Entreprise', quote: '"La plateforme tout-en-un pour trouver, evaluer et recruter les meilleurs talents."', icon: 'workspace_premium', link: 'entreprise-offres.html' },
        { name: 'TechRecruit',             quote: '"L\'IA au coeur du matching — 10 000 recrutements par an."',                icon: 'precision_manufacturing',   link: 'offres.html' },
        { name: 'BuildTeam',               quote: '"Des equipes sur-mesure en moins de 48h. La qualite avant la quantite."',  icon: 'groups',                   link: 'offres.html' },
        { name: 'ScaleUp RH',              quote: '"Accelerez votre croissance avec nos solutions RH nouvelle generation."',   icon: 'trending_up',              link: 'offres.html' }
      ];

      let idx = 0;
      const SLIDE_DUR = 500;  // ms
      const CYCLE = 10000;    // total cycle time

      function swap(partner) {
        if (nameEl)  nameEl.textContent  = partner.name;
        if (quoteEl) quoteEl.textContent = partner.quote;
        if (logoEl)  logoEl.textContent  = partner.icon;
        if (linkEl)  linkEl.href         = partner.link;
      }

      function slideOut() {
        textEl.style.transition = 'transform ' + SLIDE_DUR + 'ms cubic-bezier(0.22,1,0.36,1)';
        textEl.style.transform = 'translateX(-120%)';
      }

      function onSlideOutDone() {
        textEl.removeEventListener('transitionend', onSlideOutDone);
        idx = (idx + 1) % partners2.length;
        swap(partners2[idx]);

        textEl.style.transition = 'none';
        textEl.style.transform = 'translateX(120%)';
        textEl.offsetHeight;
        textEl.style.transition = 'transform ' + SLIDE_DUR + 'ms cubic-bezier(0.22,1,0.36,1)';
        textEl.style.transform = 'translateX(0)';
      }

      function onSlideInDone() {
        textEl.removeEventListener('transitionend', onSlideInDone);
        setTimeout(cycle, CYCLE - SLIDE_DUR * 2);
      }

      function cycle() {
        textEl.addEventListener('transitionend', onSlideOutDone, { once: true });
        textEl.addEventListener('transitionend', onSlideInDone,  { once: true });
        slideOut();
      }

      // Start after first delay
      setTimeout(cycle, CYCLE);
    })();
  </script>
  <script>
    /* ── Mobile Filter Toggle ── */
    (function() {
      const btn = document.getElementById('filter-toggle');
      const panel = document.getElementById('filter-panel');
      if (!btn || !panel) return;
      let open = false;
      btn.addEventListener('click', () => {
        open = !open;
        panel.classList.toggle('hidden', !open);
        btn.querySelector('.material-symbols-outlined').textContent = open ? 'close' : 'tune';
      });
    })();
  </script>
@endsection
