@extends('back.layouts.pages-layout')

@section('pageTitle', 'Test Otomasyonu')

@push('stylesheets')
<style>
    /* Container Text Colors Override */
    .container *, .container-fluid * {
        color: var(--text-primary) !important;
    }
    
    .container .text-muted, .container-fluid .text-muted {
        color: var(--text-muted) !important;
    }

    /* Test Card Styling - Dark Theme */
    .test-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.85) 100%);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-left: 4px solid var(--primary-color);
        color: var(--text-primary) !important;
        backdrop-filter: blur(8px);
    }

    .test-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(59, 130, 246, 0.2);
        border-left-width: 6px;
        border-color: rgba(59, 130, 246, 0.3);
    }

    .test-card.running {
        background: linear-gradient(135deg, rgba(55, 48, 163, 0.15) 0%, rgba(79, 70, 229, 0.1) 100%);
        border-left-color: var(--warning-color);
        color: var(--text-primary) !important;
        box-shadow: 0 4px 20px rgba(245, 158, 11, 0.15);
    }

    .test-card.success {
        background: linear-gradient(135deg, rgba(5, 150, 105, 0.15) 0%, rgba(16, 185, 129, 0.1) 100%);
        border-left-color: var(--success-color);
        color: var(--text-primary) !important;
        box-shadow: 0 4px 20px rgba(5, 150, 105, 0.15);
    }

    .test-card.failed {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.1) 100%);
        border-left-color: var(--danger-color);
        color: var(--text-primary) !important;
        box-shadow: 0 4px 20px rgba(220, 38, 38, 0.15);
    }

    /* Button Styling - Modern Teal Design */
    .run-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 12px;
        font-weight: 700;
        border: none;
        padding: 12px 24px;
        color: white;
        font-size: 14px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        box-shadow: 0 4px 12px rgba(15, 118, 110, 0.2);
    }

    .run-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 118, 110, 0.3);
    }

    .btn-play {
        background: var(--primary-gradient);
        color: white;
    }

    .btn-play:hover {
        background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
        color: white;
        filter: brightness(1.05);
    }

    /* Status Badge - Modern Color Palette */
    .status-badge {
        font-size: 12px;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: white;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.2);
    }

    .badge-running {
        background: var(--accent-gradient);
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
    }

    .badge-success {
        background: linear-gradient(135deg, var(--success-color) 0%, #047857 100%);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
    }

    .badge-failed {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    }

    .badge-pending {
        background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    }

    /* Results Section - Yüksek kontrast */
    .results-section {
        max-height: 400px;
        overflow-y: auto;
        border-radius: 8px;
        background: rgba(76, 81, 191, 0.1);
        border: 1px solid rgba(76, 81, 191, 0.1);
    }

    .command-output {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 16px;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 13px;
        line-height: 1.5;
        white-space: pre-wrap;
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        color: #2d3748;
    }

    /* Loading Spinner */
    .loading-spinner {
        display: none;
    }

    .spinner-border {
        width: 1.5rem;
        height: 1.5rem;
        border-width: 2px;
        color: #667eea;
    }

    /* Header Styling - Dark Theme */
    .card-header.bg-gradient-primary {
        background: var(--primary-gradient) !important;
        border-radius: 12px 12px 0 0 !important;
        border: none;
        color: white;
    }

    /* Project Section - Dark Theme */
    .project-header {
        background: rgba(59, 130, 246, 0.15);
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 16px;
        border-left: 4px solid var(--primary-color);
        border: 1px solid rgba(59, 130, 246, 0.2);
    }

    .project-title {
        color: var(--text-primary) !important;
        font-weight: 700;
        margin: 0;
    }

    /* Empty State - Dark Theme */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.85) 100%);
        border-radius: 12px;
        color: var(--text-primary) !important;
        border: 1px solid rgba(59, 130, 246, 0.2);
        backdrop-filter: blur(10px);
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 16px;
    }

    /* Multi-select styling - Dark Theme */
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
    }

    /* Test summary styling */
    .test-summary {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .test-summary .badge {
        font-size: 11px;
        padding: 3px 6px;
    }

    /* Table styling for results */
    .table-results {
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    .table-results thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .table-results thead th {
        color: white;
        font-weight: 600;
        border: none;
        padding: 12px 16px;
    }

    .table-results tbody td {
        padding: 12px 16px;
        border-color: #e2e8f0;
        color: #2d3748;
    }

    .table-results tbody tr:hover {
        background-color: #f7fafc;
    }

    /* Action buttons in cards */
    .card-actions {
        padding: 12px 16px;
        background: #f8f9fa;
        border-top: 1px solid #e2e8f0;
        border-radius: 0 0 12px 12px;
    }

    /* File info styling - İyileştirilmiş kontrast */
    .file-info {
        font-size: 12px;
        color: #718096;
        margin-top: 4px;
    }

    .file-size {
        color: #4a5568;
        font-weight: 500;
    }

    .file-modified {
        color: #718096;
    }

    /* Test file name - İyileştirilmiş kontrast */
    .test-file-name {
        color: #1a202c;
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* Card body text */
    .card-body {
        color: #2d3748;
    }

    .card-title {
        color: #1a202c;
        font-weight: 600;
    }

    .card-text {
        color: #4a5568;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .test-card {
            margin-bottom: 12px;
        }
        
        .command-output {
            font-size: 12px;
            padding: 12px;
        }
    }
</style>
@endpush

@section('content')
<div class="container mt-4">

    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-robot me-2"></i>Test Otomasyonu
            </h4>
            <div class="d-flex align-items-center">
                <button class="btn btn-dark me-2" onclick="refreshTestFiles()">
                    <i class="fas fa-sync-alt"></i> Yenile
                </button>
                <button class="btn btn-success" onclick="runAllTests()">
                    <i class="fas fa-play-circle"></i> Tüm Testleri Çalıştır
                </button>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Test Dosyaları -->
            <div class="row">
                <div class="col-md-8">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-file-code text-primary"></i> Playwright Test Dosyaları
                    </h5>
                    
                    @if(count($testFiles) > 0)
                        <!-- Yüklenen Proje Testleri -->
                        @php
                            $uploadedProjectTests = collect($testFiles)->where('project_name', '!=', null)->groupBy('project_name');
                            $localTests = collect($testFiles)->where('project_name', null);
                        @endphp
                        
                        @if($uploadedProjectTests->count() > 0)
                            @foreach($uploadedProjectTests as $projectName => $projectTests)
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fas fa-archive text-info me-2"></i>
                                        Proje: {{ $projectName }} 
                                        <span class="badge bg-info">{{ $projectTests->count() }} test</span>
                                    </h6>
                                    <div class="row">
                                        @foreach($projectTests as $testFile)
                                        <div class="col-md-6 mb-3">
                                            <div class="card test-card h-100" data-test="{{ $testFile['path'] }}">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="card-title mb-0">{{ $testFile['name'] }}</h6>
                                                        <span class="status-badge badge bg-secondary" id="status-{{ $loop->parent->index }}-{{ $loop->index }}">Hazır</span>
                                                    </div>
                                                    <p class="card-text text-muted small">
                                                        <i class="fas fa-folder me-1"></i>{{ $testFile['path'] }}<br>
                                                        <i class="fas fa-weight me-1"></i>{{ round($testFile['size'] / 1024, 1) }} KB
                                                        <i class="fas fa-clock ms-2 me-1"></i>{{ date('d.m.Y H:i', $testFile['modified']) }}
                                                    </p>
                                                    
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <button class="btn btn-primary btn-sm run-btn" 
                                                                onclick="runSingleTest('{{ addslashes($testFile['full_path']) }}', '{{ addslashes($testFile['name']) }}', '{{ $loop->parent->index }}-{{ $loop->index }}')"
                                                                data-test-path="{{ $testFile['full_path'] }}"
                                                                data-test-name="{{ $testFile['name'] }}"
                                                                data-test-index="{{ $loop->parent->index }}-{{ $loop->index }}">
                                                            <i class="fas fa-play"></i> Çalıştır
                                                        </button>
                                                        <div class="loading-spinner" id="spinner-{{ $loop->parent->index }}-{{ $loop->index }}" style="display: none;">
                                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                                <span class="visually-hidden">Yükleniyor...</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        
                        <!-- Yerel Test Dosyaları -->
                        @if($localTests->count() > 0)
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">
                                    <i class="fas fa-laptop-code text-primary me-2"></i>
                                    Yerel Test Dosyaları 
                                    <span class="badge bg-primary">{{ $localTests->count() }} test</span>
                                </h6>
                                <div class="row" id="testFilesContainer">
                                    @foreach($localTests as $testFile)
                                    <div class="col-md-6 mb-3">
                                        <div class="card test-card h-100" data-test="{{ $testFile['path'] }}">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0">{{ $testFile['name'] }}</h6>
                                                    <span class="status-badge badge bg-secondary" id="status-local-{{ $loop->index }}">Hazır</span>
                                                </div>
                                                <p class="card-text text-muted small">
                                                    <i class="fas fa-folder me-1"></i>{{ $testFile['path'] }}<br>
                                                    <i class="fas fa-weight me-1"></i>{{ round($testFile['size'] / 1024, 1) }} KB
                                                    <i class="fas fa-clock ms-2 me-1"></i>{{ date('d.m.Y H:i', $testFile['modified']) }}
                                                </p>
                                                
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <button class="btn btn-primary btn-sm run-btn" 
                                                            onclick="runSingleTest('{{ addslashes($testFile['full_path']) }}', '{{ addslashes($testFile['name']) }}', 'local-{{ $loop->index }}')"
                                                            data-test-path="{{ $testFile['full_path'] }}"
                                                            data-test-name="{{ $testFile['name'] }}"
                                                            data-test-index="local-{{ $loop->index }}">
                                                        <i class="fas fa-play"></i> Çalıştır
                                                    </button>
                                                    <div class="loading-spinner" id="spinner-local-{{ $loop->index }}" style="display: none;">
                                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                            <span class="visually-hidden">Yükleniyor...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            Henüz Playwright test dosyası bulunamadı. 
                            <br><small>Test dosyalarınızı şu dizinlere ekleyin:</small>
                            <ul class="mt-2 mb-0">
                                <li><code>tests/e2e/</code></li>
                                <li><code>tests/playwright/</code></li>
                                <li><code>playwright/tests/</code></li>
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Test Sonuçları -->
                <div class="col-md-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-bar text-success"></i> Son Test Sonuçları
                    </h5>
                    
                    <div class="results-section" id="resultsSection">
                        @if($lastResults && count($lastResults) > 0)
                            @foreach($lastResults as $result)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            {{ $result['test_file'] ?? $result['test_name'] ?? 'Test' }}
                                            @if($result['success'] ?? false)
                                                <span class="badge bg-success">Başarılı</span>
                                            @else
                                                <span class="badge bg-danger">Başarısız</span>
                                            @endif
                                        </h6>
                                        <p class="text-muted small">{{ $result['timestamp'] ?? 'Bilinmeyen zaman' }}</p>
                                        @if(!empty($result['output']))
                                            <button class="btn btn-sm btn-outline-secondary" onclick="toggleOutput(this)">
                                                Çıktıyı Göster/Gizle
                                            </button>
                                            <pre class="mt-2 command-output" style="display: none;">{{ $result['output'] }}</pre>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Henüz test sonucu yok.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Test Çıktısı -->
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-terminal text-dark"></i> Test Çıktısı
                    </h5>
                    <div class="command-output" id="commandOutput">
                        Test çalıştırıldığında çıktı burada görünecek...
                    </div>
                </div>
            </div>
            
            <!-- Test Geçmişi -->
            @if(count($lastResults) > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-history text-dark"></i> Son Test Sonuçları
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-results">
                            <thead>
                                <tr>
                                    <th>Test Adı</th>
                                    <th>Tarih/Saat</th>
                                    <th>Durum</th>
                                    <th>Sonuç</th>
                                    <th>Süre</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lastResults as $index => $result)
                                <tr>
                                    <td>
                                        <div class="test-file-name">{{ $result['test_name'] }}</div>
                                        @if(isset($result['test_file']))
                                            <div class="file-info">{{ $result['test_file'] }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $result['timestamp'] }}</small>
                                    </td>
                                    <td>
                                        @if($result['success'])
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Başarılı
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times"></i> Başarısız
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($result['test_results']) && $result['test_results']['total'] > 0)
                                            <div class="test-summary">
                                                <span class="badge bg-primary">{{ $result['test_results']['total'] }}</span>
                                                @if($result['test_results']['passed'] > 0)
                                                    <span class="badge bg-success">✓ {{ $result['test_results']['passed'] }}</span>
                                                @endif
                                                @if($result['test_results']['failed'] > 0)
                                                    <span class="badge bg-danger">✗ {{ $result['test_results']['failed'] }}</span>
                                                @endif
                                                @if($result['test_results']['skipped'] > 0)
                                                    <span class="badge bg-warning">⏭ {{ $result['test_results']['skipped'] }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            @if(isset($result['test_results']['duration']))
                                                {{ $result['test_results']['duration'] }}
                                            @else
                                                {{ $result['duration'] }}ms
                                            @endif
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function runSingleTest(testPath, testName, index) {
    console.log('Test çalıştırılıyor:', testName);
    
    // Elementleri bul
    const statusBadge = document.getElementById(`status-${index}`);
    const spinner = document.getElementById(`spinner-${index}`);
    const outputElement = document.getElementById('commandOutput');
    
    // UI güncelle
    if (statusBadge) {
        statusBadge.textContent = 'Çalışıyor...';
        statusBadge.className = 'status-badge badge bg-warning';
    }
    if (spinner) spinner.style.display = 'block';
    if (outputElement) outputElement.textContent = `${testName} testi başlatılıyor...`;
    
    // CSRF token al
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Test çalıştır
    fetch('/test-automation/run-test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            test_path: testPath,
            test_name: testName
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const result = data.result;
            
            if (statusBadge) {
                if (result.success) {
                    statusBadge.textContent = 'Başarılı';
                    statusBadge.className = 'status-badge badge bg-success';
                } else {
                    statusBadge.textContent = 'Başarısız';
                    statusBadge.className = 'status-badge badge bg-danger';
                }
            }
            
            if (outputElement) {
                let output = '';
                
                // Test özeti
                if (result.test_results && result.summary) {
                    output += `📊 Test Özeti: ${result.summary}\n\n`;
                    
                    if (result.test_results.total > 0) {
                        output += `📈 Detaylar:\n`;
                        output += `   • Toplam: ${result.test_results.total} test\n`;
                        output += `   • ✅ Başarılı: ${result.test_results.passed}\n`;
                        output += `   • ❌ Başarısız: ${result.test_results.failed}\n`;
                        if (result.test_results.skipped > 0) {
                            output += `   • ⏭️ Atlanan: ${result.test_results.skipped}\n`;
                        }
                        if (result.test_results.duration) {
                            output += `   • ⏱️ Süre: ${result.test_results.duration}\n`;
                        }
                        output += '\n';
                    }
                }
                
                // Komut bilgileri
                output += `🔧 Komut: ${result.command}\n`;
                output += `📁 Çalışma Dizini: ${result.working_directory}\n`;
                output += `⏱️ Toplam Süre: ${result.duration}ms\n`;
                output += `📄 Test Dosyası: ${result.test_file || 'N/A'}\n\n`;
                
                // Test çıktısı
                output += `📝 Playwright Çıktısı:\n`;
                output += `${result.output || 'Çıktı yok'}`;
                
                // Hata çıktısı varsa
                if (result.error_output && result.error_output.trim()) {
                    output += `\n\n❗ Hata Çıktısı:\n${result.error_output}`;
                }
                
                outputElement.textContent = output;
            }
        } else {
            if (statusBadge) {
                statusBadge.textContent = 'Hata';
                statusBadge.className = 'status-badge badge bg-danger';
            }
            if (outputElement) {
                outputElement.textContent = `❌ ${testName}\n\nHata: ${data.message}`;
            }
        }
        
        // Test geçmişini yenile
        setTimeout(() => {
            location.reload();
        }, 2000);
    })
    .catch(error => {
        console.error('Hata:', error);
        if (statusBadge) {
            statusBadge.textContent = 'Hata';
            statusBadge.className = 'status-badge badge bg-danger';
        }
        if (outputElement) {
            outputElement.textContent = `💥 ${testName}\n\nBağlantı hatası: ${error.message}`;
        }
    })
    .finally(() => {
        if (spinner) spinner.style.display = 'none';
    });
}

function refreshTestFiles() {
    location.reload();
}

function runAllTests() {
    alert('Tüm testler çalıştırılacak...');
}
</script>
@endpush
@endsection
