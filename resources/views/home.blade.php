@extends('back.layouts.pages-layout')

@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Dashboard')

@push('stylesheets')
<style>
    /* Container Text Colors Override */
    .container *, .container-fluid * {
        color: var(--text-primary) !important;
    }
    
    .container .text-muted, .container-fluid .text-muted {
        color: var(--text-muted) !important;
    }

    /* Dashboard Cards - Dark Theme */
    .dashboard-card {
        border: none;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.85) 100%);
        backdrop-filter: blur(4px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(59, 130, 246, 0.2);
        transition: all 0.2s ease;
        overflow: hidden;
        position: relative;
        height: 100%;
        color: var(--text-primary) !important;
    }

    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--accent-gradient);
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(59, 130, 246, 0.2);
        border-color: rgba(59, 130, 246, 0.3);
    }

    .dashboard-card .card-body {
        padding: 32px 24px;
        position: relative;
        z-index: 2;
    }

    /* Card Icons - Yüksek kontrast */
    .card-icon {
        font-size: 3.5rem;
        margin-bottom: 24px;
        background: linear-gradient(135deg, #4c51bf 0%, #553c9a 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: all 0.2s ease;
    }

    .dashboard-card:hover .card-icon {
        transform: scale(1.05);
    }

    /* Specific icon colors for different cards - Yüksek kontrast */
    .card-automation .card-icon {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .card-scenarios .card-icon {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .card-reports .card-icon {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .card-settings .card-icon {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Card Titles - Dark Theme */
    .dashboard-card .card-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary) !important;
        margin-bottom: 16px;
        transition: all 0.2s ease;
    }

    .dashboard-card:hover .card-title {
        color: var(--accent-color) !important;
    }

    /* Card Text - Dark Theme */
    .dashboard-card .card-text {
        color: var(--text-secondary) !important;
        font-size: 1rem;
        line-height: 1.7;
        margin-bottom: 0;
        font-weight: 500;
    }

    /* Remove link decoration */
    .dashboard-link {
        text-decoration: none !important;
        color: inherit;
        display: block;
        height: 100%;
    }

    .dashboard-link:hover {
        color: inherit;
    }

    /* Welcome Section - Dark Theme */
    .welcome-section {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.85) 100%);
        backdrop-filter: blur(4px);
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 32px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(59, 130, 246, 0.2);
        position: relative;
        overflow: hidden;
        color: var(--text-primary) !important;
    }

    .welcome-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(76, 81, 191, 0.08) 0%, transparent 70%);
        transform: translate(40px, -40px);
    }

    .welcome-section h2 {
        font-size: 2.5rem;
        font-weight: 900;
        background: var(--accent-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 16px;
        position: relative;
        z-index: 2;
    }

    .welcome-section p {
        font-size: 1.1rem;
        color: var(--text-secondary) !important;
        margin-bottom: 0;
        position: relative;
        z-index: 2;
        font-weight: 500;
        line-height: 1.6;
    }

    /* Hover effects for card backgrounds - Yüksek kontrast */
    .card-automation:hover {
        background: linear-gradient(135deg, rgba(2, 132, 199, 0.04) 0%, rgba(3, 105, 161, 0.04) 100%);
    }

    .card-scenarios:hover {
        background: linear-gradient(135deg, rgba(5, 150, 105, 0.04) 0%, rgba(4, 120, 87, 0.04) 100%);
    }

    .card-reports:hover {
        background: linear-gradient(135deg, rgba(217, 119, 6, 0.04) 0%, rgba(180, 83, 9, 0.04) 100%);
    }

    .card-settings:hover {
        background: linear-gradient(135deg, rgba(124, 58, 237, 0.04) 0%, rgba(109, 40, 217, 0.04) 100%);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .welcome-section h2 {
            font-size: 2rem;
        }
        
        .welcome-section p {
            font-size: 1rem;
        }
        
        .card-icon {
            font-size: 3rem;
        }
        
        .dashboard-card .card-title {
            font-size: 1.3rem;
        }
        
        .dashboard-card .card-body {
            padding: 24px 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="container mt-4">

    <div class="row">
        <div class="col-md-12">
            <div class="welcome-section">
                <h2>Hoş Geldin 👋</h2>
                <p>Test yönetimi, AI senaryo üretimi ve raporlamayı kolayca yönetebilirsin.</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Test Otomasyonu Kartı -->
        <div class="col-md-3">
            <a href="/test-automation" class="dashboard-link">
                <div class="card dashboard-card card-automation">
                    <div class="card-body text-center">
                        <i class="fas fa-cogs card-icon"></i>
                        <h5 class="card-title">Test Otomasyonu</h5>
                        <p class="card-text">Playwright otomasyonu ile testleri çalıştır ve sonuçları incele.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Kendi Senaryolarım Kartı -->
        <div class="col-md-3">
            <a href="/my-scenarios" class="dashboard-link">
                <div class="card dashboard-card card-scenarios">
                    <div class="card-body text-center">
                        <i class="fas fa-file-alt card-icon"></i>
                        <h5 class="card-title">Kendi Senaryolarım</h5>
                        <p class="card-text">Manuel oluşturduğun veya AI çıktısı olan tüm senaryolar burada.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Test Raporları Kartı -->
        <div class="col-md-3">
            <a href="/test-reports" class="dashboard-link">
                <div class="card dashboard-card card-reports">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line card-icon"></i>
                        <h5 class="card-title">Test Raporları</h5>
                        <p class="card-text">Otomasyon test raporlarını yükle ve detaylı incele.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Ayarlar Kartı -->
        <div class="col-md-3">
            <a href="/settings" class="dashboard-link">
                <div class="card dashboard-card card-settings">
                    <div class="card-body text-center">
                        <i class="fas fa-cog card-icon"></i>
                        <h5 class="card-title">Ayarlar</h5>
                        <p class="card-text">API anahtarları, hesap ve sistem ayarlarını yönet.</p>
                    </div>
                </div>
            </a>
        </div>

    </div>

</div>
@endsection
