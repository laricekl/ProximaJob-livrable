import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from './helpers/assertions.js';
import { credentials, login } from './helpers/auth.js';

test.describe('enterprise A-Z journeys', () => {
  test.beforeEach(async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'A-Z journeys run on desktop; smoke suite covers mobile');

    await login(page, credentials.enterprise);
    await expect(page).toHaveURL(/\/entreprise/);
  });

  test('enterprise journey 1: search offers, keep HTML dashboard, reset query', async ({ page }) => {
    let response = await page.goto('/entreprise');
    await expectHealthyResponse(response, page);

    const form = page.getByTestId('enterprise-offers-search-form');
    await expect(form).toBeVisible();

    await form.getByPlaceholder('Rechercher une offre...').fill('dev');
    await Promise.all([
      page.waitForURL(/\/entreprise\?search=dev/),
      form.getByPlaceholder('Rechercher une offre...').press('Enter'),
    ]);

    await expect(page.locator('body')).toContainText(/Ajouter une offre|Aucune offre/i);
    await expect(page.locator('body')).not.toContainText(/"success":true/i);

    response = await page.goto('/entreprise');
    await expectHealthyResponse(response, page);
    await expect(page).toHaveURL(/\/entreprise$/);
  });

  test('enterprise journey 2: open create offer form and validate required fields without creating data', async ({ page }) => {
    const response = await page.goto('/entreprise/offres/create');
    await expectHealthyResponse(response, page);

    await expect(page.getByLabel(/Titre du poste/i)).toBeVisible();
    await expect(page.getByLabel(/Type de contrat/i)).toBeVisible();
    await expect(page.getByLabel(/Lieu de travail/i)).toBeVisible();

    await page.getByRole('button', { name: /Publier/i }).first().click();
    await expect(page).toHaveURL(/\/entreprise\/offres\/create/);
    await expect(page.getByLabel(/Titre du poste/i)).toBeVisible();
  });

  test('enterprise journey 3: open first offer candidates, filter list, return to offers', async ({ page }) => {
    let response = await page.goto('/entreprise');
    await expectHealthyResponse(response, page);

    const candidatesLink = page.locator('a[href*="/entreprise/offres/"][href*="/candidatures"]').first();
    await expect(candidatesLink).toBeVisible();
    await Promise.all([
      page.waitForURL(/\/entreprise\/offres\/[^/]+\/candidatures/),
      candidatesLink.click(),
    ]);

    await expect(page.locator('body')).toContainText(/Candidatures pour/i);
    await page.locator('select[name="status"]').selectOption('en_attente');
    await page.getByLabel('Rechercher').fill('test');
    await Promise.all([
      page.waitForURL(/status=en_attente/),
      page.getByRole('button', { name: /Filtrer/i }).click(),
    ]);

    await expect(page).toHaveURL(/search=test/);
    await page.getByRole('link', { name: /Retour aux offres/i }).click();
    await expect(page).toHaveURL(/\/entreprise$/);
  });

  test('enterprise journey 4: review AI candidates area and enforce role guard', async ({ page }) => {
    let response = await page.goto('/entreprise/candidatures-ia');
    await expectHealthyResponse(response, page);

    await expect(page.locator('body')).toContainText(/Candidatures IA/i);
    await expect(page.locator('body')).toContainText(/Postulations automatiques|Aucune candidature IA|candidats matchés/i);

    await page.goto('/user');
    await expect(page).toHaveURL(/\/entreprise$/);
    await expect(page.locator('body')).not.toContainText(/403/i);
  });
});
