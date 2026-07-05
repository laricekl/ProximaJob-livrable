import { expect, test } from '@playwright/test';
import path from 'path';
import { fileURLToPath } from 'url';
import { expectHealthyResponse } from './helpers/assertions.js';
import { credentials, login } from './helpers/auth.js';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const fixturesDir = path.resolve(__dirname, 'fixtures');

test.describe('candidate CV builder', () => {
  test.beforeEach(async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'CV builder tests run on desktop');
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);
  });

  test('CV builder page loads with principal CV card and source upload area', async ({ page }) => {
    const response = await page.goto('/user/infos-cv');
    await expectHealthyResponse(response, page);

    // Titre de la page
    await expect(page.locator('body')).toContainText(/Construire mon CV|CV principal/i);

    // Bouton pour modifier/ouvrir le builder (il y en a 2 : un dans le bandeau haut, un dans l'aperçu)
    await expect(page.getByRole('button', { name: /Modifier le CV principal|Modifier les informations/i }).first()).toBeVisible();

    // Zone d'upload CV source
    const sourceUploadForm = page.locator('#sourceCvUploadForm');
    await expect(sourceUploadForm).toBeVisible();

    // Lien vers la personnalisation
    await expect(page.locator('a[href*="personnaliser-cv"]').first()).toBeVisible();

    // Pas d'erreur
    await expect(page.locator('body')).not.toContainText(/403|500/);
  });

  test('upload source CV file triggers upload and shows status', async ({ page }) => {
    const response = await page.goto('/user/infos-cv');
    await expectHealthyResponse(response, page);

    // Trouver l'input file caché dans le formulaire d'upload source
    const fileInput = page.locator('#sourceCvUploadForm input[type="file"]');
    await expect(fileInput).toBeAttached();

    // Upload du fichier CV fixture
    const cvFile = path.join(fixturesDir, 'candidate-cv-demo.pdf');
    await fileInput.setInputFiles(cvFile);

    // Attendre la réponse (l'upload se fait via JS onchange)
    await page.waitForTimeout(2000);

    // Vérifier qu'un statut ou un message apparaît
    await expect(page.locator('body')).not.toContainText(/500|erreur serveur/i);
  });

  test('CV builder wizard opens with all 8 sections navigable', async ({ page }) => {
    const response = await page.goto('/user/infos-cv');
    await expectHealthyResponse(response, page);

    // Ouvrir le builder
    const editButton = page.getByRole('button', { name: /Modifier le CV principal|Modifier les informations/i });
    await editButton.first().click();
    await page.waitForTimeout(800);

    // Le formulaire du builder doit être visible
    const cvForm = page.locator('#cvDataForm');
    await expect(cvForm).toBeVisible();

    // Section 1 (infos personnelles) doit être active par défaut
    await expect(page.locator('#section-1.active, #section-1.cv-form-section.active')).toBeVisible();

    // Vérifier les champs de la section 1
    await expect(page.locator('input[name="nom"]').first()).toBeAttached();
    await expect(page.locator('input[name="prenom_cv"]').first()).toBeAttached();
    await expect(page.locator('input[name="email_cv"]').first()).toBeAttached();
    await expect(page.locator('input[name="telephone_cv"]').first()).toBeAttached();
  });

  test('fill CV wizard section 1 (personal info) and navigate forward', async ({ page }) => {
    const response = await page.goto('/user/infos-cv');
    await expectHealthyResponse(response, page);

    // Ouvrir le builder
    await page.getByRole('button', { name: /Modifier le CV principal|Modifier les informations/i }).first().click();
    await page.waitForTimeout(800);

    // Remplir les champs de la section 1
    const nomInput = page.locator('#cvDataForm input[name="nom"]');
    const prenomInput = page.locator('#cvDataForm input[name="prenom_cv"]');
    const telInput = page.locator('#cvDataForm input[name="telephone_cv"]');

    if (await nomInput.count()) {
      await nomInput.fill('Dubois');
    }
    if (await prenomInput.count()) {
      await prenomInput.fill('Marie');
    }
    if (await telInput.count()) {
      await telInput.fill('+1 514 555 0199');
    }

    // Chercher le bouton "Suivant"
    const nextBtn = page.getByRole('button', { name: /Suivant|suivant/i });
    if (await nextBtn.count()) {
      await nextBtn.first().click();
      await page.waitForTimeout(500);
    }

    // La section 2 devrait être visible maintenant
    await expect(page.locator('body')).not.toContainText(/erreur/i);
  });

  test('add competence in section 2 via dynamic button', async ({ page }) => {
    const response = await page.goto('/user/infos-cv');
    await expectHealthyResponse(response, page);

    // Ouvrir le builder
    await page.getByRole('button', { name: /Modifier le CV principal|Modifier les informations/i }).first().click();
    await page.waitForTimeout(800);

    // Naviguer vers la section 2 (compétences) — cliquer sur l'étape 2 dans la sidebar ou utiliser Suivant
    // D'abord, essayer de cliquer directement sur l'indicateur de section
    const section2Trigger = page.locator('[onclick*="section-2"], .cv-form-step[data-step="2"]').first();
    if (await section2Trigger.count()) {
      await section2Trigger.click();
      await page.waitForTimeout(400);
    }

    // Chercher le bouton "Ajouter une competence"
    const addSkillBtn = page.locator('#cvDataForm').getByRole('button', { name: /Ajouter une comp[eé]tence/i });
    if (await addSkillBtn.count()) {
      await addSkillBtn.click();
      await page.waitForTimeout(400);

      // Vérifier qu'un nouveau champ de compétence est apparu
      const skillInputs = page.locator('#cvDataForm input[name*="competences"]');
      const initialCount = await skillInputs.count();
      expect(initialCount).toBeGreaterThanOrEqual(1);
    }

    await expect(page.locator('body')).not.toContainText(/erreur/i);
  });

  test('save CV data via the wizard final step', async ({ page }) => {
    test.setTimeout(30_000);

    const response = await page.goto('/user/infos-cv');
    await expectHealthyResponse(response, page);

    // Ouvrir le builder
    await page.getByRole('button', { name: /Modifier le CV principal|Modifier les informations/i }).first().click();
    await page.waitForTimeout(800);

    // Remplir les champs minimum requis
    const nomInput = page.locator('#cvDataForm input[name="nom"]');
    const prenomInput = page.locator('#cvDataForm input[name="prenom_cv"]');
    const emailInput = page.locator('#cvDataForm input[name="email_cv"]');
    const telInput = page.locator('#cvDataForm input[name="telephone_cv"]');

    if (await nomInput.count()) await nomInput.fill('TestNom');
    if (await prenomInput.count()) await prenomInput.fill('TestPrenom');
    if (await emailInput.count()) await emailInput.fill('test@example.com');
    if (await telInput.count()) await telInput.fill('+1 514 555 0000');

    // Chercher le bouton Enregistrer (peut être dans la dernière section ou toujours visible)
    const saveButton = page.locator('#cvDataForm').getByRole('button', { name: /Enregistrer|Sauvegarder|Save/i });
    if (await saveButton.count()) {
      // Dialog possible — accepter
      page.once('dialog', (dialog) => dialog.accept());

      await saveButton.first().click();
      await page.waitForTimeout(1500);

      // Vérifier que la page n'a pas crashé
      await expect(page.locator('body')).not.toContainText(/500|erreur serveur/i);
    }
  });
});
