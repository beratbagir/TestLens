// @ts-check
const { test, expect } = require('@playwright/test');

test('Sample test for TestLens', async ({ page }) => {
  await page.goto('https://example.com');
  
  // Check if the page title contains "Example"
  await expect(page).toHaveTitle(/Example/);
  
  // Check if the main heading is visible
  const heading = page.locator('h1');
  await expect(heading).toBeVisible();
  
  console.log('✅ Sample test completed successfully');
});

test('Another sample test', async ({ page }) => {
  await page.goto('https://httpbin.org/html');
  
  // Check if the page loads
  await expect(page).toHaveTitle(/Herman Melville - Moby Dick/);
  
  console.log('✅ Second test completed successfully');
});
