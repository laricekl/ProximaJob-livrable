import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from './helpers/assertions.js';
import { getUserStatus, getVerificationToken } from './helpers/verification.js';

const password = 'Password123!';

function uniqueSuffix() {
  return `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
}

test.describe('real account creation flows', () => {
  test('candidate real flow: register, verify email, access candidate area', async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'Real account creation flow runs on desktop');

    const suffix = uniqueSuffix();
    const email = `candidate-${suffix}@example.test`;
    const phone = `514${Date.now().toString().slice(-7)}`;

    let response = await page.goto('/register');
    await expectHealthyResponse(response, page);

    await page.getByLabel('Nom *').fill('FlowCandidate');
    await page.getByLabel('Prénoms *').fill('QA');
    await page.getByLabel('Adresse e-mail *').fill(email);
    await page.getByLabel('Téléphone *').fill(phone);
    await page.getByLabel('Adresse *').fill('123 Rue QA');
    await page.getByLabel('Mot de passe *').fill(password);
    await page.getByLabel('Confirmer *').fill(password);
    await page.getByLabel(/J'accepte les/i).check();

    await Promise.all([
      page.waitForURL(/\/register$/),
      page.getByRole('button', { name: /Créer mon compte/i }).click(),
    ]);

    await expect(page.locator('body')).toContainText(/lien de vérification|verification/i);

    const token = getVerificationToken(email);
    expect(token, 'Candidate verification token should be created').not.toBe('');

    response = await page.goto(`/verify-email/${token}`);
    await expectHealthyResponse(response, page);
    await page.waitForLoadState('domcontentloaded');

    expect(getUserStatus(email)).toContain('verified');

    response = await page.goto('/user');
    await expectHealthyResponse(response, page);
    await expect(page).toHaveURL(/\/user$/);
    await expect(page.locator('body')).not.toContainText(/403|Votre compte est inactif/i);
  });

  test('enterprise real flow: register, verify email, stay pending admin validation', async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'Real account creation flow runs on desktop');

    const suffix = uniqueSuffix();
    const email = `enterprise-${suffix}@example.test`;
    const phone = `438${Date.now().toString().slice(-7)}`;
    const company = `QA Entreprise ${suffix}`;

    let response = await page.goto('/entreprise/register');
    await expectHealthyResponse(response, page);

    await page.getByLabel('Nom *').fill('FlowEnterprise');
    await page.getByLabel('Prénoms *').fill('QA');
    await page.getByLabel('Adresse e-mail *').fill(email);
    await page.getByLabel('Téléphone *').fill(phone);
    await page.getByLabel("Nom de l'entreprise *").fill(company);
    await page.locator('#rccm').fill(`RCCM-${suffix}`);
    await page.locator('#neq').fill(`NEQ-${suffix}`);
    await page.getByLabel('Adresse *').fill('456 Avenue QA');
    await page.getByLabel('Mot de passe *').fill(password);
    await page.getByLabel('Confirmer *').fill(password);
    await page.getByLabel(/J'accepte les/i).check();

    await Promise.all([
      page.waitForURL(/\/email\/verify$/),
      page.getByRole('button', { name: /Créer mon compte entreprise/i }).click(),
    ]);

    const token = getVerificationToken(email);
    expect(token, 'Enterprise verification token should be created').not.toBe('');

    response = await page.goto(`/verify-email/${token}`);
    await expectHealthyResponse(response, page);
    await page.waitForLoadState('domcontentloaded');

    expect(getUserStatus(email)).toContain('pending|verified');

    response = await page.goto('/login');
    await expectHealthyResponse(response, page);
    await page.getByLabel(/adresse e-mail/i).fill(email);
    await page.getByLabel(/mot de passe/i).fill(password);
    await page.getByRole('button', { name: /se connecter/i }).click();
    await page.waitForLoadState('domcontentloaded');

    await expect(page).toHaveURL(/\/login$/);
    await expect(page.locator('body')).toContainText(/attente de validation administrative/i);
    await expect(page.locator('body')).not.toContainText(/403/i);
  });
});
