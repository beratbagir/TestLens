# TestLens - QA Test Management System

![TestLens Logo](https://img.shields.io/badge/TestLens-QA%20Management-blue?style=for-the-badge)
![Laravel](https://img.shields.io/badge/Laravel-10.x-red?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1+-purple?style=for-the-badge&logo=php)
![JIRA](https://img.shields.io/badge/JIRA-Integration-0052cc?style=for-the-badge&logo=jira)

TestLens, QA ekipleri için tasarlanmış kapsamlı bir test yönetim sistemidir. Manuel test senaryoları, otomatik test yürütme, regresyon testleri ve JIRA entegrasyonu ile test süreçlerinizi tek bir platformda yönetmenizi sağlar.

## 🚀 Özellikler

### 📋 Manuel Test Yönetimi
- **Senaryo Oluşturma**: Detaylı test senaryoları oluşturma ve düzenleme
- **Test Süitleri**: Senaryoları gruplandırma ve organize etme
- **Test Sonuçları**: Pass/Fail durumları ve detaylı notlar
- **Bulk İşlemler**: Çoklu senaryo yönetimi

### 🤖 Test Otomasyonu
- **Playwright Integration**: E2E test otomasyonu
- **Multi-Browser Support**: Chromium, Firefox, Safari desteği
- **Test Execution**: Tekil ve toplu test yürütme
- **Real-time Results**: Canlı test sonuçları ve raporlama
- **Flexible Configuration**: Headless/GUI mod seçenekleri

### 📊 Regresyon Testleri
- **Automated Regression**: Otomatik regresyon test yürütme
- **Excel Export**: Test sonuçlarını Excel formatında dışa aktarma
- **Detailed Reports**: Kapsamlı test raporları
- **Historical Data**: Geçmiş test verilerini takip etme

### 🔗 JIRA Entegrasyonu
- **Task Management**: JIRA taskları görüntüleme ve yönetme
- **Comment System**: Tasklara yorum ekleme
- **Issue Creation**: Yeni JIRA issue oluşturma
- **JQL Support**: Gelişmiş JIRA sorguları
- **Real-time Sync**: Canlı JIRA entegrasyonu

### 📈 Raporlama ve Analiz
- **Test Reports**: Detaylı test raporları
- **Visual Dashboard**: Grafik ve istatistikler
- **Export Options**: PDF, Excel, JSON formatları
- **Performance Metrics**: Test performans metrikleri

### 🎨 Modern Arayüz
- **Dark Theme**: Göz dostu karanlık tema
- **Responsive Design**: Mobil uyumlu tasarım
- **Intuitive UX**: Kullanıcı dostu arayüz
- **Real-time Updates**: Canlı güncellemeler

## 🛠️ Kurulum

### Gereksinimler

- PHP 8.1 veya üstü
- Composer
- Node.js 16+ (opsiyonel, frontend assets için)
- SQLite/MySQL/PostgreSQL
- Git

### Adım Adım Kurulum

1. **Projeyi klonlayın**
```bash
git clone https://github.com/yourusername/testlens.git
cd testlens
```

2. **Bağımlılıkları yükleyin**
```bash
composer install
```

3. **Environment dosyasını yapılandırın**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Veritabanını yapılandırın**
```bash
# .env dosyasında veritabanı ayarlarını yapılandırın
php artisan migrate
```

5. **Storage klasörlerini oluşturun**
```bash
php artisan storage:link
mkdir -p storage/app/projects
mkdir -p storage/app/settings
mkdir -p storage/app/test-results
```

6. **Sunucuyu başlatın**
```bash
php artisan serve
```

Uygulama `http://localhost:8000` adresinde çalışacaktır.

## ⚙️ Yapılandırma

### Test Otomasyonu Ayarları

1. **Ayarlar** sayfasına gidin
2. **Test Otomasyonu** bölümünde:
   - Test dizinlerini belirleyin
   - Browser seçeneklerini ayarlayın
   - Timeout değerlerini yapılandırın
   - Reporter formatını seçin

### JIRA Entegrasyonu

1. **Ayarlar** sayfasında **JIRA Entegrasyon Ayarları** bölümünü bulun
2. Gerekli bilgileri doldurun:
   - **JIRA Server URL**: `https://yourcompany.atlassian.net`
   - **Kullanıcı Adı/Email**: JIRA hesap bilgileriniz
   - **API Token**: JIRA'dan oluşturduğunuz API token
   - **Proje Anahtarı**: Varsayılan proje anahtarı
   - **Issue Tipi**: Varsayılan issue tipi
   - **Öncelik**: Varsayılan öncelik seviyesi

3. **Bağlantıyı Test Et** butonuna tıklayarak ayarları doğrulayın

### Proje Yükleme

Test projelerinizi sisteme yüklemek için:

1. **Ayarlar** sayfasında **Proje Yükleme** bölümünü kullanın
2. ZIP dosyası olarak test projelerinizi yükleyin
3. Yüklenen projeler otomatik olarak `storage/app/projects` klasörüne çıkarılır

## 📚 Kullanım Kılavuzu

### Manuel Test Senaryoları

1. **Manuel Senaryolarım** sayfasına gidin
2. **Yeni Senaryo Ekle** butonuna tıklayın
3. Senaryo detaylarını doldurun:
   - Test adı
   - Açıklama
   - Beklenen sonuç
   - Test adımları
4. **Kaydet** ile senaryoyu kaydedin
5. Test sonuçlarını **Pass/Fail** olarak işaretleyin

### Test Otomasyonu

1. **Test Otomasyonu** sayfasına gidin
2. Test dosyalarınızı listeden seçin
3. **Testi Çalıştır** butonuna tıklayın
4. Test sonuçlarını **Test Raporları** sayfasından görüntüleyin

### JIRA Taskları

1. **JIRA Taskları** sayfasına gidin
2. JQL sorgusu ile taskları filtreleyin
3. Tasklara yorum ekleyin
4. **Yeni Issue Oluştur** ile yeni JIRA issue'ları oluşturun

### Regresyon Testleri

1. **Regresyon** sayfasına gidin
2. Test süitlerini seçin
3. **Regresyon Testi Başlat** butonuna tıklayın
4. Sonuçları Excel formatında dışa aktarın

## 🔧 API Endpoints

### Test Otomasyonu
- `POST /test-automation/run-test` - Tekil test çalıştırma
- `POST /test-automation/run-multiple` - Çoklu test çalıştırma
- `POST /test-automation/run-all` - Tüm testleri çalıştırma
- `GET /test-automation/results` - Test sonuçlarını getirme

### JIRA Entegrasyonu
- `GET /jira-tasks` - JIRA taskları listesi
- `POST /jira-tasks/fetch` - JQL ile task getirme
- `POST /jira-tasks/{issueKey}/comment` - Yorum ekleme
- `POST /jira-tasks/create-issue` - Yeni issue oluşturma

### Regresyon Testleri
- `POST /regression/run` - Regresyon testi çalıştırma
- `POST /regression/save-results` - Sonuçları kaydetme
- `GET /regression/export/{id}` - Excel export

## 🎯 Test Senaryoları

### Örnek Test Senaryosu

```javascript
// tests/e2e/login.spec.js
const { test, expect } = require('@playwright/test');

test('User Login Test', async ({ page }) => {
  await page.goto('/login');
  await page.fill('#username', 'testuser');
  await page.fill('#password', 'testpass');
  await page.click('#login-btn');
  await expect(page).toHaveURL('/dashboard');
});
```

### JQL Sorgu Örnekleri

```sql
-- Bana atanmış açık tasklar
assignee = currentUser() AND resolution = Unresolved

-- Yüksek öncelikli buglar
priority = High AND issuetype = Bug

-- Son 7 gün içinde güncellenen tasklar
updated >= -7d ORDER BY updated DESC

-- Belirli projedeki tüm tasklar
project = "TEST" AND status != Done
```

## 📊 Raporlama

### Test Raporu Formatları

- **JSON**: Detaylı test sonuçları
- **HTML**: Görsel raporlar
- **Excel**: Tablo formatında veriler
- **PDF**: Yazdırılabilir raporlar

### Metrikler

- Test başarı oranı
- Ortalama test süresi
- Hata dağılımı
- Performans trendi

## 🔐 Güvenlik

### JIRA API Güvenliği

- API tokenları güvenli şekilde saklanır
- HTTPS bağlantı zorunluluğu
- Rate limiting koruması
- Credential encryption

### Dosya Güvenliği

- Yüklenen dosyalar sanitize edilir
- Güvenli dosya uzantı kontrolü
- Maksimum dosya boyutu sınırı
- Virus scanning (opsiyonel)

## 🚀 Deployment

### Production Kurulum

1. **Server Gereksinimleri**
   - PHP 8.1+ with required extensions
   - Web server (Apache/Nginx)
   - Database (MySQL/PostgreSQL)
   - SSL sertifikası

2. **Environment Yapılandırması**
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

3. **Optimization**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

### Docker Kurulum

```dockerfile
FROM php:8.1-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip \
    unzip

# Copy application
COPY . /var/www/html/

# Install composer dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage

EXPOSE 80
```

## 🤝 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some AmazingFeature'`)
4. Branch'inizi push edin (`git push origin feature/AmazingFeature`)
5. Pull Request oluşturun

## 📝 Changelog

### v2.0.0 (2025-07-04)
- ✨ JIRA entegrasyonu eklendi
- 🎨 Dark theme iyileştirmeleri
- 🔧 Bulk test operations
- 📊 Gelişmiş raporlama sistemi

### v1.5.0 (2025-06-15)
- 🚀 Playwright entegrasyonu
- 📋 Manuel test yönetimi
- 🔄 Regresyon testleri
- 📈 Dashboard metrikleri

### v1.0.0 (2025-05-01)
- 🎉 İlk sürüm yayınlandı
- 📝 Temel test yönetimi
- 🎯 Proje yükleme sistemi

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakın.

## 🆘 Destek

### Dokümantasyon
- [Kurulum Kılavuzu](docs/installation.md)
- [API Referansı](docs/api.md)
- [Kullanım Örnekleri](docs/examples.md)

### İletişim
- **Email**: support@testlens.com
- **Discord**: [TestLens Community](https://discord.gg/testlens)
- **Issues**: [GitHub Issues](https://github.com/yourusername/testlens/issues)

### Sık Sorulan Sorular

**Q: JIRA bağlantısı kurulamıyor?**
A: API token'ınızın doğru olduğunu ve JIRA URL'inizin https:// ile başladığını kontrol edin.

**Q: Test dosyaları çalışmıyor?**
A: Playwright'ın kurulu olduğunu ve test dosyalarınızın doğru formatta olduğunu kontrol edin.

**Q: Büyük dosyalar yüklenemiyor?**
A: PHP'nin upload_max_filesize ve post_max_size ayarlarını kontrol edin.

---

<div align="center">
  <p>TestLens ile test süreçlerinizi modernleştirin! 🚀</p>
  <p>Made with ❤️ by QA Team</p>
</div>

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
