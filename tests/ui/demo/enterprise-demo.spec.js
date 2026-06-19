import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from '../helpers/assertions.js';
import { credentials, login } from '../helpers/auth.js';
import { uniqueSuffix } from '../helpers/provisioning.js';

async function createOffer(page, jobTitle) {
  await page.goto('/entreprise/offres/create');
  await expect(page.getByLabel(/Titre du poste/i)).toBeVisible();

  await page.getByLabel(/Titre du poste/i).fill(jobTitle);
  await page.getByLabel(/Type de contrat/i).selectOption({ index: 1 });
  await page.getByLabel(/Secteur d'activité/i).selectOption({ index: 1 });
  await page.getByLabel(/Lieu de travail/i).fill('Montreal');
  await page.locator('#remote_work').selectOption('Hybride');
  await page.locator('#job_category').selectOption('informatique');
  await page.locator('#salary_type').selectOption('annuel');
  await page.locator('#start_date').selectOption('Immédiate');
  await page.getByLabel(/Description du poste/i).fill(`Description de demo pour ${jobTitle}`);
  await page.getByLabel(/Responsabilités principales/i).fill(`Responsabilites de demo pour ${jobTitle}`);
  await page.getByLabel(/Salaire minimum/i).fill('70000');
  await page.getByLabel(/Salaire maximum/i).fill('95000');
  await page.getByLabel(/Expérience requise/i).selectOption('2-3 ans');
  await page.getByLabel(/Date d'expiration/i).fill('2026-12-31');
  await page.getByLabel(/Langues demandées/i).fill('Francais, Anglais');
  await page.getByRole('button', { name: /Publier l'offre|Publier/i }).last().click();
}

test.describe('Demo Entreprise', () => {
  test.beforeEach(async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'La demo entreprise est ecrite pour le parcours desktop.');

    await login(page, credentials.enterprise);
    await expect(page).toHaveURL(/\/entreprise/);
  });

  test('demo entreprise: dashboard, creation offre, recherche et candidatures', async ({ page }) => {
    test.setTimeout(90_000);

    let response = await page.goto('/entreprise');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).toContainText(/Ajouter une offre|Aucune offre/i);

    const offerTitle = `Offre Demo Entreprise ${uniqueSuffix()}`;
    await createOffer(page, offerTitle);

    await expect(page).toHaveURL(/\/entreprise$/);
    await expect(page.locator('body')).toContainText(/Offre créée avec succès|matching IA/i);

    const searchForm = page.getByTestId('enterprise-offers-search-form');
    await expect(searchForm).toBeVisible();
    await searchForm.getByPlaceholder('Rechercher une offre...').fill(offerTitle);
    await Promise.all([
      page.waitForURL(/\/entreprise\?search=/),
      searchForm.getByPlaceholder('Rechercher une offre...').press('Enter'),
    ]);

    await expect(page.locator('body')).toContainText(offerTitle);

    const candidatesLink = page.locator('a[href*="/entreprise/offres/"][href*="/candidatures"]').first();
    await expect(candidatesLink).toBeVisible();
    await Promise.all([
      page.waitForURL(/\/entreprise\/offres\/[^/]+\/candidatures/),
      candidatesLink.click(),
    ]);

    await expect(page.locator('body')).toContainText(/Candidatures pour/i);
    await expect(page.getByRole('link', { name: /Retour aux offres/i })).toBeVisible();
  });
});
