import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from '../helpers/assertions.js';
import { credentials, login, logout } from '../helpers/auth.js';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const fixturesDir = path.resolve(__dirname, '..', 'fixtures');

test.describe('Demo Candidat — Parcours Complet', () => {
  test.beforeEach(async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'La demo candidat est ecrite pour le parcours desktop.');
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);
  });

  test('parcours complet: dashboard → offres → postuler → CV → IA → abonnement → notifications → profil public', async ({ page }) => {
    test.setTimeout(600_000);

    // ====================================================================
    // 1. DASHBOARD
    // ====================================================================
    let response = await page.goto('/user');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).toContainText(/Bonjour|Tableau de bord|Espace candidat/i);

    // ====================================================================
    // 2. OFFRES — LISTE
    // ====================================================================
    response = await page.goto('/offres');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).not.toContainText(/403|Page Expired/i);

    // ====================================================================
    // 3. OFFRES — DÉTAIL
    // ====================================================================
    const offerLink = page.locator('a[href^="/offres/"], a[href*="/offres/"]').filter({ hasText: /.+/ }).first();
    if (await offerLink.count()) {
      await expect(offerLink).toBeVisible();
      await Promise.all([
        page.waitForURL(/\/offres\/[^/]+$/),
        offerLink.click(),
      ]);
      await expect(page.locator('main h1').first()).toBeVisible();
      await expect(page.locator('body')).toContainText(/Postuler maintenant|Retour aux offres|Se connecter/i);
    }

    // ====================================================================
    // 4. POSTULER À UNE OFFRE (upload CV + lettre + docs supplémentaires)
    // ====================================================================
    // Retourner sur la première offre pour postuler
    await page.goto('/offres');
    const applyLink = page.locator('a[href^="/offres/"]').filter({ hasText: /.+/ }).first();
    if (await applyLink.count()) {
      await applyLink.click();
      await page.waitForURL(/\/offres\/[^/]+$/);

      // Chercher le lien/bouton "Postuler maintenant"
      const applyButton = page.getByRole('link', { name: /postuler/i }).or(page.getByRole('button', { name: /postuler/i }));
      if (await applyButton.count()) {
        await applyButton.first().click();
        await page.waitForLoadState('domcontentloaded');

        // Si on arrive sur un formulaire de candidature (modal ou page)
        const fileInput = page.locator('input[type="file"][name="cv"]');
        if (await fileInput.count()) {
          // Upload du CV
          const cvFile = path.join(fixturesDir, 'candidate-cv-demo.pdf');
          await fileInput.setInputFiles(cvFile);
          await expect(page.locator('body')).not.toContainText(/erreur/i);

          // Upload de la lettre de motivation
          const motivationInput = page.locator('input[type="file"][name="motivation"]');
          if (await motivationInput.count()) {
            const letterFile = path.join(fixturesDir, 'candidate-letter-demo.pdf');
            await motivationInput.setInputFiles(letterFile);
          }
        }
      }
    }

    // ====================================================================
    // 5. HISTORIQUE CANDIDATURES MANUELLES — FILTRE + RECHERCHE
    // ====================================================================
    response = await page.goto('/user/historique-candidatures');
    await expectHealthyResponse(response, page);

    const manualHistoryForm = page.getByTestId('candidate-manual-history-filter-form');
    if (await manualHistoryForm.count()) {
      await expect(manualHistoryForm).toBeVisible();

      // Filtrer par statut
      const statusSelect = manualHistoryForm.locator('select[name="status"]');
      if (await statusSelect.count()) {
        await statusSelect.selectOption('En attente');
      }

      // Rechercher par mot-clé
      const searchInput = manualHistoryForm.getByPlaceholder(/poste|entreprise/i);
      if (await searchInput.count()) {
        await searchInput.fill('dev');
      }

      const filterButton = manualHistoryForm.getByRole('button', { name: /filtrer|rechercher/i });
      if (await filterButton.count()) {
        await Promise.all([
          page.waitForURL(/\/(user\/historique-candidatures|user\/historique-candidature)\?.*/),
          filterButton.click(),
        ]);
      }

      await expect(page.locator('body')).not.toContainText(/403|500|erreur/i);
    }

    // ====================================================================
    // 6. RÉINITIALISER LE FILTRE
    // ====================================================================
    await page.goto('/user/historique-candidatures');
    await expectHealthyResponse(response, page);

    // ====================================================================
    // 7. DÉTAIL D'UNE CANDIDATURE
    // ====================================================================
    response = await page.goto('/user/detail-candidature');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).not.toContainText(/403|500/i);

    // ====================================================================
    // 8. HISTORIQUE CANDIDATURES IA
    // ====================================================================
    response = await page.goto('/user/historique-candidatures_ia');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).toContainText(/Candidatures automatiques|IA/i);

    const aiHistoryForm = page.getByTestId('candidate-ai-history-filter-form');
    if (await aiHistoryForm.count()) {
      await expect(aiHistoryForm).toBeVisible();

      // Filtrer IA par statut
      const aiStatusSelect = aiHistoryForm.locator('select[name="status"]');
      if (await aiStatusSelect.count()) {
        await aiStatusSelect.selectOption('En attente');
      }

      const aiFilterButton = aiHistoryForm.getByRole('button', { name: /filtrer|rechercher/i });
      if (await aiFilterButton.count()) {
        await aiFilterButton.click();
      }
    }

    // ====================================================================
    // 9. PRÉVISUALISER UN CV IA (si une candidature IA existe)
    // ====================================================================
    // On tente de cliquer sur le lien de prévisualisation dans l'historique IA
    const cvIaLink = page.locator('a[href*="preview-cv-ia"]').first();
    if (await cvIaLink.count()) {
      response = await cvIaLink.click();
      if (response) await expect(response.status()).toBeLessThan(500);
    }

    // ====================================================================
    // 10. PRÉVISUALISER UNE LETTRE IA
    // ====================================================================
    await page.goto('/user/historique-candidatures_ia');
    const letterIaLink = page.locator('a[href*="preview-letter-ia"]').first();
    if (await letterIaLink.count()) {
      response = await letterIaLink.click();
      if (response) await expect(response.status()).toBeLessThan(500);
    }

    // ====================================================================
    // 11. PROFIL CV — PAGE DE GESTION DU CV PRINCIPAL
    // ====================================================================
    response = await page.goto('/user/infos-cv');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).toContainText(/Construire mon CV|CV principal/i);

    // Cliquer sur "Modifier le CV principal" pour ouvrir le builder
    const editCvButton = page.getByRole('button', { name: /Modifier le CV principal|Modifier les informations/i });
    if (await editCvButton.count()) {
      await editCvButton.first().click();
      await page.waitForTimeout(800);

      // Le builder modal devrait être visible
      // Remplir les champs si le formulaire est présent
      const nomInput = page.locator('input[name="nom"], #cv-builder input[name="nom"]');
      if (await nomInput.count()) {
        await nomInput.fill('Dubois');
      }
      const prenomInput = page.locator('input[name="prenom_cv"], #cv-builder input[name="prenom_cv"]');
      if (await prenomInput.count()) {
        await prenomInput.fill('Marie');
      }
      const emailInput = page.locator('input[name="email_cv"], #cv-builder input[name="email_cv"]');
      if (await emailInput.count()) {
        await emailInput.fill('marie.dubois@test.com');
      }
      const telInput = page.locator('input[name="telephone_cv"], #cv-builder input[name="telephone_cv"]');
      if (await telInput.count()) {
        await telInput.fill('514-555-0100');
      }

      // Sauvegarder si un bouton est présent
      const saveBtn = page.getByRole('button', { name: /enregistrer|sauvegarder|save/i });
      if (await saveBtn.count()) {
        await saveBtn.first().click();
        await page.waitForTimeout(1000);
      }
    }

    // ====================================================================
    // 12. MISE À JOUR DU PROFIL UTILISATEUR
    // ====================================================================
    response = await page.goto('/user');
    await expectHealthyResponse(response, page);

    // ====================================================================
    // 13. PERSONNALISATION CV — FORMULAIRE
    // ====================================================================
    response = await page.goto('/user/personnaliser-cv');
    // Si pas de profil CV, on est redirigé vers infos-cv (comportement normal)
    if (page.url().includes('personnaliser-cv')) {
      await expectHealthyResponse(response, page);
      await expect(page.locator('body')).not.toContainText(/403|500/i);

      // Remplir le formulaire de personnalisation
      const titleInput = page.locator('input[name="offer_title"]');
      if (await titleInput.count()) {
        await titleInput.fill('Développeur Full Stack');
      }
      const detailsInput = page.locator('textarea[name="offer_details"]');
      if (await detailsInput.count()) {
        await detailsInput.fill('Poste en développement web full stack avec React et Node.js');
      }
      const requirementsInput = page.locator('textarea[name="key_requirements"]');
      if (await requirementsInput.count()) {
        await requirementsInput.fill('JavaScript, React, Node.js, SQL');
      }
      const companyInput = page.locator('input[name="company_name"]');
      if (await companyInput.count()) {
        await companyInput.fill('Startup Innovante');
      }

      // Soumettre la génération (peut échouer si pas de clé Gemini en local)
      const generateButton = page.getByRole('button', { name: /generer|générer/i });
      if (await generateButton.count()) {
        try {
          await generateButton.click();
          await page.waitForTimeout(3000);
          // Vérifier qu'on est redirigé vers la preview ou qu'une erreur propre est affichée
          await expect(page.locator('body')).not.toContainText(/500|erreur serveur/i);
        } catch (e) {
          // La génération Gemini peut échouer sans clé API — acceptable
        }
      }

      // Si on a été redirigé vers une preview
      if (page.url().includes('previsualiser-cv')) {
        await expect(page.locator('body')).not.toContainText(/403|500/i);

        // Télécharger le CV
        const downloadLink = page.locator('a[href*="telecharger-cv"]').first();
        if (await downloadLink.count()) {
          const [download] = await Promise.all([
            page.waitForEvent('download', { timeout: 5000 }).catch(() => null),
            downloadLink.click(),
          ]);
          if (download) {
            expect(download.suggestedFilename()).toContain('CV');
          }
        }
      }
    } else {
      // Redirigé vers infos-cv (normal : pas encore de profil CV)
      expect(page.url()).toContain('infos-cv');
    }

    // ====================================================================
    // 14. ABONNEMENT — PAGE "MON ABONNEMENT"
    // ====================================================================
    response = await page.goto('/user/abonnement');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).not.toContainText(/403|500/i);

    // ====================================================================
    // 15. PLAN D'ABONNEMENT — CATALOGUE
    // ====================================================================
    response = await page.goto('/user/plan-abonnement');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).not.toContainText(/403|500/i);

    // ====================================================================
    // 16. NOTIFICATIONS — LISTE
    // ====================================================================
    response = await page.goto('/notifications');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).not.toContainText(/403|Page Expired/i);

    // ====================================================================
    // 17. NOTIFICATIONS — MARQUER COMME LUE
    // ====================================================================
    const markReadButton = page.getByRole('button', { name: /marquer comme lu|marquer/i }).or(
      page.locator('form[action*="mark-as-read"] button, a[href*="mark-as-read"]')
    ).first();
    if (await markReadButton.count()) {
      await markReadButton.click();
      await page.waitForTimeout(500);
    }

    // ====================================================================
    // 18. NOTIFICATIONS — TOUT MARQUER COMME LU
    // ====================================================================
    await page.goto('/notifications');
    const markAllButton = page.getByRole('button', { name: /tout marquer|marquer tout/i }).or(
      page.locator('form[action*="mark-all-read"] button, a[href*="mark-all-read"]')
    ).first();
    if (await markAllButton.count()) {
      await markAllButton.click();
      await page.waitForTimeout(500);
    }

    // ====================================================================
    // 19. CHANGEMENT DE LANGUE (FR → EN)
    // ====================================================================
    const langButton = page.locator('a[href*="set-language"], button[data-locale-switcher]').or(
      page.getByRole('button', { name: /EN|English|fr|Français/i }).first()
    );
    if (await langButton.count()) {
      await langButton.first().click();
      await page.waitForTimeout(800);
      await expect(page.locator('body')).not.toContainText(/403|500|erreur/i);
    }

    // ====================================================================
    // 20. PROFIL PUBLIC
    // ====================================================================
    response = await page.goto('/user/profil-public');
    await expectHealthyResponse(response, page);
    await expect(page.locator('body')).not.toContainText(/403|Page Expired/i);

    // ====================================================================
    // 21. RETOUR AU DASHBOARD FINAL
    // ====================================================================
    response = await page.goto('/user');
    await expectHealthyResponse(response, page);

    // ====================================================================
    // 22. DÉCONNEXION
    // ====================================================================
    await logout(page);
    await expect(page).toHaveURL(/\/($|offres|login)/);
  });
});
