@once
  <style>
    .cookie-consent-shell {
      position: fixed;
      inset-inline: 0;
      bottom: 0;
      z-index: 80;
      pointer-events: none;
      padding: 0 1rem 1rem;
    }

    .cookie-consent-card {
      pointer-events: auto;
      width: min(64rem, calc(100vw - 2rem));
      margin: 0 auto;
      border: 1px solid rgba(15, 23, 42, 0.1);
      background: rgba(255, 255, 255, 0.92);
      backdrop-filter: blur(18px);
      box-shadow: 0 14px 32px rgba(15, 23, 42, 0.1);
      border-radius: 999px;
    }

    .cookie-consent-shell.is-preferences-mode {
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.25rem;
      background: rgba(15, 23, 42, 0.24);
      backdrop-filter: blur(8px);
    }

    .cookie-consent-shell.is-preferences-mode .cookie-consent-card {
      max-width: 54rem;
      width: 100%;
      border-radius: 1.75rem;
      box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
    }

    .cookie-consent-shell.is-preferences-mode .cookie-consent-summary {
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 1rem;
      text-align: center;
      max-width: 42rem;
      margin: 0 auto;
    }

    .cookie-consent-shell.is-preferences-mode .cookie-summary-copy {
      max-width: 36rem;
    }

    .cookie-consent-shell.is-preferences-mode .cookie-consent-line {
      justify-content: center;
    }

    .cookie-consent-shell.is-preferences-mode .cookie-consent-actions {
      justify-content: center;
    }

    .cookie-preferences-panel {
      display: none;
    }

    .cookie-preferences-panel.is-open {
      display: block;
    }

    .cookie-summary-copy {
      min-width: 0;
      flex: 1 1 auto;
      max-width: none;
    }

    .cookie-consent-hidden {
      display: none !important;
    }

    .cookie-accessible-only {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      border: 0;
    }

    .cookie-choice-chip[aria-pressed="true"] {
      background: var(--pj-accent, #eb843c);
      color: #fff;
      border-color: transparent;
      box-shadow: 0 10px 24px rgba(235, 132, 60, 0.2);
    }

    .cookie-floating-trigger {
      position: fixed;
      left: 1rem;
      bottom: 1rem;
      z-index: 70;
      border: 1px solid rgba(15, 23, 42, 0.08);
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(18px);
      box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
      width: 2.75rem;
      height: 2.75rem;
      min-height: 2.75rem;
      justify-content: center;
    }

    .cookie-mini-note {
      color: rgba(71, 85, 105, 0.88);
    }

    .cookie-summary-copy strong {
      color: var(--primary, #1f2937);
    }

    .cookie-close-button {
      display: none;
    }

    .cookie-consent-shell.is-preferences-mode .cookie-close-button {
      display: inline-flex;
    }

    .cookie-consent-summary {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      min-width: 0;
    }

    .cookie-consent-actions {
      flex: 0 0 auto;
      flex-wrap: nowrap;
    }

    .cookie-consent-kicker {
      margin: 0;
      color: rgba(100, 116, 139, 0.95);
      font-size: 0.68rem;
      font-weight: 800;
      letter-spacing: 0.14em;
      text-transform: uppercase;
    }

    .cookie-consent-line {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      min-width: 0;
      overflow: hidden;
      white-space: nowrap;
    }

    .cookie-consent-title {
      color: var(--primary, #1f2937);
      font-weight: 700;
    }

    .cookie-consent-separator {
      width: 0.25rem;
      height: 0.25rem;
      border-radius: 999px;
      background: rgba(148, 163, 184, 0.9);
      flex: 0 0 auto;
    }

    @media (max-width: 640px) {
      .cookie-consent-shell {
        padding: 0 0.5rem 0.5rem;
      }

      .cookie-consent-shell.is-preferences-mode {
        align-items: flex-end;
        padding: 0.75rem;
      }

      .cookie-floating-trigger {
        left: 0.75rem;
        bottom: 0.75rem;
      }

      .cookie-consent-card {
        border-radius: 999px;
        width: 100%;
      }

      .cookie-consent-summary {
        flex-direction: row;
        align-items: center;
        gap: 0.45rem;
        text-align: left;
      }

      .cookie-consent-line {
        gap: 0.25rem;
      }

      .cookie-summary-copy {
        width: 100%;
      }

      .cookie-consent-actions {
        width: auto;
        flex-wrap: nowrap;
        gap: 0.25rem;
        justify-content: flex-end;
      }

      .cookie-consent-actions > button:not(.cookie-close-button) {
        min-height: 2rem;
        padding: 0.35rem 0.55rem;
        font-size: 0.68rem;
        justify-content: center;
      }

      .cookie-consent-actions > button[data-cookie-action="toggle-preferences"] {
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: rgba(255, 255, 255, 0.72);
        padding-inline: 0.55rem;
      }

      .cookie-consent-separator,
      .cookie-optional-note,
      .cookie-policy-link {
        display: none;
      }

      .cookie-consent-shell.is-preferences-mode .cookie-consent-summary {
        align-items: flex-start;
        text-align: left;
        max-width: none;
        margin: 0;
      }

      .cookie-consent-shell.is-preferences-mode .cookie-consent-line,
      .cookie-consent-shell.is-preferences-mode .cookie-consent-actions {
        justify-content: flex-start;
      }

      .cookie-consent-shell.is-preferences-mode .cookie-preferences-panel {
        max-height: 58vh;
        overflow-y: auto;
        padding-right: 0.125rem;
      }

      .cookie-consent-shell.is-preferences-mode .cookie-preferences-panel .grid {
        gap: 0.875rem;
      }

      .cookie-consent-shell.is-preferences-mode .cookie-preferences-panel article {
        padding: 1rem;
      }

      .cookie-consent-shell.is-preferences-mode .cookie-preferences-panel article > div {
        flex-direction: column;
        align-items: flex-start;
      }

      .cookie-consent-shell.is-preferences-mode .cookie-preferences-panel .cookie-choice-chip,
      .cookie-consent-shell.is-preferences-mode .cookie-preferences-panel article span.rounded-full {
        margin-top: 0.4rem;
      }
    }
  </style>
@endonce

<div
  id="cookieConsentRoot"
  data-cookie-root
  data-cookie-policy-url="{{ route('cookies.policy') }}"
  class="cookie-consent-shell cookie-consent-hidden"
>
  <section class="cookie-consent-card px-3 py-2 md:px-4 md:py-2.5">
    <div class="cookie-consent-summary">
      <div class="cookie-summary-copy">
        <p class="cookie-accessible-only">
          <span class="cookie-accessible-only">Vos préférences de confidentialité</span>
        </p>
        <div class="cookie-consent-line cookie-mini-note text-xs leading-5 md:text-[13px]">
          <span class="cookie-consent-title">Cookies</span>
          <span class="cookie-consent-separator" aria-hidden="true"></span>
          <span class="cookie-optional-note">Audience, préférences et marketing en option.</span>
          <a href="{{ route('cookies.policy') }}" class="cookie-policy-link font-bold text-secondary-container hover:underline">Politique cookies</a>
        </div>
      </div>

      <div class="cookie-consent-actions flex flex-wrap items-center gap-2">
        <button type="button" data-cookie-action="toggle-preferences" class="rounded-full px-2 py-1 text-[12px] font-bold text-outline transition hover:text-secondary-container">
          Personnaliser
        </button>
        <button type="button" data-cookie-action="decline-all" class="rounded-full border border-outline-variant/30 bg-white/80 px-3 py-1.5 text-[12px] font-bold text-primary transition hover:bg-surface-container-low">
          Refuser
        </button>
        <button type="button" data-cookie-action="accept-all" class="rounded-full bg-secondary-container px-3 py-1.5 text-[12px] font-bold text-white transition hover:bg-secondary">
          Tout accepter
        </button>
        <button type="button" data-cookie-action="close-preferences" class="cookie-close-button h-8 w-8 items-center justify-center rounded-full border border-outline-variant/20 bg-white/85 text-outline transition hover:text-primary">
          <span class="material-symbols-outlined text-[16px]">close</span>
        </button>
      </div>
    </div>

    <div id="cookiePreferencesPanel" class="cookie-preferences-panel mt-5 border-t border-outline-variant/10 pt-5">
      <div class="grid gap-4 lg:grid-cols-2">
        <article class="rounded-2xl border border-outline-variant/10 bg-white/70 p-4">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h3 class="text-sm font-bold text-primary">Cookies nécessaires</h3>
              <p class="mt-1 text-sm text-on-surface-variant">Connexion, sécurité, navigation, choix de langue et mémorisation de votre consentement.</p>
            </div>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Actifs</span>
          </div>
        </article>

        <article class="rounded-2xl border border-outline-variant/10 bg-white/70 p-4">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h3 class="text-sm font-bold text-primary">Mesure d'audience</h3>
              <p class="mt-1 text-sm text-on-surface-variant">Mesurer les visites, comprendre les parcours et améliorer le produit sans casser l'expérience.</p>
            </div>
            <button type="button" class="cookie-choice-chip rounded-full border border-outline-variant/20 px-3 py-1 text-xs font-bold text-outline transition" data-cookie-toggle="analytics" aria-pressed="false">
              Désactivé
            </button>
          </div>
        </article>

        <article class="rounded-2xl border border-outline-variant/10 bg-white/70 p-4">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h3 class="text-sm font-bold text-primary">Préférences</h3>
              <p class="mt-1 text-sm text-on-surface-variant">Conserver des réglages de confort, par exemple des préférences d'affichage avancées.</p>
            </div>
            <button type="button" class="cookie-choice-chip rounded-full border border-outline-variant/20 px-3 py-1 text-xs font-bold text-outline transition" data-cookie-toggle="preferences" aria-pressed="false">
              Désactivé
            </button>
          </div>
        </article>

        <article class="rounded-2xl border border-outline-variant/10 bg-white/70 p-4">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h3 class="text-sm font-bold text-primary">Marketing</h3>
              <p class="mt-1 text-sm text-on-surface-variant">Campagnes, pixels publicitaires et intégrations sociales non essentielles.</p>
            </div>
            <button type="button" class="cookie-choice-chip rounded-full border border-outline-variant/20 px-3 py-1 text-xs font-bold text-outline transition" data-cookie-toggle="marketing" aria-pressed="false">
              Désactivé
            </button>
          </div>
        </article>
      </div>

      <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs text-outline">Vous pourrez modifier ces choix plus tard via “Gérer les cookies”.</p>
        <button type="button" data-cookie-action="save-preferences" class="rounded-full bg-primary px-4 py-2 text-[13px] font-bold text-white transition hover:bg-primary/90">
          Enregistrer mes choix
        </button>
      </div>
    </div>
  </section>
</div>

<button
  type="button"
  id="cookiePreferencesTrigger"
  data-cookie-open
  class="cookie-floating-trigger cookie-consent-hidden inline-flex items-center rounded-full text-primary transition hover:border-secondary-container/30 hover:text-secondary-container"
  aria-label="Gérer les cookies"
  title="Cookies"
>
  <span class="material-symbols-outlined text-[17px]">cookie</span>
</button>

@once
  <script>
    (() => {
      const COOKIE_NAME = 'pj_cookie_preferences';
      const COOKIE_MAX_AGE = 60 * 60 * 24 * 180;
      const root = document.querySelector('[data-cookie-root]');
      const floatingTrigger = document.querySelector('[data-cookie-open]');

      if (!root || !floatingTrigger) {
        return;
      }

      const preferencesPanel = document.getElementById('cookiePreferencesPanel');
      const toggleButtons = [...root.querySelectorAll('[data-cookie-toggle]')];

      const defaultConsent = {
        necessary: true,
        analytics: false,
        preferences: false,
        marketing: false,
      };

      const setCookie = (name, value, maxAge) => {
        document.cookie = `${name}=${encodeURIComponent(value)}; path=/; max-age=${maxAge}; SameSite=Lax`;
      };

      const getCookie = (name) => {
        const match = document.cookie
          .split('; ')
          .find((row) => row.startsWith(`${name}=`));

        return match ? decodeURIComponent(match.split('=').slice(1).join('=')) : null;
      };

      const safeParse = (value) => {
        if (!value) {
          return null;
        }

        try {
          return JSON.parse(value);
        } catch (error) {
          return null;
        }
      };

      const normalizeConsent = (value) => {
        const parsed = typeof value === 'string' ? safeParse(value) : value;

        if (!parsed || typeof parsed !== 'object') {
          return null;
        }

        return {
          necessary: true,
          analytics: Boolean(parsed.analytics),
          preferences: Boolean(parsed.preferences),
          marketing: Boolean(parsed.marketing),
          consented_at: parsed.consented_at || null,
          version: parsed.version || 1,
        };
      };

      const getStoredConsent = () => normalizeConsent(getCookie(COOKIE_NAME));

      const syncToggleButton = (button, enabled) => {
        button.setAttribute('aria-pressed', enabled ? 'true' : 'false');
        button.textContent = enabled ? 'Activé' : 'Désactivé';
      };

      const applyConsentToToggles = (consent) => {
        toggleButtons.forEach((button) => {
          const category = button.dataset.cookieToggle;
          syncToggleButton(button, Boolean(consent?.[category]));
        });
      };

      const openBanner = () => {
        root.classList.remove('cookie-consent-hidden');
      };

      const closeBanner = () => {
        root.classList.add('cookie-consent-hidden');
      };

      const openPreferences = () => {
        preferencesPanel.classList.add('is-open');
      };

      const closePreferences = () => {
        preferencesPanel.classList.remove('is-open');
      };

      const showFloatingTrigger = () => {
        floatingTrigger.classList.remove('cookie-consent-hidden');
      };

      const hideFloatingTrigger = () => {
        floatingTrigger.classList.add('cookie-consent-hidden');
      };

      const activateDeferredScripts = (consent) => {
        document.querySelectorAll('script[data-cookie-category]').forEach((script) => {
          const category = script.dataset.cookieCategory;
          const isAllowed = category === 'necessary' || Boolean(consent?.[category]);

          if (!isAllowed || script.dataset.cookieActivated === '1') {
            return;
          }

          const replacement = document.createElement('script');

          [...script.attributes].forEach((attribute) => {
            if (attribute.name === 'type' || attribute.name.startsWith('data-cookie-')) {
              return;
            }

            replacement.setAttribute(attribute.name, attribute.value);
          });

          const externalSource = script.dataset.cookieSrc;
          if (externalSource) {
            replacement.src = externalSource;
          } else {
            replacement.textContent = script.textContent;
          }

          script.dataset.cookieActivated = '1';
          script.parentNode?.insertBefore(replacement, script.nextSibling);
        });
      };

      const broadcastConsent = (consent) => {
        document.documentElement.dataset.cookieAnalytics = consent.analytics ? 'granted' : 'denied';
        document.documentElement.dataset.cookiePreferences = consent.preferences ? 'granted' : 'denied';
        document.documentElement.dataset.cookieMarketing = consent.marketing ? 'granted' : 'denied';

        window.dispatchEvent(new CustomEvent('proximajob:cookie-consent-updated', {
          detail: consent,
        }));

        activateDeferredScripts(consent);
      };

      const persistConsent = (consent) => {
        const payload = {
          ...defaultConsent,
          ...consent,
          necessary: true,
          consented_at: new Date().toISOString(),
          version: 1,
        };

        setCookie(COOKIE_NAME, JSON.stringify(payload), COOKIE_MAX_AGE);
        applyConsentToToggles(payload);
        broadcastConsent(payload);
        closePreferences();
        closeBanner();
        showFloatingTrigger();
      };

      const getToggleState = () => {
        const state = { ...defaultConsent };

        toggleButtons.forEach((button) => {
          state[button.dataset.cookieToggle] = button.getAttribute('aria-pressed') === 'true';
        });

        return state;
      };

      toggleButtons.forEach((button) => {
        button.addEventListener('click', () => {
          const nextValue = button.getAttribute('aria-pressed') !== 'true';
          syncToggleButton(button, nextValue);
        });
      });

      root.querySelector('[data-cookie-action="accept-all"]')?.addEventListener('click', () => {
        persistConsent({
          analytics: true,
          preferences: true,
          marketing: true,
        });
      });

      root.querySelector('[data-cookie-action="decline-all"]')?.addEventListener('click', () => {
        persistConsent({
          analytics: false,
          preferences: false,
          marketing: false,
        });
      });

      root.querySelector('[data-cookie-action="toggle-preferences"]')?.addEventListener('click', () => {
        preferencesPanel.classList.toggle('is-open');
        root.classList.toggle('is-preferences-mode', preferencesPanel.classList.contains('is-open'));
      });

      root.querySelector('[data-cookie-action="save-preferences"]')?.addEventListener('click', () => {
        persistConsent(getToggleState());
      });

      root.querySelector('[data-cookie-action="close-preferences"]')?.addEventListener('click', () => {
        closePreferences();
        root.classList.remove('is-preferences-mode');
        if (getStoredConsent()) {
          closeBanner();
        }
      });

      floatingTrigger.addEventListener('click', () => {
        const consent = getStoredConsent() || defaultConsent;
        applyConsentToToggles(consent);
        closePreferences();
        root.classList.add('is-preferences-mode');
        openBanner();
      });

      const initialConsent = getStoredConsent();

      if (initialConsent) {
        applyConsentToToggles(initialConsent);
        broadcastConsent(initialConsent);
        closeBanner();
        root.classList.remove('is-preferences-mode');
        showFloatingTrigger();
      } else {
        applyConsentToToggles(defaultConsent);
        openBanner();
        hideFloatingTrigger();
      }

      window.ProximaCookieConsent = {
        get() {
          return getStoredConsent() || { ...defaultConsent };
        },
        allows(category) {
          const consent = getStoredConsent() || defaultConsent;
          return category === 'necessary' ? true : Boolean(consent[category]);
        },
        open() {
          const consent = getStoredConsent() || defaultConsent;
          applyConsentToToggles(consent);
          openPreferences();
          openBanner();
        },
        save(consent) {
          persistConsent(consent);
        },
        reset() {
          setCookie(COOKIE_NAME, '', 0);
          openBanner();
          hideFloatingTrigger();
          applyConsentToToggles(defaultConsent);
          closePreferences();
        },
      };
    })();
  </script>
@endonce
