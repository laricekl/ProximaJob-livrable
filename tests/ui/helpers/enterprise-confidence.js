import { expect } from '@playwright/test';
import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { credentials, login, logout } from './auth.js';
import { expectHealthyResponse } from './assertions.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const fixturesDir = path.resolve(__dirname, '../fixtures');
const localSqliteDatabase = path.resolve(process.cwd(), 'storage/database.sqlite');

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
    env: {
      ...process.env,
      DB_CONNECTION: process.env.DB_CONNECTION || 'sqlite',
      DB_DATABASE: process.env.DB_DATABASE || localSqliteDatabase,
      DB_HOST: process.env.DB_HOST || '',
      DB_PORT: process.env.DB_PORT || '',
      DB_USERNAME: process.env.DB_USERNAME || '',
      DB_PASSWORD: process.env.DB_PASSWORD || '',
    },
  }).trim();
}

export function uniqueScenarioId(prefix = 'qa') {
  const timestamp = Date.now();
  const random = Math.random().toString(36).slice(2, 7);
  return `${prefix}-${timestamp}-${random}`;
}

export function buildOfferData(prefix = 'QA') {
  const id = uniqueScenarioId(prefix.toLowerCase());

  return {
    id,
    title: `${prefix} Offre ${id}`,
    location: 'Montreal, QC',
    description: 'Nous recherchons une personne organisee, fiable et a l aise avec la coordination produit et les suivis d equipe.',
    responsibilities: 'Coordonner les priorites de livraison.\nAssurer le suivi avec les equipes.\nDocumenter les avancements de sprint.',
    languages: 'Francais courant, anglais professionnel',
    otherCriteria: 'Aisance de communication, autonomie et rigueur.',
    companySearch: 'Tech Solutions',
    candidateSearch: 'Test',
  };
}

export async function createOffer(page, offer) {
  let response = await page.goto('/entreprise/offres/create');
  await expectHealthyResponse(response, page);

  await page.getByLabel(/Titre du poste/i).fill(offer.title);
  await page.getByLabel(/Type de contrat/i).selectOption({ index: 1 });
  await page.getByLabel(/Secteur d'activite|Secteur d'activité/i).selectOption({ index: 1 });
  await page.getByLabel(/Lieu de travail/i).fill(offer.location);
  await page.getByLabel(/Mode de travail/i).selectOption('Hybride');
  await page.getByLabel(/Categorie|Catégorie/i).selectOption('informatique');
  await page.getByLabel(/Type de remuneration|Type de rémunération/i).selectOption('annuel');
  await page.getByLabel(/Date d'entree|Date d'entrée/i).selectOption('Sous 1 mois');
  await page.getByLabel(/Description du poste/i).fill(offer.description);
  await page.getByLabel(/Responsabilites principales|Responsabilités principales/i).fill(offer.responsibilities);
  await page.getByLabel(/Salaire minimum/i).fill('70000');
  await page.getByLabel(/Salaire maximum/i).fill('90000');
  await page.getByLabel(/Experience requise|Expérience requise/i).selectOption('2-3 ans');
  await page.getByLabel(/Date d'expiration/i).fill('2026-12-31');
  await page.getByLabel(/Langues demandees|Langues demandées/i).fill(offer.languages);
  await page.getByLabel(/Autres criteres|Autres critères/i).fill(offer.otherCriteria);

  await page.getByRole('button', { name: /Publier l'offre|Publier/i }).last().click();
  await page.waitForLoadState('domcontentloaded');

  await expect(page).toHaveURL(/\/entreprise$/);
  await expect(page.locator('body')).toContainText(/Offre creee avec succes|Offre créée avec succès/i);
  await expect(page.locator('body')).toContainText(offer.title);
}

export async function openOfferCandidatesFromDashboard(page, offerTitle) {
  const card = page.locator('.card-glow').filter({ hasText: offerTitle }).first();
  await expect(card).toBeVisible();

  const viewCandidates = card.locator('a[href*="/candidatures"]').first();
  await Promise.all([
    page.waitForURL(/\/entreprise\/offres\/\d+\/candidatures/),
    viewCandidates.click(),
  ]);

  await expect(page.locator('body')).toContainText(offerTitle);
}

export async function applyToOffer(page, offerTitle) {
  const offerSlug = getOfferSlugByTitle(offerTitle);
  expect(offerSlug, `Offer slug should exist for ${offerTitle}`).not.toBe('');

  let response = await page.goto(`/offres/${offerSlug}`);
  await expectHealthyResponse(response, page);

  await expect(page.locator('main h1').first()).toContainText(offerTitle);

  await page.getByRole('button', { name: /Postuler maintenant/i }).click();

  const modal = page.locator('#applicationModal');
  await expect(modal).toBeVisible();
  let latestDialogMessage = '';

  const dialogHandler = async (dialog) => {
    latestDialogMessage = dialog.message();
    await dialog.accept();
  };

  page.on('dialog', dialogHandler);

  await page.evaluate(() => {
    document.querySelectorAll('#applicationModal input[readonly]').forEach((input) => {
      input.removeAttribute('readonly');
    });
  });

  await modal.locator('input[name="nom"]').fill('User');
  await modal.locator('input[name="prenom"]').fill('Test');
  await modal.locator('input[name="email"]').fill(credentials.candidate.email);
  await modal.locator('input[name="telephone"]').fill('5145550000');
  await modal.locator('input[name="adresse"]').fill('123 Rue Test, Montreal');
  await page.locator('#nextBtn').click();
  await expect(page.locator('#step-2.active')).toBeVisible();

  const cvPath = path.join(fixturesDir, 'candidate-cv-demo.pdf');
  const motivationPath = path.join(fixturesDir, 'candidate-letter-demo.pdf');

  await page.locator('#cv-upload').setInputFiles(cvPath);
  await page.locator('#motivation-upload').setInputFiles(motivationPath);
  await page.locator('#nextBtn').click();
  await expect(page.locator('#step-3.active')).toBeVisible();

  await page.locator('#applicationModal #terms').check({ force: true });
  const submitResponsePromise = page.waitForResponse((networkResponse) =>
    networkResponse.url().includes('/candidature/store') && networkResponse.request().method() === 'POST'
  );
  await page.locator('#nextBtn').click();
  const submitResponse = await submitResponsePromise;

  expect(submitResponse.ok(), `Application submit should succeed. Dialog: ${latestDialogMessage}`).toBeTruthy();
  await expect
    .poll(() => getPostulationStatusForCandidate(offerTitle), {
      timeout: 10000,
    })
    .toBe('en_attente');

  page.off('dialog', dialogHandler);
}

export function getOfferSlugByTitle(title) {
  const safeTitle = title.replace(/\\/g, '\\\\').replace(/'/g, "\\'");

  return runLaravelExpression(
    `echo App\\Models\\Offre::where('titre', '${safeTitle}')->latest('id')->value('slug') ?? '';`,
  );
}

export function seedManualApplicationForCandidate(offerTitle) {
  const safeTitle = offerTitle.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
  const safeEmail = credentials.candidate.email.replace(/\\/g, '\\\\').replace(/'/g, "\\'");

  return runLaravelExpression(`
    $user = App\\Models\\User::where('email', '${safeEmail}')->first();
    $offer = App\\Models\\Offre::where('titre', '${safeTitle}')->latest('id')->first();
    if (!$user || !$offer) { echo 'missing'; return; }
    $postulation = App\\Models\\Postulation::updateOrCreate(
      ['user_id' => $user->id, 'offre_id' => $offer->id],
      [
        'cv' => 'assets/cvs/demo/candidate-cv-demo.pdf',
        'lettre_motivation' => 'assets/cvs/demo/candidate-letter-demo.pdf',
        'status' => 'en_attente',
        'autopostulation' => false,
      ]
    );
    $entrepriseUser = $offer->entreprise?->user;
    if ($entrepriseUser) {
      App\\Models\\Notification::updateOrCreate(
        [
          'user_id' => $entrepriseUser->id,
          'title' => 'Nouvelle candidature reçue',
          'link' => '/entreprise/offres/' . $offer->id . '/candidatures',
        ],
        [
          'role' => 'entreprise',
          'message' => 'Un candidat a postulé à votre offre "' . $offer->titre . '"',
          'is_read' => false,
        ]
      );
    }
    echo $postulation->status;
  `);
}

function getPostulationStatusForCandidate(offerTitle) {
  const safeTitle = offerTitle.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
  const safeEmail = credentials.candidate.email.replace(/\\/g, '\\\\').replace(/'/g, "\\'");

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

export async function assertCandidateHistoryContains(page, offerTitle, expectedStatusPattern) {
  const response = await page.goto('/user/historique-candidatures');
  await expectHealthyResponse(response, page);

  const row = page.locator('tbody tr').filter({ hasText: offerTitle }).first();
  await expect(row).toBeVisible();
  await expect(row).toContainText(expectedStatusPattern);
}

export async function assertCandidateNotificationContains(page, offerTitle, expectedStatusSnippet) {
  const response = await page.goto('/notifications');
  await expectHealthyResponse(response, page);

  const notification = page.locator('.notif-item').filter({ hasText: offerTitle }).first();
  await expect(notification).toBeVisible();
  await expect(notification).toContainText(expectedStatusSnippet);
}

export async function loginAsEnterprise(page) {
  await login(page, credentials.enterprise);
  await expect(page).toHaveURL(/\/entreprise/);
}

export async function loginAsCandidate(page) {
  await login(page, credentials.candidate);
  await expect(page).toHaveURL(/\/user|\/dashboard/);
}

export async function switchFromEnterpriseToCandidate(page) {
  await logout(page);
  await loginAsCandidate(page);
}

export async function switchFromCandidateToEnterprise(page) {
  await logout(page);
  await loginAsEnterprise(page);
}
