import { execFileSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';
import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from '../helpers/assertions.js';
import { login, logout } from '../helpers/auth.js';
import {
  approveEnterpriseForLogin as approveEnterpriseForLoginLocal,
  buildDemoAccounts,
  getOfferRecordByTitle as getOfferRecordByTitleLocal,
  getVerificationToken as getVerificationTokenLocal,
  verifyCandidateForLogin as verifyCandidateForLoginLocal,
} from '../helpers/provisioning.js';

const CLOUD_ENVIRONMENT_ID = process.env.UI_CLOUD_ENVIRONMENT_ID || 'env-a20eecae-81b4-4f72-9e10-5091ea0a4f7a';
const USE_CLOUD_HELPERS =
  process.env.UI_USE_CLOUD_HELPERS === '1'
  || /laravel\.cloud/.test(process.env.UI_BASE_URL || '');
const CV_FIXTURE_PATH = fileURLToPath(new URL('../fixtures/candidate-cv-demo.pdf', import.meta.url));
const LETTER_FIXTURE_PATH = fileURLToPath(
  new URL('../fixtures/candidate-letter-demo.pdf', import.meta.url),
);

function escapePhpString(value) {
  return value.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

function parseLastJsonLine(rawOutput) {
  const lines = rawOutput
    .split(/\r?\n/)
    .map((line) => line.trim())
    .filter(Boolean);

  for (let index = lines.length - 1; index >= 0; index -= 1) {
    try {
      return JSON.parse(lines[index]);
    } catch {
      // Try previous line until we find the final JSON payload.
    }
  }

  throw new Error(`Impossible de parser la sortie cloud:\n${rawOutput}`);
}

function runCloudCommand(command) {
  const rawOutput = execFileSync(
    'cloud',
    ['command:run', CLOUD_ENVIRONMENT_ID, `--cmd=${command}`, '--json'],
    {
      cwd: process.cwd(),
      encoding: 'utf8',
    },
  );

  const payload = parseLastJsonLine(rawOutput);

  if (payload.exitCode !== 0) {
    throw new Error(`Commande cloud en echec: ${command}\n${payload.output ?? rawOutput}`);
  }

  return payload.output ?? '';
}

function runCloudExpression(expression) {
  const normalizedExpression = expression
    .replace(/\s+/g, ' ')
    .trim()
    .replace(/\$/g, '\\$')
    .replace(/"/g, '\\"');
  const command = `php artisan tinker --execute="${normalizedExpression}"`;
  return runCloudCommand(command).trim();
}

function getVerificationToken(email) {
  if (!USE_CLOUD_HELPERS) {
    return getVerificationTokenLocal(email);
  }

  const safeEmail = escapePhpString(email);

  return runCloudExpression(
    `echo App\\\\Models\\\\EmailVerification::where('email', '${safeEmail}')->latest('created_at')->value('token') ?? '';`,
  ).trim();
}

function verifyCandidateForLogin(email) {
  if (!USE_CLOUD_HELPERS) {
    verifyCandidateForLoginLocal(email);
    return;
  }

  const safeEmail = escapePhpString(email);

  runCloudExpression(`
    $user = App\\\\Models\\\\User::where('email', '${safeEmail}')->first();
    if ($user) {
      $user->email_verified_at = now();
      $user->status = 'Actif';
      $user->save();
    }
    App\\\\Models\\\\EmailVerification::where('email', '${safeEmail}')->delete();
    echo 'ok';
  `);
}

function approveEnterpriseForLogin(email) {
  if (!USE_CLOUD_HELPERS) {
    approveEnterpriseForLoginLocal(email);
    return;
  }

  const safeEmail = escapePhpString(email);

  runCloudExpression(`
    $user = App\\\\Models\\\\User::where('email', '${safeEmail}')->first();
    if ($user) {
      $user->email_verified_at = now();
      $user->status = 'Actif';
      $user->save();
      if ($user->entreprise) {
        $user->entreprise->status = 'approved';
        $user->entreprise->verified_at = now();
        $user->entreprise->save();
      }
    }
    App\\\\Models\\\\EmailVerification::where('email', '${safeEmail}')->delete();
    echo 'ok';
  `);
}

function getOfferRecordByTitle(title) {
  if (!USE_CLOUD_HELPERS) {
    return getOfferRecordByTitleLocal(title);
  }

  const safeTitle = escapePhpString(title);
  const raw = runCloudExpression(`
    $offer = App\\\\Models\\\\Offre::where('titre', '${safeTitle}')->latest('id')->first();
    echo $offer ? json_encode(['id' => $offer->id, 'slug' => $offer->slug, 'title' => $offer->titre]) : '';
  `);

  return raw ? JSON.parse(raw) : null;
}

async function registerCandidate(page, account, password) {
  await page.goto('/register');

  await page.getByLabel('Nom *').fill(account.lastName);
  await page.getByLabel('Prénoms *').fill(account.firstName);
  await page.getByLabel('Adresse e-mail *').fill(account.email);
  await page.getByLabel('Téléphone *').fill(account.phone);
  await page.getByLabel('Adresse *').fill(account.address);
  await page.getByLabel('Mot de passe *').fill(password);
  await page.getByLabel('Confirmer *').fill(password);
  await page.getByLabel(/J'accepte les/i).check();

  await Promise.all([
    page.waitForURL(/\/register$/),
    page.getByRole('button', { name: /Créer mon compte/i }).click(),
  ]);

  await expect(page.locator('body')).toContainText(/vérification|verification/i);
}

async function registerEnterprise(page, account, password) {
  await page.goto('/entreprise/register');

  await page.getByLabel('Nom *').fill(account.lastName);
  await page.getByLabel('Prénoms *').fill(account.firstName);
  await page.getByLabel('Adresse e-mail *').fill(account.email);
  await page.getByLabel('Téléphone *').fill(account.phone);
  await page.getByLabel("Nom de l'entreprise *").fill(account.companyName);
  await page.locator('#rccm').fill(account.rccm);
  await page.locator('#neq').fill(account.neq);
  await page.getByLabel('Adresse *').fill(account.address);
  await page.locator('#rccm_document').setInputFiles({
    name: `rccm-${account.rccm}.pdf`,
    mimeType: 'application/pdf',
    buffer: Buffer.from('demo-rccm-document'),
  });
  await page.getByLabel('Mot de passe *').fill(password);
  await page.getByLabel('Confirmer *').fill(password);
  await page.getByLabel(/J'accepte les/i).check();

  await Promise.all([
    page.waitForURL(/\/email\/verify$/),
    page.getByRole('button', { name: /Créer mon compte entreprise/i }).click(),
  ]);
}

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
  await page.getByLabel(/Description du poste/i).fill(`Description demo live pour ${jobTitle}`);
  await page.getByLabel(/Responsabilités principales/i).fill(`Responsabilites demo live pour ${jobTitle}`);
  await page.getByLabel(/Salaire minimum/i).fill('72000');
  await page.getByLabel(/Salaire maximum/i).fill('98000');
  await page.getByLabel(/Expérience requise/i).selectOption('2-3 ans');
  await page.getByLabel(/Date d'expiration/i).fill('2026-12-31');
  await page.getByLabel(/Langues demandées/i).fill('Francais, Anglais');

  await page.getByRole('button', { name: /Publier l'offre|Publier/i }).last().click();
  await expect(page).toHaveURL(/\/entreprise$/);
}

async function applyToOffer(page, offerRecord) {
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
  await nextButton.click();

  await expect(page.locator('body')).toContainText(/Candidature|succès|envoyée/i);
}

test.describe.serial('Live Handoff Demo', () => {
  test('creation entreprise, creation offre, creation candidat, postulation', async ({ page }) => {
    test.setTimeout(240_000);
    test.skip((page.viewportSize()?.width || 0) < 768, 'La demo live est ecrite pour desktop.');

    const accounts = buildDemoAccounts();
    const password = `SafeDemo!${accounts.suffix.replace(/[^a-zA-Z0-9]/g, '').slice(0, 10)}`;
    const offerTitle = `Offre Live Demo ${accounts.suffix}`;

    await registerEnterprise(page, accounts.enterprise, password);
    const enterpriseToken = getVerificationToken(accounts.enterprise.email);
    expect(enterpriseToken).not.toBe('');
    await page.goto(`/verify-email/${enterpriseToken}`);
    approveEnterpriseForLogin(accounts.enterprise.email);

    await login(page, {
      email: accounts.enterprise.email,
      password,
    });
    await expect(page).toHaveURL(/\/entreprise/);

    await createOffer(page, offerTitle);
    await expect(page.locator('body')).toContainText(offerTitle);

    const offerRecord = getOfferRecordByTitle(offerTitle);
    expect(offerRecord).not.toBeNull();

    await logout(page);

    await registerCandidate(page, accounts.candidate, password);
    const candidateToken = getVerificationToken(accounts.candidate.email);
    expect(candidateToken).not.toBe('');
    await page.goto(`/verify-email/${candidateToken}`);
    verifyCandidateForLogin(accounts.candidate.email);

    await login(page, {
      email: accounts.candidate.email,
      password,
    });
    await expect(page).toHaveURL(/\/user|\/dashboard/);

    await applyToOffer(page, offerRecord);
    await expect(page.locator('body')).toContainText(/Historique|Mes candidatures|Tableau de bord|Profil/i);
  });
});
