import { expect } from '@playwright/test';

export async function expectNoHorizontalOverflow(page) {
  const overflow = await page.evaluate(() => {
    const documentWidth = document.documentElement.scrollWidth;
    const viewportWidth = document.documentElement.clientWidth;

    return {
      documentWidth,
      viewportWidth,
      overflow: documentWidth - viewportWidth,
    };
  });

  expect(
    overflow.overflow,
    `Page should not overflow horizontally. document=${overflow.documentWidth}, viewport=${overflow.viewportWidth}`,
  ).toBeLessThanOrEqual(2);
}

export async function expectRevealContentVisible(page) {
  const hiddenRevealCount = await page.evaluate(() => {
    return Array.from(document.querySelectorAll('.reveal')).filter((element) => {
      const style = window.getComputedStyle(element);
      return Number(style.opacity) < 0.95 || style.visibility === 'hidden' || style.display === 'none';
    }).length;
  });

  expect(hiddenRevealCount, 'Reveal sections should not remain visually hidden').toBe(0);
}

export async function expectTextVisibleAfterScroll(page, text) {
  const locator = page.getByText(text).first();

  await locator.scrollIntoViewIfNeeded();
  await expect(locator).toBeVisible();
}

export async function expectFooterVisible(page) {
  const footer = page.locator('footer');

  await footer.scrollIntoViewIfNeeded();
  await expect(footer).toBeVisible();
  await expect(footer).toContainText(/ProximaJob/i);
  await expect(footer.getByRole('link', { name: /Contact/i })).toBeVisible();
}

export async function scrollThroughFullPage(page, steps = 8) {
  for (let index = 0; index <= steps; index += 1) {
    await page.evaluate((progress) => {
      const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
      window.scrollTo(0, Math.max(0, maxScroll * progress));
    }, index / steps);
    await page.waitForTimeout(100);
  }
}
