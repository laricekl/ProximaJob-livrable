import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from './helpers/assertions.js';
import {
  expectFooterVisible,
  expectNoHorizontalOverflow,
  expectRevealContentVisible,
  expectTextVisibleAfterScroll,
  scrollThroughFullPage,
} from './helpers/layout.js';

const publicPages = [
  { path: '/', title: /Accueil|ProximaJob/i, landmark: /Accueil|Offres/i },
  { path: '/offres', title: /Offres|ProximaJob/i, landmark: /offres/i },
  { path: '/contact', title: /Contact|ProximaJob/i, landmark: /contact/i },
  { path: '/ressources', title: /Ressources|ProximaJob/i, landmark: /ressources/i },
  { path: '/abonnement', title: /Abonnement|Forfaits|ProximaJob/i, landmark: /abonnement|forfaits/i },
];

test.describe('public area', () => {
  test.afterEach(async ({ page }, testInfo) => {
    if (testInfo.status === testInfo.expectedStatus) {
      return;
    }

    await testInfo.attach('public-page-full-screenshot', {
      body: await page.screenshot({ fullPage: true }),
      contentType: 'image/png',
    });
  });

  for (const pageCase of publicPages) {
    test(`${pageCase.path} loads`, async ({ page }) => {
      const response = await page.goto(pageCase.path);

      await expectHealthyResponse(response, page);
      await expect(page).toHaveTitle(pageCase.title);
      await expect(page.locator('body')).toContainText(pageCase.landmark);
    });
  }

  test('language switcher is available in the public navigation', async ({ page }) => {
    const response = await page.goto('/');
    await expectHealthyResponse(response, page);

    if ((page.viewportSize()?.width || 0) < 768) {
      await page.locator('#menu-toggle').click();
      await expect(page.locator('#mobile-menu [data-language-switcher]')).toBeVisible();
    } else {
      await expect(page.locator('nav [data-language-switcher]').first()).toBeVisible();
    }

    await expect(page.locator('[data-language-switcher] [data-locale="fr"]').first()).toBeAttached();
    await expect(page.locator('[data-language-switcher] [data-locale="en"]').first()).toBeAttached();
  });

  test('home page renders completely from hero to footer', async ({ page }) => {
    test.setTimeout(60_000);

    const response = await page.goto('/');

    await expectHealthyResponse(response, page);
    await expect(page).toHaveTitle(/Accueil|ProximaJob/i);
    await expectNoHorizontalOverflow(page);
    await expectRevealContentVisible(page);

    const requiredSections = [
      /Votre recherche d'emploi/i,
      /propulsée par l'IA/i,
      /Excellence/i,
      /concierge dédié/i,
      /Explorer par/i,
      /Dernières opportunités/i,
      /Choisissez votre forfait/i,
      /Gratuit/i,
      /Premium/i,
      /Entreprise/i,
    ];

    for (const sectionText of requiredSections) {
      await expectTextVisibleAfterScroll(page, sectionText);
    }

    await scrollThroughFullPage(page);
    await expectFooterVisible(page);
    await expectNoHorizontalOverflow(page);
  });

  test('offers list links to a real offer detail page', async ({ page }) => {
    let response = await page.goto('/offres');
    await expectHealthyResponse(response, page);

    const offerLink = page.locator('a[href^="/offres/"], a[href*="/offres/"]').filter({ hasText: /.+/ }).first();
    await expect(offerLink).toBeVisible();
    await Promise.all([
      page.waitForURL(/\/offres\/[^/]+$/),
      offerLink.click(),
    ]);

    await expect(page).toHaveURL(/\/offres\/[^/]+$/);
    await expect(page.locator('h1')).toBeVisible();
    await expect(page.locator('body')).not.toContainText(/TechCorp|Développeur Full Stack passionné pour rejoindre notre équipe produit/i);
    await expect(page.getByText(/Postuler maintenant|Se connecter pour postuler/i)).toBeVisible();
  });

  test('offers search form submits real filter query parameters', async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'Desktop search form coverage');

    const response = await page.goto('/offres');
    await expectHealthyResponse(response, page);

    const searchForm = page.getByTestId('offers-desktop-search-form');
    await expect(searchForm).toBeVisible();

    await searchForm.getByPlaceholder('Titre du poste, mots-clés...').fill('developpeur');
    await searchForm.getByPlaceholder('Localisation').fill('Montreal');
    await searchForm.locator('select[name="categories[]"]').selectOption({ index: 1 });

    await Promise.all([
      page.waitForURL(/\/offres\?.*search=developpeur/),
      searchForm.getByRole('button', { name: /Rechercher/i }).click(),
    ]);

    await expect(page).toHaveURL(/search=developpeur/);
    await expect(page).toHaveURL(/localisation=Montreal/);
    await expect(page).toHaveURL(/categories%5B%5D=|categories\[\]=/);
    await expect(page.locator('body')).toContainText(/offres|Aucune offre/i);
  });

  test('offers pagination controls stay separated', async ({ page }) => {
    const response = await page.goto('/offres');
    await expectHealthyResponse(response, page);

    const pagination = page.getByRole('navigation', { name: /pagination/i }).last();
    await expect(pagination).toBeVisible();
    await pagination.scrollIntoViewIfNeeded();

    const overlap = await pagination.locator('[data-pagination-control]:visible').evaluateAll((items) => {
      const boxes = items
        .map((item) => {
          const rect = item.getBoundingClientRect();
          return {
            text: item.textContent?.trim() || item.getAttribute('aria-label') || '',
            left: rect.left,
            right: rect.right,
            top: rect.top,
            bottom: rect.bottom,
            width: rect.width,
            height: rect.height,
          };
        })
        .filter((box) => box.width > 0 && box.height > 0);

      for (let currentIndex = 0; currentIndex < boxes.length; currentIndex += 1) {
        for (let nextIndex = currentIndex + 1; nextIndex < boxes.length; nextIndex += 1) {
          const current = boxes[currentIndex];
          const next = boxes[nextIndex];
          const intersects = current.left < next.right
            && current.right > next.left
            && current.top < next.bottom
            && current.bottom > next.top;

          if (intersects) {
            return { current, next };
          }
        }
      }

      return null;
    });

    expect(overlap, 'Pagination controls should not overlap').toBeNull();
  });
});
