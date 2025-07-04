// @ts-check
const { test, expect } = require('@playwright/test');

test('Senaryo Yönetimi - Yeni Senaryo Oluşturma', async ({ page }) => {
  await page.goto('http://localhost:8000/my-scenarios');
  
  // Yeni Senaryo butonunu bul ve tıkla
  const newScenarioButton = page.locator('button:has-text("Yeni Senaryo")');
  await expect(newScenarioButton).toBeVisible();
  await newScenarioButton.click();
  
  // Modal açıldığını kontrol et
  const modal = page.locator('#createScenarioModal');
  await expect(modal).toBeVisible();
  
  // Form alanlarını doldur
  await page.fill('input[name="title"]', 'Test Senaryosu - Otomatik');
  await page.fill('textarea[name="description"]', 'Bu senaryo Playwright tarafından otomatik olarak oluşturuldu.');
  
  // İlk adımı doldur
  await page.fill('input[name="steps[]"]', 'İlk test adımı');
  
  console.log('✅ Senaryo formu dolduruldu');
});

test('Senaryo Yönetimi - Test Suit Oluşturma', async ({ page }) => {
  await page.goto('http://localhost:8000/my-scenarios');
  
  // Sayfa yüklenene kadar bekle
  await page.waitForLoadState('networkidle');
  
  // Checkbox'ları kontrol et
  const checkboxes = page.locator('.scenario-checkbox');
  const checkboxCount = await checkboxes.count();
  
  if (checkboxCount >= 2) {
    // İlk iki senaryoyu seç
    await checkboxes.nth(0).check();
    await checkboxes.nth(1).check();
    
    // Test Suit butonunun görünür olduğunu kontrol et
    const suitButton = page.locator('#suitButton');
    await expect(suitButton).toBeVisible();
    
    console.log('✅ Test suit oluşturma hazır');
  } else {
    console.log('⚠️  Yeterli senaryo bulunamadı');
  }
});

test('Regresyon Testi - Sayfa Kontrolü', async ({ page }) => {
  await page.goto('http://localhost:8000/regression');
  
  // Regresyon sayfası yüklendiğini kontrol et
  await expect(page.locator('h4')).toContainText('Regresyon');
  
  // Test suit'leri listesini kontrol et
  const suitsList = page.locator('.suit-item');
  const suitsCount = await suitsList.count();
  
  if (suitsCount > 0) {
    console.log(`✅ ${suitsCount} adet test suit bulundu`);
  } else {
    console.log('ℹ️  Henüz test suit oluşturulmamış');
  }
});
