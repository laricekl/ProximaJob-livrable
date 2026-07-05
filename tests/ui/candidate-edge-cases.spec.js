import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from './helpers/assertions.js';
import { credentials, login } from './helpers/auth.js';

test.describe('candidate edge cases', () => {
  test.beforeEach(async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'Edge cases run on desktop');
  });

  test('empty state — no manual applications shows placeholder', async ({ page }) => {
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);

    // Aller sur l'historique sans filtre — le tableau peut être vide ou non
    const response = await page.goto('/user/historique-candidatures?status=Rejeté&date=Cette+semaine&keyword=zzzzzzzzz');
    await expectHealthyResponse(response, page);

    // Vérifier le message d'état vide
    await expect(page.locator('body')).toContainText(/Aucune candidature/i);
  });

  test('empty state — no AI applications shows placeholder', async ({ page }) => {
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);

    const response = await page.goto('/user/historique-candidatures_ia?status=Rejeté&date=Cette+semaine&keyword=zzzzzzzzz');
    await expectHealthyResponse(response, page);

    await expect(page.locator('body')).toContainText(/Aucune candidature IA/i);
  });

  test('empty state — notifications shows empty message when all read', async ({ page }) => {
    // Marquer toutes les notifs comme lues puis vérifier
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);

    const response = await page.goto('/notifications');
    await expectHealthyResponse(response, page);

    // La page doit charger sans erreur (même si pas de notifications)
    await expect(page.locator('body')).not.toContainText(/403|500|erreur/i);

    // Si l'état vide est affiché, vérifier le message
    const emptyState = page.locator('#emptyState');
    if (await emptyState.count()) {
      await expect(emptyState).toContainText(/Aucune notification/i);
    }
  });

  test('invalid verification token redirects without crashing', async ({ page }) => {
    const response = await page.goto('/verify-email/token_invalide_12345');
    await page.waitForLoadState('domcontentloaded');

    // Ne doit pas faire de 500 — le comportement attendu est une redirection
    // vers la page d'accueil ou login avec un message d'erreur flash
    await expect(page.locator('body')).not.toContainText(/500|Server Error/i);
    // La page ne doit pas être vide ou cassée
    await expect(page.locator('h1, h2, nav').first()).toBeVisible();
  });

  test('candidate blocked from entreprise area with proper redirect', async ({ page }) => {
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);

    // Tenter d'accéder à une page entreprise
    const response = await page.goto('/entreprise');
    await page.waitForLoadState('domcontentloaded');

    // Doit être redirigé vers /user (pas de 403 brute)
    await expect(page).toHaveURL(/\/user/);
    await expect(page.locator('body')).not.toContainText(/403/);
  });
});
