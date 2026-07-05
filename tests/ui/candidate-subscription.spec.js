import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from './helpers/assertions.js';
import { credentials, login } from './helpers/auth.js';

test.describe('candidate subscription', () => {
  test.beforeEach(async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'Subscription tests run on desktop');
  });

  test('subscription plans page shows three tiers with monthly/yearly toggle', async ({ page }) => {
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);

    const response = await page.goto('/user/plan-abonnement');
    await expectHealthyResponse(response, page);

    // Titre de la page
    await expect(page.getByRole('heading', { name: /Choisissez votre Plan/i })).toBeVisible();

    // Les trois plans
    await expect(page.locator('body')).toContainText(/Basic/i);
    await expect(page.locator('body')).toContainText(/Standard/i);
    await expect(page.locator('body')).toContainText(/Premium/i);

    // Toggle Mensuel/Annuel
    await expect(page.getByRole('button', { name: /Mensuel/i })).toBeVisible();
    await expect(page.getByRole('button', { name: /Annuel/i })).toBeVisible();

    // Pas d'erreur
    await expect(page.locator('body')).not.toContainText(/403|500/);
  });

  test('monthly/yearly toggle switches prices', async ({ page }) => {
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);

    await page.goto('/user/plan-abonnement');

    // Vérifier que les prix mensuels sont visibles par défaut
    const monthlyPrices = page.locator('.monthly-price');
    await expect(monthlyPrices.first()).toBeVisible();

    // Cliquer sur Annuel
    await page.getByRole('button', { name: /Annuel/i }).click();
    await page.waitForTimeout(500);

    // Les prix annuels devraient être visibles
    const yearlyPrices = page.locator('.yearly-price');
    await expect(yearlyPrices.first()).toBeVisible();

    // Revenir au mensuel
    await page.getByRole('button', { name: /Mensuel/i }).click();
    await page.waitForTimeout(300);
    await expect(monthlyPrices.first()).toBeVisible();
  });

  test('my subscription page loads and shows filter tabs', async ({ page }) => {
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);

    const response = await page.goto('/user/abonnement');
    await expectHealthyResponse(response, page);

    // Tabs de filtre
    await expect(page.getByRole('button', { name: /Tous les abonnements/i })).toBeVisible();
    await expect(page.getByRole('button', { name: /Actifs/i })).toBeVisible();
    await expect(page.getByRole('button', { name: /Expirés|Expires/i })).toBeVisible();

    // Bouton pour souscrire
    await expect(page.locator('body')).toContainText(/Nouvel Abonnement|plan.abonnement/i);

    // Pas d'erreur
    await expect(page.locator('body')).not.toContainText(/403|500/);
  });
});
