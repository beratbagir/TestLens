// @ts-check
const { test, expect } = require('@playwright/test');

test('Örnek Test - Ana Sayfa Kontrolü', async ({ page }) => {
  await page.goto('http://localhost:8000');
  
  // Sayfa başlığını kontrol et
  await expect(page).toHaveTitle(/Laravel/);
  
  // Ana sayfa elementlerini kontrol et
  const heading = page.locator('h1');
  await expect(heading).toBeVisible();
  
  console.log('✅ Ana sayfa testi başarılı');
});

test('Örnek Test - Navigation Menü Kontrolü', async ({ page }) => {
  await page.goto('http://localhost:8000/home');
  
  // Dashboard linkini kontrol et
  const dashboardLink = page.locator('a[href="/home"]');
  await expect(dashboardLink).toBeVisible();
  
  // Test Otomasyonu linkini kontrol et  
  const testAutomationLink = page.locator('a[href="/test-automation"]');
  await expect(testAutomationLink).toBeVisible();
  
  // Kendi Senaryolarım linkini kontrol et
  const scenariosLink = page.locator('a[href="/my-scenarios"]');
  await expect(scenariosLink).toBeVisible();
  
  console.log('✅ Navigation menü testi başarılı');
});

test('Örnek Test - Test Otomasyonu Sayfası', async ({ page }) => {
  await page.goto('http://localhost:8000/test-automation');
  
  // Sayfa başlığını kontrol et
  await expect(page.locator('h2')).toContainText('Test Otomasyonu');
  
  // Test dosyaları bölümünü kontrol et
  const testFilesSection = page.locator('.card').first();
  await expect(testFilesSection).toBeVisible();
  
  // Butonları kontrol et
  const runAllButton = page.locator('button:has-text("Tüm Testleri Çalıştır")');
  await expect(runAllButton).toBeVisible();
  
  console.log('✅ Test Otomasyonu sayfası testi başarılı');
});
