import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from '../helpers/assertions.js';
import { credentials, login } from '../helpers/auth.js';

test.describe('Demo Candidat', () => {
  test.beforeEach(async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'La demo candidat est ecrite pour le parcours desktop.');

    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);
  });

  test('demo candidat: connexion, dashboard, offres, historiques, notifications et profil', async ({ page }) => {
    test.setTimeout(60_000);

    let response = await page.goto('/user');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).toContainText(/Bonjour|Tableau de bord|Espace candidat/i);

    response = await page.goto('/offres');
    await expectHealthyResponse(response, page);

    const offerLink = page.locator('a[href^="/offres/"], a[href*="/offres/"]').filter({ hasText: /.+/ }).first();
    await expect(offerLink).toBeVisible();
    await Promise.all([
      page.waitForURL(/\/offres\/[^/]+$/),
      offerLink.click(),
    ]);

    await expect(page.locator('main h1').first()).toBeVisible();
    await expect(page.locator('body')).toContainText(/Postuler maintenant|Retour aux offres/i);

    response = await page.goto('/user/historique-candidatures');
    await expectHealthyResponse(response, page);

    const manualHistoryForm = page.getByTestId('candidate-manual-history-filter-form');
    await expect(manualHistoryForm).toBeVisible();
    await manualHistoryForm.locator('select[name="status"]').selectOption('En attente');
    await manualHistoryForm.getByPlaceholder('Poste ou entreprise...').fill('dev');
    await Promise.all([
      page.waitForURL(/\/user\/historique-candidatures\?.*keyword=dev/),
      manualHistoryForm.getByRole('button', { name: /Filtrer/i }).click(),
    ]);

    response = await page.goto('/user/historique-candidatures_ia');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).toContainText(/Candidatures automatiques IA/i);
    await expect(page.getByTestId('candidate-ai-history-filter-form')).toBeVisible();

    response = await page.goto('/notifications');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).not.toContainText(/403|Page Expired/i);

    response = await page.goto('/user/profil-public');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).not.toContainText(/403|Page Expired/i);
  });
});
