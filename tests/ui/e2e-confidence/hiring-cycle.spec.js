import { expect, test } from '@playwright/test';
import { fileURLToPath } from 'node:url';
import { expectHealthyResponse } from '../helpers/assertions.js';
import { login, logout } from '../helpers/auth.js';
import {
  approveEnterpriseForLogin,
  buildDemoAccounts,
  getOfferRecordByTitle,
  getPostulationStatusFor,
  getUserStatus,
  getVerificationToken,
  registerCandidate,
  registerEnterprise,
  verifyCandidateForLogin,
} from '../helpers/provisioning.js';

const CV_FIXTURE_PATH = fileURLToPath(new URL('../fixtures/candidate-cv-demo.pdf', import.meta.url));
const LETTER_FIXTURE_PATH = fileURLToPath(
  new URL('../fixtures/candidate-letter-demo.pdf', import.meta.url),
);

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
  await page.getByLabel(/Description du poste/i).fill(`Description E2E pour ${jobTitle}`);
  await page.getByLabel(/Responsabilités principales/i).fill(`Responsabilites E2E pour ${jobTitle}`);
  await page.getByLabel(/Salaire minimum/i).fill('72000');
  await page.getByLabel(/Salaire maximum/i).fill('98000');
  await page.getByLabel(/Expérience requise/i).selectOption('2-3 ans');
  await page.getByLabel(/Date d'expiration/i).fill('2026-12-31');
  await page.getByLabel(/Langues demandées/i).fill('Francais, Anglais');

  await page.getByRole('button', { name: /Publier l'offre|Publier/i }).last().click();
  await expect(page).toHaveURL(/\/entreprise$/);
}

async function applyToOffer(page, candidateEmail, offerRecord) {
  const response = await page.goto(`/offres/${offerRecord.slug}`);
  await expectHealthyResponse(response, page);

  const applyButton = page.getByRole('button', { name: /Postuler maintenant/i });
  await expect(applyButton).toBeVisible();
  await applyButton.click();

  await expect(page.locator('#applicationModal')).toBeVisible();

  const nextButton = page.locator('#nextBtn');
  await nextButton.click();

  await page.locator('#cv-upload').setInputFiles(CV_FIXTURE_PATH);
  await page.locator('#motivation-upload').setInputFiles(LETTER_FIXTURE_PATH);

  await nextButton.click();
  await expect(page.locator('#step-3.active')).toBeVisible();
  await page.locator('#applicationModal #terms').check({ force: true });
  let latestDialogMessage = '';
  const dialogHandler = async (dialog) => {
    latestDialogMessage = dialog.message();
    await dialog.dismiss();
  };
  page.on('dialog', dialogHandler);
  const submissionResponsePromise = page.waitForResponse(
    (response) =>
      response.request().method() === 'POST' && response.url().includes('/candidature/store'),
    { timeout: 5000 },
  ).catch(() => null);
  await nextButton.click();
  const submissionResponse = await submissionResponsePromise;
  page.off('dialog', dialogHandler);
  if (latestDialogMessage) {
    throw new Error(`Erreur candidature UI: ${latestDialogMessage}`);
  }
  expect(submissionResponse, `Aucune requete POST /candidature/store pour ${offerRecord.title}`).not.toBeNull();
  if (!submissionResponse.ok()) {
    const responseBody = await submissionResponse.text().catch(() => '[corps de reponse indisponible]');
    throw new Error(
      `Echec POST /candidature/store pour ${candidateEmail} sur "${offerRecord.title}" `
        + `(status ${submissionResponse.status()}): ${responseBody}`,
    );
  }

  await expect
    .poll(() => getPostulationStatusFor(candidateEmail, offerRecord.title), {
      timeout: 10000,
    })
    .toBe('en_attente');
}

test.describe.serial('E2E Confiance', () => {
  test('cycle principal: comptes crees, offre, candidature, acceptation, refus et notifications', async ({ page }) => {
    test.setTimeout(180_000);
    test.skip((page.viewportSize()?.width || 0) < 768, 'Le scenario E2E confiance est ecrit pour le parcours desktop.');

    const accounts = buildDemoAccounts();
    const acceptedOfferTitle = `Offre Confiance Acceptation ${accounts.suffix}`;
    const rejectedOfferTitle = `Offre Confiance Refus ${accounts.suffix}`;

    await registerCandidate(page, accounts.candidate, accounts.password);
    const candidateToken = getVerificationToken(accounts.candidate.email);
    expect(candidateToken).not.toBe('');
    await page.goto(`/verify-email/${candidateToken}`);
    verifyCandidateForLogin(accounts.candidate.email);
    expect(getUserStatus(accounts.candidate.email)).toContain('Actif|verified');
    await logout(page);

    await registerEnterprise(page, accounts.enterprise, accounts.password);
    const enterpriseToken = getVerificationToken(accounts.enterprise.email);
    expect(enterpriseToken).not.toBe('');
    await page.goto(`/verify-email/${enterpriseToken}`);
    approveEnterpriseForLogin(accounts.enterprise.email);
    expect(getUserStatus(accounts.enterprise.email)).toContain('Actif|verified');

    await login(page, {
      email: accounts.enterprise.email,
      password: accounts.password,
    });
    await expect(page).toHaveURL(/\/entreprise/);

    await createOffer(page, acceptedOfferTitle);
    await createOffer(page, rejectedOfferTitle);

    const acceptedOffer = getOfferRecordByTitle(acceptedOfferTitle);
    const rejectedOffer = getOfferRecordByTitle(rejectedOfferTitle);
    expect(acceptedOffer).not.toBeNull();
    expect(rejectedOffer).not.toBeNull();

    await logout(page);

    await login(page, {
      email: accounts.candidate.email,
      password: accounts.password,
    });
    await expect(page).toHaveURL(/\/user/);

    await applyToOffer(page, accounts.candidate.email, acceptedOffer);
    await applyToOffer(page, accounts.candidate.email, rejectedOffer);

    let response = await page.goto('/user/historique-candidatures');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).toContainText(acceptedOfferTitle);
    await expect(page.locator('body')).toContainText(rejectedOfferTitle);

    await logout(page);

    await login(page, {
      email: accounts.enterprise.email,
      password: accounts.password,
    });
    await expect(page).toHaveURL(/\/entreprise/);

    response = await page.goto(`/entreprise/offres/${acceptedOffer.id}/candidatures`);
    await expectHealthyResponse(response, page);
    const acceptedRow = page.locator('.card-glow').filter({ hasText: accounts.candidate.email }).first();
    await expect(acceptedRow).toBeVisible();
    await acceptedRow.getByRole('button', { name: /Accepter/i }).click();
    await expect(acceptedRow).toContainText(/Acceptée/i);

    response = await page.goto(`/entreprise/offres/${rejectedOffer.id}/candidatures`);
    await expectHealthyResponse(response, page);
    const rejectedRow = page.locator('.card-glow').filter({ hasText: accounts.candidate.email }).first();
    await expect(rejectedRow).toBeVisible();
    await rejectedRow.getByRole('button', { name: /Rejeter/i }).click();
    await expect(rejectedRow).toContainText(/Rejetée/i);

    await logout(page);

    await login(page, {
      email: accounts.candidate.email,
      password: accounts.password,
    });
    await expect(page).toHaveURL(/\/user/);

    response = await page.goto('/notifications');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).toContainText(/Notifications/i);

    response = await page.goto('/user/historique-candidatures');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).toContainText(/Acceptée/i);
    await expect(page.locator('body')).toContainText(/Refusée/i);
  });
});
