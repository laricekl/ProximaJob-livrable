import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from './helpers/assertions.js';
import { credentials, login } from './helpers/auth.js';

test.describe('candidate A-Z journeys', () => {
  test.beforeEach(async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'A-Z journeys run on desktop; smoke suite covers mobile');

    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);
  });

  test('candidate journey 1: discover offers, open a real detail page, see apply CTA', async ({ page }) => {
    let response = await page.goto('/offres');
    await expectHealthyResponse(response, page);

    const offerLink = page.locator('a[href^="/offres/"], a[href*="/offres/"]').filter({ hasText: /.+/ }).first();
    await expect(offerLink).toBeVisible();
    await Promise.all([
      page.waitForURL(/\/offres\/[^/]+$/),
      offerLink.click(),
    ]);

    await expect(page.locator('main h1').first()).toBeVisible();
    await expect(page.locator('body')).toContainText(/Postuler maintenant|Se connecter pour postuler|Retour aux offres/i);
    await expect(page.locator('body')).not.toContainText(/TechCorp|Développeur Full Stack passionné/i);
  });

  test('candidate journey 2: filter manual applications and reset', async ({ page }) => {
    let response = await page.goto('/user/historique-candidatures');
    await expectHealthyResponse(response, page);

    const form = page.getByTestId('candidate-manual-history-filter-form');
    await expect(form).toBeVisible();

    await form.locator('select[name="status"]').selectOption('En attente');
    await form.locator('select[name="date"]').selectOption('Ce mois');
    await form.getByPlaceholder('Poste ou entreprise...').fill('dev');
    await Promise.all([
      page.waitForURL(/\/user\/historique-candidatures\?.*status=En\+attente/),
      form.getByRole('button', { name: /Filtrer/i }).click(),
    ]);

    await expect(page).toHaveURL(/keyword=dev/);
    await page.getByRole('link', { name: /Réinitialiser/i }).click();
    await expect(page).toHaveURL(/\/user\/historique-candidatures$/);
  });

  test('candidate journey 3: filter AI applications and keep real-data rendering', async ({ page }) => {
    let response = await page.goto('/user/historique-candidatures_ia');
    await expectHealthyResponse(response, page);

    const form = page.getByTestId('candidate-ai-history-filter-form');
    await expect(form).toBeVisible();

    await form.locator('select[name="status"]').selectOption('En attente');
    await form.getByPlaceholder('Poste ou entreprise...').fill('dev');
    await Promise.all([
      page.waitForURL(/\/user\/historique-candidatures_ia\?.*keyword=dev/),
      form.getByRole('button', { name: /Filtrer/i }).click(),
    ]);

    await expect(page.locator('body')).toContainText(/Candidatures automatiques IA/i);
    await expect(page.locator('body')).not.toContainText(/DataProd|DesignLab|InnovaGroup/i);
  });

  test('candidate journey 4: visit profile, detail candidature, notifications, then enforce role guard', async ({ page }) => {
    for (const path of ['/user/profil-public', '/user/detail-candidature', '/notifications']) {
      const response = await page.goto(path);
      await expectHealthyResponse(response, page);
      await expect(page.locator('body')).not.toContainText(/403|Page Expired/i);
    }

    await page.goto('/entreprise');
    await expect(page).toHaveURL(/\/user$/);
    await expect(page.locator('body')).not.toContainText(/403/i);
  });
});
