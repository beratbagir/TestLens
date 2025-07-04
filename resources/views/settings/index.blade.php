@extends('back.layouts.pages-layout')
@section('pageTitle', 'Ayarlar')

@push('stylesheets')
<style>
    /* Settings Card Styling - Yüksek kontrast ve performans */
    .settings-card {
        border: none;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(4px);
        box-shadow: 0 4px 20px rgba(76, 81, 191, 0.12);
        transition: all 0.2s ease;
        overflow: hidden;
    }

    .settings-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 32px rgba(76, 81, 191, 0.18);
    }

    .settings-header {
        background: linear-gradient(135deg, #4c51bf 0%, #553c9a 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        border: none;
        position: relative;
        overflow: hidden;
        padding: 20px 24px;
    }

    .settings-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 80px;
        height: 80px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
        transform: translate(20px, -20px);
    }

    .settings-header h5 {
        position: relative;
        z-index: 2;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    /* Form Controls - Yüksek kontrast */
    .form-control, .form-select {
        border-radius: 8px;
        border: 2px solid rgba(76, 81, 191, 0.15);
        padding: 12px 16px;
        transition: all 0.15s ease;
        background: white;
        color: #1a202c;
        font-weight: 500;
        font-size: 14px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #4c51bf;
        box-shadow: 0 0 0 0.25rem rgba(76, 81, 191, 0.25);
        background: white;
        outline: none;
    }

    .form-control::placeholder {
        color: #4a5568;
        opacity: 0.7;
    }

    /* Labels - Yüksek kontrast */
    .form-label {
        color: #1a202c;
        font-weight: 700;
        margin-bottom: 8px;
        font-size: 14px;
    }

    /* Custom Buttons - Yüksek kontrast */
    .btn-custom {
        background: linear-gradient(135deg, #4c51bf 0%, #553c9a 100%);
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        font-weight: 700;
        transition: all 0.15s ease;
        box-shadow: 0 2px 8px rgba(76, 81, 191, 0.25);
        color: white;
        font-size: 14px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .btn-custom:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(76, 81, 191, 0.35);
        color: white;
        filter: brightness(1.05);
    }

    .btn-custom *,
    .btn-custom:hover * {
        position: relative;
        z-index: 2;
    }

    /* Directory Items */
    .directory-item {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(102, 126, 234, 0.1) 100%);
        border: 1px solid rgba(102, 126, 234, 0.1);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .directory-item:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        border-color: rgba(102, 126, 234, 0.2);
        transform: translateX(4px);
    }

    /* Badge Styling */
    .badge-custom {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(72, 187, 120, 0.3);
    }

    /* Progress Bar Styling */
    .progress {
        height: 8px;
        border-radius: 20px;
        background: rgba(102, 126, 234, 0.1);
        overflow: hidden;
    }

    .progress-bar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        transition: width 0.3s ease;
    }

    .progress-bar-animated {
        animation: progress-bar-stripes 1s linear infinite;
    }

    @keyframes progress-bar-stripes {
        0% { background-position: 40px 0; }
        100% { background-position: 0 0; }
    }

    /* File Upload Area */
    .upload-area {
        border: 2px dashed rgba(102, 126, 234, 0.3);
        border-radius: 16px;
        padding: 40px 20px;
        text-align: center;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%);
    }

    .upload-area:hover {
        border-color: rgba(102, 126, 234, 0.5);
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    }

    /* Project Cards */
    .project-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(247, 250, 252, 0.9) 100%);
        border: 1px solid rgba(102, 126, 234, 0.1);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .project-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.1);
        border-color: rgba(102, 126, 234, 0.2);
    }

    /* System Info Cards */
    .info-item {
        padding: 12px 0;
        border-bottom: 1px solid rgba(102, 126, 234, 0.1);
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 4px;
    }

    /* Alert Improvements */
    .alert {
        border: none;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
        backdrop-filter: blur(10px);
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(72, 187, 120, 0.1) 0%, rgba(56, 161, 105, 0.1) 100%);
        color: #2f855a;
        border-left: 4px solid #48bb78;
    }

    .alert-danger {
        background: linear-gradient(135deg, rgba(245, 101, 101, 0.1) 0%, rgba(229, 62, 62, 0.1) 100%);
        color: #c53030;
        border-left: 4px solid #f56565;
    }

    .alert-info {
        background: linear-gradient(135deg, rgba(66, 153, 225, 0.1) 0%, rgba(49, 130, 206, 0.1) 100%);
        color: #2b6cb0;
        border-left: 4px solid #4299e1;
    }

    /* Input Groups */
    .input-group {
        border-radius: 12px;
        overflow: hidden;
    }

    .input-group .form-control {
        border-radius: 0;
        border-right: none;
    }

    .input-group .btn {
        border-radius: 0;
        border-left: none;
    }

    /* Loading States */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        backdrop-filter: blur(5px);
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 4px;
        color: #667eea;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #718096;
    }

    .empty-state i {
        font-size: 3rem;
        color: #cbd5e0;
        margin-bottom: 16px;
    }

    /* Tooltips */
    .tooltip-inner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 12px;
    }

    .tooltip .arrow::before {
        border-top-color: #667eea;
    }
</style>
</style>
@endpush

@section('content')
<div class="container mt-4">
    
    <div class="row">
        <div class="col-12 mb-4">
            <h2 class="fw-bold">
                <i class="fas fa-cog text-primary me-2"></i>
                Sistem Ayarları
            </h2>
            <p class="text-muted">Test otomasyonu ve sistem konfigürasyonlarını yönetin</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Test Otomasyonu Ayarları -->
        <div class="col-md-8">
            <div class="card settings-card">
                <div class="card-header settings-header py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-robot me-2"></i>
                        Test Otomasyonu Ayarları
                    </h5>
                </div>
                <div class="card-body p-4">
                    
                    <form action="{{ route('settings.updateTestAutomation') }}" method="POST">
                        @csrf
                        
                        <!-- Gizli test dizinleri - kullanıcıya gösterilmez -->
                        @foreach($settings['test_directories'] as $directory)
                            <input type="hidden" name="test_directories[]" value="{{ $directory }}">
                        @endforeach
                        
                        <!-- Playwright Ayarları -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-chart-bar text-success me-2"></i>
                                    Varsayılan Reporter
                                </label>
                                <select name="default_reporter" class="form-select">
                                    <option value="json" {{ $settings['default_reporter'] == 'json' ? 'selected' : '' }}>JSON</option>
                                    <option value="html" {{ $settings['default_reporter'] == 'html' ? 'selected' : '' }}>HTML</option>
                                    <option value="junit" {{ $settings['default_reporter'] == 'junit' ? 'selected' : '' }}>JUnit</option>
                                    <option value="line" {{ $settings['default_reporter'] == 'line' ? 'selected' : '' }}>Line</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-clock text-info me-2"></i>
                                    Timeout (ms)
                                </label>
                                <input type="number" name="timeout" class="form-control" 
                                       value="{{ $settings['timeout'] }}" min="1000" step="1000">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-globe text-primary me-2"></i>
                                    Tarayıcı
                                </label>
                                <select name="browser" class="form-select">
                                    <option value="chromium" {{ $settings['browser'] == 'chromium' ? 'selected' : '' }}>Chromium</option>
                                    <option value="firefox" {{ $settings['browser'] == 'firefox' ? 'selected' : '' }}>Firefox</option>
                                    <option value="webkit" {{ $settings['browser'] == 'webkit' ? 'selected' : '' }}>WebKit</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-eye text-warning me-2"></i>
                                    Headless Mod
                                </label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="headless" 
                                           value="1" {{ $settings['headless'] ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        Testleri arka planda çalıştır
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-custom text-white">
                                <i class="fas fa-save me-2"></i>
                                Ayarları Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sistem Bilgileri -->
        <div class="col-md-4">
            <div class="card settings-card">
                <div class="card-header settings-header py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle me-2"></i>
                        Sistem Bilgileri
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>PHP Versiyonu:</strong>
                        <span class="badge-custom">{{ PHP_VERSION }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Laravel Versiyonu:</strong>
                        <span class="badge-custom">{{ app()->version() }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Test Sonuçları:</strong>
                        <span class="badge-custom">
                            {{ count(\Illuminate\Support\Facades\Storage::files('test-results')) }} dosya
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Toplam Test Dosyası:</strong>
                        @php
                            $testCount = 0;
                            foreach($settings['test_directories'] as $dir) {
                                $fullPath = base_path($dir);
                                if (is_dir($fullPath)) {
                                    $files = new \RecursiveIteratorIterator(
                                        new \RecursiveDirectoryIterator($fullPath, \RecursiveDirectoryIterator::SKIP_DOTS)
                                    );
                                    foreach ($files as $file) {
                                        if ($file->isFile() && preg_match('/\.(spec|test)\.(js|ts)$/', $file->getFilename())) {
                                            $testCount++;
                                        }
                                    }
                                }
                            }
                        @endphp
                        <span class="badge-custom">{{ $testCount }} test</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JIRA Integration Ayarları -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card settings-card">
                <div class="card-header settings-header py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fab fa-jira me-2"></i>
                        JIRA Entegrasyon Ayarları
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('settings.updateJira') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-server text-primary me-2"></i>
                                    JIRA Server URL
                                </label>
                                <input type="url" name="jira_url" class="form-control" 
                                       value="{{ old('jira_url', $settings['jira_url'] ?? '') }}" 
                                       placeholder="https://yourcompany.atlassian.net">
                                <div class="form-text">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        JIRA Cloud veya Server URL'nizi girin
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-user text-info me-2"></i>
                                    Kullanıcı Adı / Email
                                </label>
                                <input type="text" name="jira_username" class="form-control" 
                                       value="{{ old('jira_username', $settings['jira_username'] ?? '') }}" 
                                       placeholder="user@company.com">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-key text-warning me-2"></i>
                                    API Token
                                </label>
                                <div class="input-group">
                                    <input type="password" name="jira_api_token" class="form-control" 
                                           value="{{ old('jira_api_token', $settings['jira_api_token'] ?? '') }}" 
                                           placeholder="JIRA API Token" id="jiraApiToken">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleApiToken">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        JIRA hesabınızdan oluşturacağınız API token
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-project-diagram text-success me-2"></i>
                                    Varsayılan Proje Anahtarı
                                </label>
                                <input type="text" name="jira_project_key" class="form-control" 
                                       value="{{ old('jira_project_key', $settings['jira_project_key'] ?? '') }}" 
                                       placeholder="TEST" style="text-transform: uppercase;">
                                <div class="form-text">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Issue oluştururken kullanılacak proje anahtarı
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-tags text-danger me-2"></i>
                                    Varsayılan Issue Tipi
                                </label>
                                <select name="jira_issue_type" class="form-select">
                                    <option value="Bug" {{ ($settings['jira_issue_type'] ?? '') == 'Bug' ? 'selected' : '' }}>Bug</option>
                                    <option value="Task" {{ ($settings['jira_issue_type'] ?? '') == 'Task' ? 'selected' : '' }}>Task</option>
                                    <option value="Story" {{ ($settings['jira_issue_type'] ?? '') == 'Story' ? 'selected' : '' }}>Story</option>
                                    <option value="Test" {{ ($settings['jira_issue_type'] ?? '') == 'Test' ? 'selected' : '' }}>Test</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                    Varsayılan Öncelik
                                </label>
                                <select name="jira_priority" class="form-select">
                                    <option value="Highest" {{ ($settings['jira_priority'] ?? '') == 'Highest' ? 'selected' : '' }}>Highest</option>
                                    <option value="High" {{ ($settings['jira_priority'] ?? '') == 'High' ? 'selected' : '' }}>High</option>
                                    <option value="Medium" {{ ($settings['jira_priority'] ?? '') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="Low" {{ ($settings['jira_priority'] ?? '') == 'Low' ? 'selected' : '' }}>Low</option>
                                    <option value="Lowest" {{ ($settings['jira_priority'] ?? '') == 'Lowest' ? 'selected' : '' }}>Lowest</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-primary" id="testJiraConnection">
                                <i class="fas fa-plug me-2"></i>
                                Bağlantıyı Test Et
                            </button>
                            
                            <button type="submit" class="btn btn-custom text-white">
                                <i class="fas fa-save me-2"></i>
                                JIRA Ayarlarını Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Proje Yükleme Bölümü -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card settings-card">
                <div class="card-header settings-header py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-cloud-upload-alt me-2"></i>
                        Test Projesi Yükleme
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Yükleme Formu -->
                        <div class="col-md-6">
                            <form id="uploadForm" onsubmit="return false;">
                                @csrf
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-file-archive text-primary me-2"></i>
                                        Proje ZIP Dosyası
                                    </label>
                                    <input type="file" name="project_zip" class="form-control" 
                                           accept=".zip" required id="fileInput">
                                    <div class="form-text">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Playwright test dosyalarını içeren ZIP dosyası yükleyin (Max: 100MB)
                                        </small>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-custom text-white" id="uploadBtn" onclick="startChunkedUpload()">
                                    <i class="fas fa-upload me-2"></i>
                                    Projeyi Yükle
                                </button>
                                
                                <!-- Progress Bar -->
                                <div class="progress mt-3" id="uploadProgress" style="display: none;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                         role="progressbar" style="width: 0%">
                                        <span class="progress-text">Yükleniyor...</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Yüklü Projeler -->
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-folder-open text-warning me-2"></i>
                                Yüklü Projeler
                            </h6>
                            
                            @if(isset($settings['uploaded_projects']) && count($settings['uploaded_projects']) > 0)
                                @foreach($settings['uploaded_projects'] as $projectName => $projectInfo)
                                    <div class="directory-item">
                                        <div>
                                            <div class="fw-bold">{{ $projectName }}</div>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ date('d.m.Y H:i', strtotime($projectInfo['uploaded_at'])) }}
                                                <span class="badge badge-custom ms-2">
                                                    {{ $projectInfo['test_count'] }} test
                                                </span>
                                            </small>
                                        </div>
                                        <form action="{{ route('settings.deleteProject', $projectName) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Bu projeyi silmek istediğinizden emin misiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2 mb-0">Henüz proje yüklenmemiş</p>
                                    <small class="text-muted">
                                        Playwright test projelerinizi ZIP formatında yükleyebilirsiniz
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Bilgilendirme -->
                    <div class="alert alert-info mt-4" role="alert">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Bilgi:</strong> Yüklenen projelerdeki test dosyaları otomatik olarak 
                        "Test Otomasyonu" sayfasında listelenir ve çalıştırılabilir hale gelir.
                        Test dosyaları <code>/tests</code>, <code>/playwright</code> veya <code>/e2e</code> 
                        dizinlerinde aranır.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Chunked Upload için değişkenler
let currentFile = null;
const CHUNK_SIZE = 1024 * 1024; // 1MB chunks

// JIRA API Token görünürlük toggle
document.addEventListener('DOMContentLoaded', function() {
    const toggleApiToken = document.getElementById('toggleApiToken');
    const apiTokenInput = document.getElementById('jiraApiToken');
    
    if (toggleApiToken && apiTokenInput) {
        toggleApiToken.addEventListener('click', function() {
            const type = apiTokenInput.getAttribute('type') === 'password' ? 'text' : 'password';
            apiTokenInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    
    // JIRA bağlantı testi
    const testConnectionBtn = document.getElementById('testJiraConnection');
    if (testConnectionBtn) {
        testConnectionBtn.addEventListener('click', async function() {
            const btn = this;
            const originalHTML = btn.innerHTML;
            
            // Form verilerini al
            const jiraUrl = document.querySelector('input[name="jira_url"]').value;
            const jiraUsername = document.querySelector('input[name="jira_username"]').value;
            const jiraApiToken = document.querySelector('input[name="jira_api_token"]').value;
            
            if (!jiraUrl || !jiraUsername || !jiraApiToken) {
                alert('Lütfen tüm JIRA bağlantı bilgilerini doldurun!');
                return;
            }
            
            // Loading state
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Test ediliyor...';
            
            try {
                const response = await fetch('/settings/test-jira-connection', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        jira_url: jiraUrl,
                        jira_username: jiraUsername,
                        jira_api_token: jiraApiToken
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Success alert
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show mt-3';
                    alert.innerHTML = `
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Başarılı!</strong> JIRA bağlantısı kuruldu. Kullanıcı: ${result.user_info.displayName}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    testConnectionBtn.parentNode.parentNode.appendChild(alert);
                } else {
                    throw new Error(result.message || 'Bağlantı testi başarısız');
                }
            } catch (error) {
                // Error alert
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show mt-3';
                alert.innerHTML = `
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Hata!</strong> ${error.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                testConnectionBtn.parentNode.parentNode.appendChild(alert);
            } finally {
                // Reset button
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        });
    }

    // Dosya boyutu kontrolü
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const maxSize = 100 * 1024 * 1024; // 100MB in bytes
                if (file.size > maxSize) {
                    alert('Dosya çok büyük! Maximum 100MB yükleyebilirsiniz. Seçilen dosya: ' + (file.size / 1024 / 1024).toFixed(2) + ' MB');
                    this.value = '';
                    currentFile = null;
                    return;
                }
                
                // Dosya uzantısı kontrolü
                const allowedTypes = ['application/zip', 'application/x-zip-compressed'];
                if (!allowedTypes.includes(file.type) && !file.name.toLowerCase().endsWith('.zip')) {
                    alert('Sadece ZIP dosyaları yükleyebilirsiniz!');
                    this.value = '';
                    currentFile = null;
                    return;
                }
                
                currentFile = file;
                console.log('Dosya seçildi:', file.name, 'Boyut:', (file.size / 1024 / 1024).toFixed(2) + ' MB');
            }
        });
    }
});

async function startChunkedUpload() {
    if (!currentFile) {
        alert('Lütfen bir dosya seçin!');
        return;
    }
    
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = uploadProgress.querySelector('.progress-bar');
    const progressText = uploadProgress.querySelector('.progress-text');
    
    // UI güncellemeleri
    uploadBtn.disabled = true;
    uploadProgress.style.display = 'block';
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Yükleniyor...';
    
    try {
        // Upload session başlat
        const sessionResponse = await fetch('/settings/start-upload', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                filename: currentFile.name,
                filesize: currentFile.size,
                total_chunks: Math.ceil(currentFile.size / CHUNK_SIZE)
            })
        });
        
        const sessionData = await sessionResponse.json();
        if (!sessionData.success) {
            throw new Error(sessionData.message || 'Upload session başlatılamadı');
        }
        
        const sessionId = sessionData.session_id;
        
        // Dosyayı chunk'lara böl ve yükle
        const totalChunks = Math.ceil(currentFile.size / CHUNK_SIZE);
        
        for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
            const start = chunkIndex * CHUNK_SIZE;
            const end = Math.min(start + CHUNK_SIZE, currentFile.size);
            const chunk = currentFile.slice(start, end);
            
            const formData = new FormData();
            formData.append('session_id', sessionId);
            formData.append('chunk_index', chunkIndex);
            formData.append('total_chunks', totalChunks);
            formData.append('chunk', chunk);
            
            const chunkResponse = await fetch('/settings/upload-chunk', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            
            const chunkData = await chunkResponse.json();
            if (!chunkData.success) {
                throw new Error(chunkData.message || 'Chunk upload failed');
            }
            
            // Progress güncelle
            const progress = ((chunkIndex + 1) / totalChunks) * 100;
            progressBar.style.width = progress + '%';
            progressText.textContent = Math.round(progress) + '%';
        }
        
        // Upload tamamlandığında dosyayı birleştir
        const completeResponse = await fetch('/settings/complete-upload', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ session_id: sessionId })
        });
        
        const completeData = await completeResponse.json();
        if (completeData.success) {
            progressBar.style.width = '100%';
            progressText.textContent = '100% - Tamamlandı!';
            
            setTimeout(() => {
                location.reload(); // Sayfayı yenile
            }, 1500);
        } else {
            throw new Error(completeData.message || 'Upload tamamlanamadı');
        }
        
    } catch (error) {
        console.error('Upload error:', error);
        alert('Upload sırasında hata oluştu: ' + error.message);
        
        // UI'yi sıfırla
        uploadBtn.disabled = false;
        uploadProgress.style.display = 'none';
        uploadBtn.innerHTML = '<i class="fas fa-upload me-2"></i>Projeyi Yükle';
    }
}
</script>
@endsection
