import { expect } from '@playwright/test';

export const credentials = {
  candidate: {
    email: process.env.UI_CANDIDATE_EMAIL || 'test@example.com',
    password: process.env.UI_CANDIDATE_PASSWORD || 'password',
  },
  enterprise: {
    email: process.env.UI_ENTERPRISE_EMAIL || 'contact@techsolutions.com',
    password: process.env.UI_ENTERPRISE_PASSWORD || 'password',
  },
};

export async function logout(page) {
  await page.goto('/');
  const logoutButton = page.getByRole('button', { name: /deconnexion|déconnexion/i });
  if (await logoutButton.count()) {
    await logoutButton.first().click();
    await expect(page).toHaveURL(/\/$/);
  } else {
    await page.context().clearCookies();
  }
}

export async function login(page, account) {
  await page.context().clearCookies();
  await page.goto('/login');

  await page.getByLabel(/adresse e-mail/i).fill(account.email);
  await page.getByLabel(/mot de passe/i).fill(account.password);
  await page.getByRole('button', { name: /se connecter/i }).click();
  await page.waitForLoadState('domcontentloaded');
}
