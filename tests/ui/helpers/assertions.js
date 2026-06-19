import { expect } from '@playwright/test';

export async function expectHealthyResponse(response, page) {
  expect(response, `No response received for ${page.url()}`).not.toBeNull();
  expect(response.status(), `${page.url()} should not return an HTTP error`).toBeLessThan(400);
  await expect(page.locator('body')).not.toContainText(/Page Expired/i);
}
