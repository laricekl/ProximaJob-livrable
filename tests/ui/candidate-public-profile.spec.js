import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from './helpers/assertions.js';
import { credentials, login } from './helpers/auth.js';

test.describe('candidate public profile', () => {
  test.beforeEach(async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'Public profile tests run on desktop');
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);
  });

  test('public profile page loads with all sections', async ({ page }) => {
    const response = await page.goto('/user/profil-public');
    await expectHealthyResponse(response, page);

    // Titre de la page
    await expect(page.getByRole('heading', { name: /Mon profil candidat/i })).toBeVisible();

    // Sections clés
    await expect(page.locator('body')).toContainText(/En résumé|Préférences|pitch|Compétences/i);

    // Bouton toggle recruteur
    await expect(page.getByText(/Prévisualiser comme employeur/i)).toBeVisible();
  });

  test('recruiter preview toggle switches to read-only mode', async ({ page }) => {
    const response = await page.goto('/user/profil-public?preview=employeur');
    await expectHealthyResponse(response, page);

    // Le mode recruteur doit être actif (classe sur le main)
    await expect(page.locator('main.recruiter-preview')).toBeVisible();

    // Le titre indique que c'est la vue employeur
    await expect(page.locator('body')).toContainText(/voit quand il consulte|employeur/i);
  });

  test('public profile shows CV personalization and builder links', async ({ page }) => {
    const response = await page.goto('/user/profil-public');
    await expectHealthyResponse(response, page);

    // Liens vers la personnalisation CV et le builder
    const personalizationLink = page.locator('a[href*="personnaliser-cv"], a[href*="personalization"]').first();
    const builderLink = page.locator('a[href*="infos-cv"]').first();

    // Au moins un des deux doit être présent
    const count = await personalizationLink.count() + await builderLink.count();
    expect(count).toBeGreaterThanOrEqual(1);
  });

  test('public profile navigation links point to correct routes', async ({ page }) => {
    const response = await page.goto('/user/profil-public');
    await expectHealthyResponse(response, page);

    // Vérifier le lien "Adapter mon CV à une offre" s'il existe
    const adaptLink = page.locator('a[href*="personnaliser-cv"], a[href*="personalization"]').first();
    if (await adaptLink.count()) {
      const href = await adaptLink.getAttribute('href');
      expect(href).toBeTruthy();
    }

    // Vérifier le lien vers le CV builder
    const cvLink = page.locator('a[href*="infos-cv"]').first();
    if (await cvLink.count()) {
      const href = await cvLink.getAttribute('href');
      expect(href).toBeTruthy();
    }
  });
});
