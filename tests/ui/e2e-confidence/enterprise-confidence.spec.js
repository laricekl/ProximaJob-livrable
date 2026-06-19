import { expect, test } from '@playwright/test';
import {
  assertCandidateHistoryContains,
  assertCandidateNotificationContains,
  buildOfferData,
  createOffer,
  loginAsEnterprise,
  openOfferCandidatesFromDashboard,
  seedManualApplicationForCandidate,
  switchFromCandidateToEnterprise,
  switchFromEnterpriseToCandidate,
} from '../helpers/enterprise-confidence.js';

test.describe('e2e confidence enterprise', () => {
  test('confiance prioritaire: creation offre -> candidature -> reponse entreprise -> notification candidat', async ({ page }) => {
    test.skip((page.viewportSize()?.width || 0) < 768, 'Confidence enterprise flow runs on desktop');
    test.setTimeout(180_000);

    const offer = buildOfferData('CONF');

    await loginAsEnterprise(page);
    await createOffer(page, offer);

    await switchFromEnterpriseToCandidate(page);
    await expect(seedManualApplicationForCandidate(offer.title)).toBe('en_attente');
    await assertCandidateHistoryContains(page, offer.title, /En attente/i);

    await switchFromCandidateToEnterprise(page);
    await openOfferCandidatesFromDashboard(page, offer.title);

    const candidateRow = page.locator('.card-glow').filter({ hasText: /Test User|Test/i }).first();
    await expect(candidateRow).toBeVisible();
    await candidateRow.getByRole('button', { name: /Accepter/i }).click();
    await page.waitForLoadState('domcontentloaded');

    await expect(candidateRow).toContainText(/Acceptee|Acceptée/i);

    await switchFromEnterpriseToCandidate(page);
    await assertCandidateHistoryContains(page, offer.title, /Acceptee|Acceptée/i);
    await assertCandidateNotificationContains(page, offer.title, /acceptee|acceptée/i);
  });
});
