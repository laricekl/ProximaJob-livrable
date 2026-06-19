import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from '../helpers/assertions.js';
import {
  expectFooterVisible,
  expectNoHorizontalOverflow,
  expectRevealContentVisible,
  scrollThroughFullPage,
} from '../helpers/layout.js';

test.describe('Demo Public', () => {
  test('demo public: accueil, offres, detail et contact', async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'La demo public est ecrite pour le parcours desktop.');
    test.setTimeout(60_000);

    let response = await page.goto('/');
    await expectHealthyResponse(response, page);

    await expect(page).toHaveTitle(/Accueil|ProximaJob/i);
    await expect(page.locator('body')).toContainText(/Votre recherche d'emploi|propulsée par l'IA/i);
    await expect(page.locator('nav [data-language-switcher]').first()).toBeVisible();
    await expectNoHorizontalOverflow(page);
    await expectRevealContentVisible(page);

    await scrollThroughFullPage(page);
    await expectFooterVisible(page);

    response = await page.goto('/offres');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).toContainText(/offres|Aucune offre/i);

    const searchForm = page.getByTestId('offers-desktop-search-form');
    await expect(searchForm).toBeVisible();
    await searchForm.getByPlaceholder('Titre du poste, mots-clés...').fill('developpeur');
    await Promise.all([
      page.waitForURL(/\/offres\?.*search=developpeur/),
      searchForm.getByRole('button', { name: /Rechercher/i }).click(),
    ]);

    let offerLink = page.locator('a[href^="/offres/"], a[href*="/offres/"]').filter({ hasText: /.+/ }).first();
    if (await offerLink.count() === 0) {
      response = await page.goto('/offres');
      await expectHealthyResponse(response, page);
      offerLink = page.locator('a[href^="/offres/"], a[href*="/offres/"]').filter({ hasText: /.+/ }).first();
    }

    await expect(offerLink).toBeVisible();
    await Promise.all([
      page.waitForURL(/\/offres\/[^/]+$/),
      offerLink.click(),
    ]);

    await expect(page.locator('main h1').first()).toBeVisible();
    await expect(page.locator('body')).toContainText(/Postuler maintenant|Se connecter pour postuler|Retour aux offres/i);

    response = await page.goto('/contact');
    await expectHealthyResponse(response, page);
    await expect(page.locator('h1, h2').first()).toBeVisible();
    await expect(page.getByLabel(/nom/i).first()).toBeVisible();
    await expect(page.getByLabel(/adresse e-mail|email/i).first()).toBeVisible();
    await expect(page.getByLabel(/message/i).first()).toBeVisible();
    await expectFooterVisible(page);
  });
});
