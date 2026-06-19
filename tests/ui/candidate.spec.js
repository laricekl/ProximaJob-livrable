import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from './helpers/assertions.js';
import { credentials, login } from './helpers/auth.js';

test.describe('candidate area', () => {
  test.beforeEach(async ({ page }) => {
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);
  });

  test('candidate core pages load', async ({ page }) => {
    test.setTimeout(60_000);

    const pages = [
      '/user',
      '/user/historique-candidatures',
      '/user/historique-candidatures_ia',
      '/user/profil-public',
      '/user/detail-candidature',
      '/notifications',
    ];

    for (const path of pages) {
      const response = await page.goto(path);
      await page.waitForLoadState('domcontentloaded');
      await expectHealthyResponse(response, page);
    }
  });

  test('candidate navigation exposes French and English options', async ({ page }) => {
    const response = await page.goto('/user');
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

  test('candidate sent to candidate dashboard when opening enterprise area', async ({ page }) => {
    await page.goto('/entreprise');

    await expect(page).toHaveURL(/\/user$/);
    await expect(page.locator('body')).not.toContainText(/403/i);
  });

  test('candidate AI history renders real data area instead of old mock rows', async ({ page }) => {
    const response = await page.goto('/user/historique-candidatures_ia');
    await expectHealthyResponse(response, page);

    await expect(page.locator('body')).toContainText(/Candidatures automatiques IA/i);
    await expect(page.getByTestId('candidate-ai-history-filter-form')).toBeVisible();
    await expect(page.locator('body')).not.toContainText(/DataProd|DesignLab|InnovaGroup/i);
  });
});
