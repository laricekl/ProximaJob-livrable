import { execFileSync } from 'node:child_process';
import { expect } from '@playwright/test';

function runLaravelExpression(expression) {
  const bootstrap = [
    "require 'vendor/autoload.php';",
    "$app = require 'bootstrap/app.php';",
    "$app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();",
    expression,
  ].join(' ');

  return execFileSync('php', ['-r', bootstrap], {
    cwd: process.cwd(),
    encoding: 'utf8',
  }).trim();
}

function escapePhpString(value) {
  return value.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

export function uniqueSuffix() {
  return `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
}

export function buildDemoAccounts() {
  const suffix = uniqueSuffix();

  return {
    suffix,
    password: 'Password123!',
    candidate: {
      firstName: 'Demo',
      lastName: 'Candidate',
      email: `candidate-demo-${suffix}@example.test`,
      phone: `514${Date.now().toString().slice(-7)}`,
      address: '123 Rue Demo Montreal',
    },
    enterprise: {
      firstName: 'Demo',
      lastName: 'Entreprise',
      email: `enterprise-demo-${suffix}@example.test`,
      phone: `438${Date.now().toString().slice(-7)}`,
      address: '456 Avenue Demo Montreal',
      companyName: `Entreprise Demo ${suffix}`,
      rccm: `RCCM-${suffix}`,
      neq: `NEQ-${suffix}`,
    },
  };
}

export async function registerCandidate(page, account, password) {
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

export async function registerEnterprise(page, account, password) {
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

export function getVerificationToken(email) {
  const safeEmail = escapePhpString(email);

  return runLaravelExpression(
    `echo App\\Models\\EmailVerification::where('email', '${safeEmail}')->latest('created_at')->value('token') ?? '';`,
  );
}

export function verifyCandidateForLogin(email) {
  const safeEmail = escapePhpString(email);

  runLaravelExpression(`
    $user = App\\Models\\User::where('email', '${safeEmail}')->first();
    if ($user) {
      $user->email_verified_at = now();
      $user->status = 'Actif';
      $user->save();
    }
    App\\Models\\EmailVerification::where('email', '${safeEmail}')->delete();
  `);
}

export function approveEnterpriseForLogin(email) {
  const safeEmail = escapePhpString(email);

  runLaravelExpression(`
    $user = App\\Models\\User::where('email', '${safeEmail}')->first();
    if ($user) {
      $user->email_verified_at = now();
      $user->status = 'Actif';
      $user->save();
      if ($user->entreprise) {
        $user->entreprise->status = 'approved';
        $user->entreprise->save();
      }
    }
    App\\Models\\EmailVerification::where('email', '${safeEmail}')->delete();
  `);
}

export function getUserStatus(email) {
  const safeEmail = escapePhpString(email);

  return runLaravelExpression(
    `$user = App\\Models\\User::where('email', '${safeEmail}')->first(); echo $user ? ($user->status ?? '') . '|' . ($user->email_verified_at ? 'verified' : 'unverified') : '';`,
  );
}

export function getOfferRecordByTitle(title) {
  const safeTitle = escapePhpString(title);
  const raw = runLaravelExpression(`
    $offer = App\\Models\\Offre::where('titre', '${safeTitle}')->latest('id')->first();
    echo $offer ? json_encode(['id' => $offer->id, 'slug' => $offer->slug, 'title' => $offer->titre]) : '';
  `);

  return raw ? JSON.parse(raw) : null;
}

export function getPostulationStatusFor(email, offerTitle) {
  const safeEmail = escapePhpString(email);
  const safeTitle = escapePhpString(offerTitle);

  return runLaravelExpression(`
    $user = App\\Models\\User::where('email', '${safeEmail}')->first();
    $offer = App\\Models\\Offre::where('titre', '${safeTitle}')->latest('id')->first();
    if (!$user || !$offer) {
      echo '';
      return;
    }
    echo App\\Models\\Postulation::where('user_id', $user->id)
      ->where('offre_id', $offer->id)
      ->value('status') ?? '';
  `);
}
