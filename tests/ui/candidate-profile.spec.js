import { expect, test } from '@playwright/test';
import { expectHealthyResponse } from './helpers/assertions.js';
import { credentials, login } from './helpers/auth.js';

test.describe('candidate profile management', () => {
  test.beforeEach(async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'Profile management runs on desktop');
  });

  test('profile page loads with all three sections visible', async ({ page }) => {
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);

    const response = await page.goto('/profile');
    await expectHealthyResponse(response, page);

    // Section 1 : Informations du profil
    await expect(page.getByRole('heading', { name: /Informations du profil/i })).toBeVisible();
    await expect(page.getByLabel(/Nom complet/i)).toBeVisible();
    await expect(page.getByLabel(/Adresse e-mail/i)).toBeVisible();
    await expect(page.getByRole('button', { name: /Enregistrer/i })).toBeVisible();

    // Section 2 : Mot de passe
    await expect(page.getByRole('heading', { name: /Mettre a jour le mot de passe/i })).toBeVisible();
    await expect(page.locator('#current_password')).toBeVisible();
    await expect(page.locator('#new_password')).toBeVisible();
    await expect(page.locator('#password_confirmation')).toBeVisible();
    await expect(page.getByRole('button', { name: /^Mettre a jour$/i })).toBeVisible();

    // Section 3 : Supprimer le compte
    await expect(page.getByRole('heading', { name: /Supprimer le compte/i })).toBeVisible();
    await expect(page.getByRole('button', { name: /Supprimer le compte/i })).toBeVisible();
    await expect(page.locator('#delete_password')).toBeVisible();

    // Pas d'erreur 403/500
    await expect(page.locator('body')).not.toContainText(/403|500/);
  });

  test('update name field and save shows success', async ({ page }) => {
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);

    await page.goto('/profile');

    const nameInput = page.getByLabel(/Nom complet/i);
    const originalName = await nameInput.inputValue();

    // Changer le nom
    await nameInput.clear();
    await nameInput.fill('Test Candidate Updated');
    await page.getByRole('button', { name: /Enregistrer/i }).click();
    await page.waitForLoadState('domcontentloaded');

    // Revenir à la page profil et vérifier
    await page.goto('/profile');
    await expect(nameInput).toHaveValue('Test Candidate Updated');

    // Restaurer le nom original
    await nameInput.clear();
    await nameInput.fill(originalName);
    await page.getByRole('button', { name: /Enregistrer/i }).click();
  });

  test('change password with wrong current password shows error', async ({ page }) => {
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);

    await page.goto('/profile');

    // Remplir le formulaire de mot de passe avec un mauvais mot de passe actuel
    await page.locator('#current_password').fill('WrongPassword123!');
    await page.locator('#new_password').fill('NewPassword123!');
    await page.locator('#password_confirmation').fill('NewPassword123!');

    await page.getByRole('button', { name: /^Mettre a jour$/i }).click();
    await page.waitForLoadState('domcontentloaded');

    // Le message d'erreur devrait apparaître (Laravel renvoie une erreur de validation)
    await expect(page.locator('body')).toContainText(/incorrect|error|erreur|actuel/i);
  });

  test('delete account requires password field to be filled', async ({ page }) => {
    await login(page, credentials.candidate);
    await expect(page).toHaveURL(/\/user|\/dashboard/);

    await page.goto('/profile');

    // Le champ password pour la suppression doit exister et être required
    const deletePasswordInput = page.locator('#delete_password');
    await expect(deletePasswordInput).toBeVisible();
    await expect(deletePasswordInput).toHaveAttribute('required');

    // Le bouton de suppression doit être présent
    await expect(page.getByRole('button', { name: /Supprimer le compte/i })).toBeVisible();
  });
});
