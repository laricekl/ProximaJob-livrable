import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from './helpers/assertions.js';
import { credentials, login } from './helpers/auth.js';

test.describe('enterprise area', () => {
  test.beforeEach(async ({ page }) => {
    await login(page, credentials.enterprise);
    await expect(page).toHaveURL(/\/entreprise/);
  });

  test('enterprise core pages load', async ({ page }) => {
    test.setTimeout(60_000);

    const pages = [
      '/entreprise',
      '/entreprise/offres/create',
      '/entreprise/historique',
      '/entreprise/candidatures-ia',
    ];

    for (const path of pages) {
      const response = await page.goto(path);
      await page.waitForLoadState('domcontentloaded');
      await expectHealthyResponse(response, page);
    }
  });

  test('enterprise navigation exposes French and English options', async ({ page }) => {
    const response = await page.goto('/entreprise');
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

  test('enterprise dashboard renders real offer cards and actions', async ({ page }) => {
    const response = await page.goto('/entreprise');
    await expectHealthyResponse(response, page);

    await expect(page.getByRole('link', { name: /Ajouter une offre/i })).toBeVisible();
    await expect(page.locator('a[href*="/entreprise/offres/"][href*="/candidatures"]').first()).toBeVisible();
    await expect(page.locator('a[href*="/entreprise/offres/"][href*="/edit"]').first()).toBeVisible();
    await expect(page.locator('body')).not.toContainText(/Rédacteur SEO|Designer UX\/UI/i);
  });

  test('enterprise offers search stays on dashboard with query parameters', async ({ page }) => {
    const response = await page.goto('/entreprise');
    await expectHealthyResponse(response, page);

    const searchForm = page.getByTestId('enterprise-offers-search-form');
    await expect(searchForm).toBeVisible();

    await searchForm.getByPlaceholder('Rechercher une offre...').fill('dev');
    await Promise.all([
      page.waitForURL(/\/entreprise\?search=dev/),
      searchForm.getByPlaceholder('Rechercher une offre...').press('Enter'),
    ]);

    await expect(page).toHaveURL(/\/entreprise\?search=dev/);
    await expect(page.locator('body')).toContainText(/Ajouter une offre|Aucune offre/i);
    await expect(page.locator('body')).not.toContainText(/"success":true/i);
  });

  test('enterprise sent to enterprise dashboard when opening candidate area', async ({ page }) => {
    await page.goto('/user');

    await expect(page).toHaveURL(/\/entreprise$/);
    await expect(page.locator('body')).not.toContainText(/403/i);
  });
});
