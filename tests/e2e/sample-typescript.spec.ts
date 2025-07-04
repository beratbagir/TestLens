import { test, expect } from '@playwright/test';

test('Homepage loads correctly', async ({ page }) => {
  await page.goto('https://example.com');
  
  // Check if the page title contains "Example"
  await expect(page).toHaveTitle(/Example/);
  
  // Check if the main heading is visible
  const heading = page.locator('h1');
  await expect(heading).toBeVisible();
  
  console.log('✅ Homepage test completed successfully');
});

test('Navigation works', async ({ page }) => {
  await page.goto('https://example.com');
  
  // Check if there are links on the page
  const links = page.locator('a');
  const linkCount = await links.count();
  
  expect(linkCount).toBeGreaterThan(0);
  
  console.log(`✅ Found ${linkCount} links on the page`);
});
