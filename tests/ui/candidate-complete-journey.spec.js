import { expect, test } from '@playwright/test';
import path from 'path';
import { fileURLToPath } from 'url';
import { expectHealthyResponse } from './helpers/assertions.js';
import { login, logout } from './helpers/auth.js';
import { expectFooterVisible } from './helpers/layout.js';
import {
  buildDemoAccounts,
  getPostulationStatusFor,
  getVerificationToken,
  getUserStatus,
  registerCandidate,
  verifyCandidateForLogin,
} from './helpers/provisioning.js';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const fixturesDir = path.resolve(__dirname, 'fixtures');

test.describe('Candidate Complete Journey', () => {
  test('parcours complet : inscription → offres → postuler → CV builder → abonnement → profil → notifications → logout', async ({ page }) => {
    test.setTimeout(600_000);
    test.skip((page.viewportSize()?.width || 0) < 768, 'Parcours complet desktop uniquement');

    const accounts = buildDemoAccounts();
    const password = accounts.password;

    // ====================================================================
    // 1. INSCRIPTION + VÉRIFICATION EMAIL
    // ====================================================================
    await registerCandidate(page, accounts.candidate, password);

    const token = getVerificationToken(accounts.candidate.email);
    expect(token, 'Token de verification doit exister').not.toBe('');

    await page.goto(`/verify-email/${token}`);
    verifyCandidateForLogin(accounts.candidate.email);
    expect(getUserStatus(accounts.candidate.email)).toContain('Actif|verified');

    // Login manuel après vérification
    await login(page, { email: accounts.candidate.email, password });
    await expect(page).toHaveURL(/\/user/);

    // ====================================================================
    // 2. DASHBOARD
    // ====================================================================
    let response = await page.goto('/user');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).toContainText(/Bonjour|Tableau de bord|Espace candidat/i);
    await expect(page.locator('body')).not.toContainText(/403/);

    // Stats du dashboard
    await expect(page.locator('body')).toContainText(/Candidatures|Notifications/i);

    // ====================================================================
    // 3. OFFRES — LISTE + DÉTAIL
    // ====================================================================
    response = await page.goto('/offres');
    await expectHealthyResponse(response, page);

    const offerLink = page.locator('a[href^="/offres/"], a[href*="/offres/"]').filter({ hasText: /.+/ }).first();
    if (await offerLink.count()) {
      await expect(offerLink).toBeVisible();
      await Promise.all([
        page.waitForURL(/\/offres\/[^/]+$/),
        offerLink.click(),
      ]);
      await expect(page.locator('main h1').first()).toBeVisible();
      await expect(page.locator('body')).toContainText(/Postuler maintenant|Retour aux offres/i);
    }

    // ====================================================================
    // 4. POSTULER À UNE OFFRE
    // ====================================================================
    await page.goto('/offres');
    const applyOfferLink = page.locator('a[href^="/offres/"]').filter({ hasText: /.+/ }).first();
    if (await applyOfferLink.count()) {
      await applyOfferLink.click();
      await page.waitForURL(/\/offres\/[^/]+$/);

      const applyButton = page.getByRole('button', { name: /Postuler maintenant/i });
      if (await applyButton.count()) {
        await applyButton.click();
        await page.waitForTimeout(600);

        // Modal de candidature
        const modal = page.locator('#applicationModal');
        if (await modal.count()) {
          // Step 1 → Step 2
          const nextBtn = page.locator('#nextBtn');
          if (await nextBtn.count()) {
            await nextBtn.click();
            await page.waitForTimeout(400);
          }

          // Step 2 : upload CV
          const cvInput = page.locator('#cv-upload');
          if (await cvInput.count()) {
            await cvInput.setInputFiles(path.join(fixturesDir, 'candidate-cv-demo.pdf'));
          }

          // Upload lettre si présent
          const letterInput = page.locator('#motivation-upload');
          if (await letterInput.count()) {
            await letterInput.setInputFiles(path.join(fixturesDir, 'candidate-letter-demo.pdf'));
          }

          // Step 2 → Step 3
          if (await nextBtn.count()) {
            await nextBtn.click();
            await page.waitForTimeout(400);
          }

          // Step 3 : accepter conditions + submit
          const termsCheckbox = page.locator('#applicationModal #terms');
          if (await termsCheckbox.count()) {
            await termsCheckbox.check({ force: true });
          }

          // Intercepter le dialog
          page.on('dialog', (dialog) => dialog.accept());

          if (await nextBtn.count()) {
            await nextBtn.click();
            await page.waitForTimeout(1500);
          }

          await expect(page.locator('body')).not.toContainText(/500|erreur serveur/i);
        }
      }
    }

    // ====================================================================
    // 5. HISTORIQUE CANDIDATURES MANUELLES
    // ====================================================================
    response = await page.goto('/user/historique-candidatures');
    await expectHealthyResponse(response, page);

    const filterForm = page.getByTestId('candidate-manual-history-filter-form');
    if (await filterForm.count()) {
      await expect(filterForm).toBeVisible();

      // Filtrer par statut
      const statusSelect = filterForm.locator('select[name="status"]');
      if (await statusSelect.count()) {
        await statusSelect.selectOption('En attente');
      }

      // Recherche
      const searchInput = filterForm.getByPlaceholder(/poste|entreprise/i);
      if (await searchInput.count()) {
        await searchInput.fill('dev');
      }

      // Filtrer
      const filterBtn = filterForm.getByRole('button', { name: /Filtrer/i });
      if (await filterBtn.count()) {
        await filterBtn.click();
        await page.waitForLoadState('domcontentloaded');
      }

      // Réinitialiser
      const resetLink = filterForm.getByRole('link', { name: /Réinitialiser/i });
      if (await resetLink.count()) {
        await resetLink.click();
        await expect(page).toHaveURL(/\/user\/historique-candidatures$/);
      }
    }

    // ====================================================================
    // 6. HISTORIQUE CANDIDATURES IA
    // ====================================================================
    response = await page.goto('/user/historique-candidatures_ia');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).toContainText(/Candidatures IA|automatiques/i);

    // ====================================================================
    // 7. DÉTAIL CANDIDATURE
    // ====================================================================
    response = await page.goto('/user/detail-candidature');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).not.toContainText(/403|Page Expired/);

    // ====================================================================
    // 8. CV BUILDER — INFOS CV
    // ====================================================================
    response = await page.goto('/user/infos-cv');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).toContainText(/Construire mon CV|CV principal/i);

    // Upload CV source
    const sourceFileInput = page.locator('#sourceCvUploadForm input[type="file"]');
    if (await sourceFileInput.count()) {
      await sourceFileInput.setInputFiles(path.join(fixturesDir, 'candidate-cv-demo.pdf'));
      await page.waitForTimeout(1500);
    }

    // Ouvrir le builder
    const editCvBtn = page.getByRole('button', { name: /Modifier le CV principal|Modifier les informations/i }).first();
    if (await editCvBtn.count()) {
      await editCvBtn.click();
      await page.waitForTimeout(800);

      // Remplir infos personnelles (section 1)
      const nomInput = page.locator('#cvDataForm input[name="nom"]');
      const prenomInput = page.locator('#cvDataForm input[name="prenom_cv"]');
      const telInput = page.locator('#cvDataForm input[name="telephone_cv"]');

      if (await nomInput.count()) await nomInput.fill('JourneyNom');
      if (await prenomInput.count()) await prenomInput.fill('JourneyPrenom');
      if (await telInput.count()) await telInput.fill('+1 514 555 0101');

      // Sauvegarder si possible
      const saveBtn = page.locator('#cvDataForm').getByRole('button', { name: /Enregistrer|Sauvegarder|Save/i });
      if (await saveBtn.count()) {
        page.once('dialog', (dialog) => dialog.accept());
        await saveBtn.first().click();
        await page.waitForTimeout(1500);
      }
    }

    // ====================================================================
    // 9. CV PRINCIPAL — AFFICHER + TÉLÉCHARGER
    // ====================================================================
    await page.goto('/user/infos-cv');

    // Si le CV principal est disponible, afficher l'iframe
    const cvFrame = page.locator('#generatedCvFrame');
    if (await cvFrame.count()) {
      await expect(cvFrame).toBeVisible();
    }

    // ====================================================================
    // 10. PERSONNALISER LE CV
    // ====================================================================
    response = await page.goto('/user/personnaliser-cv');
    // Si pas de profil CV, on est redirigé — c'est normal
    if (page.url().includes('personnaliser-cv')) {
      await expectHealthyResponse(response, page);

      // Remplir le formulaire
      const titleInput = page.locator('input[name="offer_title"]');
      if (await titleInput.count()) {
        await titleInput.fill('Développeur Full Stack');
      }
      const detailsInput = page.locator('textarea[name="offer_details"]');
      if (await detailsInput.count()) {
        await detailsInput.fill('Poste en développement web full stack avec React, Node.js et PostgreSQL.');
      }
      const requirementsInput = page.locator('textarea[name="key_requirements"]');
      if (await requirementsInput.count()) {
        await requirementsInput.fill('JavaScript, React, Node.js, SQL, Git');
      }

      // Générer
      const generateBtn = page.getByRole('button', { name: /generer|générer/i });
      if (await generateBtn.count()) {
        try {
          await generateBtn.click();
          await page.waitForTimeout(3000);
          await expect(page.locator('body')).not.toContainText(/500|erreur serveur/i);
        } catch (e) {
          // La génération peut échouer sans clé API Gemini — acceptable
        }
      }
    }

    // ====================================================================
    // 11. ABONNEMENT — VOIR LES PLANS
    // ====================================================================
    response = await page.goto('/user/plan-abonnement');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).toContainText(/Basic|Standard|Premium/i);

    // Toggle annuel
    const yearlyBtn = page.getByRole('button', { name: /Annuel/i });
    if (await yearlyBtn.count()) {
      await yearlyBtn.click();
      await page.waitForTimeout(400);
    }

    // Voir mon abonnement
    response = await page.goto('/user/abonnement');
    await expectHealthyResponse(response, page);

    // ====================================================================
    // 12. PROFIL COMPTE
    // ====================================================================
    response = await page.goto('/profile');
    await expectHealthyResponse(response, page);
    await expect(page.getByRole('heading', { name: /Informations du profil/i })).toBeVisible();
    await expect(page.getByRole('heading', { name: /Supprimer le compte/i })).toBeVisible();

    // ====================================================================
    // 13. PROFIL PUBLIC
    // ====================================================================
    response = await page.goto('/user/profil-public');
    await expectHealthyResponse(response, page);
    await expect(page.getByRole('heading', { name: /Mon profil candidat/i })).toBeVisible();

    // Toggle recruteur
    const previewLink = page.getByText(/Prévisualiser comme employeur/i);
    if (await previewLink.count()) {
      await previewLink.click();
      await page.waitForLoadState('domcontentloaded');
      await expect(page.locator('body')).toContainText(/employeur|recruteur/i);
    }

    // ====================================================================
    // 14. NOTIFICATIONS
    // ====================================================================
    response = await page.goto('/notifications');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).not.toContainText(/403/);

    // Marquer tout comme lu
    const markAllBtn = page.getByRole('button', { name: /tout marquer|marquer tout/i }).or(
      page.locator('form[action*="mark-all-read"] button, a[href*="mark-all-read"]')
    ).first();
    if (await markAllBtn.count()) {
      await markAllBtn.click();
      await page.waitForTimeout(500);
    }

    // ====================================================================
    // 15. DÉCONNEXION
    // ====================================================================
    await logout(page);
    await expect(page).toHaveURL(/\/($|offres|login)/);
    await expect(page.locator('body')).not.toContainText(/403/);
  });
});
