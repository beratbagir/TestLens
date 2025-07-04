@extends('back.layouts.pages-layout')
@section('pageTitle', 'Test Raporları')

@push('stylesheets')
<style>
    /* Container Text Colors Override */
    .container *, .container-fluid * {
        color: var(--text-primary) !important;
    }
    
    .container .text-muted, .container-fluid .text-muted {
        color: var(--text-muted) !important;
    }

    /* Report Card Styling - Dark Theme */
    .report-card {
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.85) 100%);
        backdrop-filter: blur(4px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(59, 130, 246, 0.2);
        transition: all 0.2s ease;
        overflow: hidden;
        position: relative;
        color: var(--text-primary) !important;
    }

    .report-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #cbd5e0 0%, #94a3b8 100%);
        transition: width 0.15s ease;
    }

    .report-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 32px rgba(59, 130, 246, 0.2);
        border-color: rgba(59, 130, 246, 0.3);
    }

    .report-card:hover::before {
        width: 6px;
    }

    .report-card.success::before {
        background: linear-gradient(180deg, #059669 0%, #047857 100%);
    }

    .report-card.success {
        background: linear-gradient(135deg, rgba(5, 150, 105, 0.15) 0%, rgba(16, 185, 129, 0.1) 100%);
        box-shadow: 0 4px 20px rgba(5, 150, 105, 0.15);
        border-color: rgba(5, 150, 105, 0.3);
    }

    .report-card.failed::before {
        background: linear-gradient(180deg, #dc2626 0%, #b91c1c 100%);
    }

    .report-card.failed {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.1) 100%);
        box-shadow: 0 4px 20px rgba(220, 38, 38, 0.15);
        border-color: rgba(220, 38, 38, 0.3);
    }

    .report-card.warning::before {
        background: linear-gradient(180deg, #d97706 0%, #b45309 100%);
    }

    .report-card.warning {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(217, 119, 6, 0.1) 100%);
        box-shadow: 0 4px 20px rgba(217, 119, 6, 0.15);
        border-color: rgba(245, 158, 11, 0.3);
    }

    /* Stats Card - Modern Teal Design */
    .stats-card {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        border-radius: 20px;
        border: none;
        box-shadow: 0 8px 32px rgba(15, 118, 110, 0.25);
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(234, 88, 12, 0.1) 0%, transparent 70%);
        animation: float 12s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(90deg); }
    }

    .stat-item {
        text-align: center;
        padding: 24px 20px;
        position: relative;
        z-index: 2;
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 900;
        margin-bottom: 8px;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .stat-label {
        font-size: 0.95rem;
        opacity: 0.95;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Result Badge - Modern Color Palette */
    .result-badge {
        font-size: 12px;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.2);
    }

    .badge-success {
        background: linear-gradient(135deg, var(--success-color) 0%, #047857 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
    }

    .badge-danger {
        background: linear-gradient(135deg, var(--danger-color) 0%, #b91c1c 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .badge-warning {
        background: linear-gradient(135deg, var(--warning-color) 0%, #c2410c 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
    }

    .badge-info {
        background: linear-gradient(135deg, var(--info-color) 0%, #0e7490 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3);
    }

    /* Command Output - Dark Theme */
    .command-output {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.8) 100%);
        border-radius: 8px;
        padding: 20px;
        font-family: 'JetBrains Mono', 'Fira Code', 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.7;
        white-space: pre-wrap;
        max-height: 250px;
        overflow-y: auto;
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: var(--text-primary) !important;
        backdrop-filter: blur(4px);
    }

    /* Filter Tabs - Dark Theme */
    .filter-tabs {
        border-bottom: 2px solid rgba(59, 130, 246, 0.2);
        margin-bottom: 32px;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(51, 65, 85, 0.7) 100%);
        border-radius: 16px 16px 0 0;
        padding: 12px 16px 0 16px;
        backdrop-filter: blur(8px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(59, 130, 246, 0.1);
    }

    .filter-tab {
        background: none;
        border: none;
        padding: 16px 24px;
        margin-right: 8px;
        cursor: pointer;
        border-radius: 12px 12px 0 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 600;
        color: var(--text-muted);
        position: relative;
        overflow: hidden;
    }

    .filter-tab::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 3px;
        background: var(--accent-gradient);
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .filter-tab:hover {
        background: rgba(59, 130, 246, 0.1);
        color: var(--primary-color);
        transform: translateY(-2px);
    }

    .filter-tab.active {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(245, 158, 11, 0.1) 100%);
        color: var(--accent-color);
        font-weight: 700;
    }

    .filter-tab.active::before {
        width: 100%;
    }

    /* Table Styling - Dark Theme */
    .table {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.85) 100%);
        border-radius: 12px;
        overflow: hidden;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: var(--text-primary) !important;
    }

    .table thead {
        background: var(--primary-gradient);
    }

    .table thead th {
        color: white;
        font-weight: 600;
        border: none;
        padding: 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 12px;
    }

    .table tbody td {
        padding: 16px;
        border-color: rgba(59, 130, 246, 0.2);
        vertical-align: middle;
        color: var(--text-primary) !important;
    }

    .table tbody tr:hover {
        background: rgba(59, 130, 246, 0.1);
    }

    /* Action Buttons - Modern Design */
    .btn-view {
        background: var(--primary-gradient);
        border: none;
        border-radius: 12px;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        color: white;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(15, 118, 110, 0.2);
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 118, 110, 0.3);
        color: white;
    }

    .btn-delete {
        background: linear-gradient(135deg, var(--danger-color) 0%, #b91c1c 100%);
        border: none;
        border-radius: 12px;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        color: white;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2);
    }

    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
        color: white;
    }

    /* Empty State - Dark Theme */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.85) 100%);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: var(--text-primary) !important;
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--text-muted);
        margin-bottom: 20px;
    }

    .empty-state h5 {
        color: var(--text-primary) !important;
        margin-bottom: 12px;
    }

    .empty-state p {
        color: var(--text-secondary) !important;
        margin-bottom: 0;
    }

    /* Loading States - Dark Theme */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        backdrop-filter: blur(5px);
        z-index: 10;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 4px;
        color: var(--primary-color);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .stat-number {
            font-size: 2rem;
        }
        
        .filter-tab {
            padding: 8px 16px;
            font-size: 14px;
        }
        
        .report-card {
            margin-bottom: 16px;
        }
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    
    <div class="row">
        <div class="col-12 mb-4">
            <h2 class="fw-bold">
                <i class="fas fa-chart-line text-primary me-2"></i>
                Test Raporları
            </h2>
            <p class="text-muted">Test otomasyonu ve regresyon test sonuçlarını görüntüleyin</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- İstatistikler -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-item">
                                <div class="stat-number">{{ $statistics['total_tests'] ?? 0 }}</div>
                                <div class="stat-label">Toplam Test</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <div class="stat-number">{{ $statistics['passed_tests'] ?? 0 }}</div>
                                <div class="stat-label">Başarılı Test</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <div class="stat-number">{{ $statistics['failed_tests'] ?? 0 }}</div>
                                <div class="stat-label">Başarısız Test</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <div class="stat-number">{{ $statistics['success_rate'] ?? 0 }}%</div>
                                <div class="stat-label">Başarı Oranı</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtreler -->
    <div class="filter-tabs">
        <button class="filter-tab active" onclick="filterResults('all')">
            <i class="fas fa-list me-1"></i> Tümü
        </button>
        <button class="filter-tab" onclick="filterResults('automation')">
            <i class="fas fa-robot me-1"></i> Test Otomasyonu
        </button>
        <button class="filter-tab" onclick="filterResults('regression')">
            <i class="fas fa-cog me-1"></i> Regresyon Testleri
        </button>
        <button class="filter-tab" onclick="filterResults('success')">
            <i class="fas fa-check me-1"></i> Başarılı
        </button>
        <button class="filter-tab" onclick="filterResults('failed')">
            <i class="fas fa-times me-1"></i> Başarısız
        </button>
    </div>

    <div class="row">
        <!-- Test Otomasyonu Sonuçları -->
        <div class="col-md-8">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-robot text-primary me-2"></i>
                Test Otomasyonu Sonuçları
            </h5>
            
            <div id="automationResults">
                @if(count($automationResults) > 0)
                    @foreach($automationResults as $result)
                        <div class="card report-card mb-3 result-item automation {{ ($result['success'] ?? false) ? 'success' : 'failed' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="card-title mb-1">
                                            <i class="fas fa-file-code text-primary me-2"></i>
                                            {{ $result['test_name'] ?? 'Test' }}
                                        </h6>
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $result['timestamp'] ?? 'Bilinmeyen tarih' }}
                                        </p>
                                        @if(isset($result['test_file']))
                                            <p class="text-muted small mb-0">
                                                <i class="fas fa-file me-1"></i>
                                                {{ $result['test_file'] }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        @if($result['success'] ?? false)
                                            <span class="badge bg-success result-badge">
                                                <i class="fas fa-check me-1"></i> Başarılı
                                            </span>
                                        @else
                                            <span class="badge bg-danger result-badge">
                                                <i class="fas fa-times me-1"></i> Başarısız
                                            </span>
                                        @endif
                                        
                                        <!-- Test Sonuçları İstatistikleri -->
                                        @if(isset($result['test_results']) && $result['test_results']['total'] > 0)
                                            <div class="mt-2">
                                                <span class="badge bg-primary me-1">
                                                    <i class="fas fa-list me-1"></i>{{ $result['test_results']['total'] }}
                                                </span>
                                                @if($result['test_results']['passed'] > 0)
                                                    <span class="badge bg-success me-1">
                                                        <i class="fas fa-check me-1"></i>{{ $result['test_results']['passed'] }}
                                                    </span>
                                                @endif
                                                @if($result['test_results']['failed'] > 0)
                                                    <span class="badge bg-danger me-1">
                                                        <i class="fas fa-times me-1"></i>{{ $result['test_results']['failed'] }}
                                                    </span>
                                                @endif
                                                @if($result['test_results']['skipped'] > 0)
                                                    <span class="badge bg-warning me-1">
                                                        <i class="fas fa-forward me-1"></i>{{ $result['test_results']['skipped'] }}
                                                    </span>
                                                @endif
                                                @if(isset($result['test_results']['duration']))
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-clock me-1"></i>{{ $result['test_results']['duration'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <div class="btn-group mt-2">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="toggleOutput('automation-{{ $loop->index }}')">
                                                <i class="fas fa-eye"></i> Çıktı
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                @if(!empty($result['output']))
                                    <div class="collapse" id="automation-{{ $loop->index }}">
                                        <div class="command-output">{{ $result['output'] }}</div>
                                    </div>
                                @endif
                                
                                @if(!empty($result['error_output']))
                                    <div class="mt-2">
                                        <strong class="text-danger">Hata Çıktısı:</strong>
                                        <div class="command-output bg-danger-subtle">{{ $result['error_output'] }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Henüz test otomasyonu sonucu bulunmuyor.
                    </div>
                @endif
            </div>
        </div>

        <!-- Regresyon Test Sonuçları -->
        <div class="col-md-4">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-cog text-success me-2"></i>
                Regresyon Test Özeti
            </h5>
            
            <div id="regressionResults">
                @if(count($regressionResults) > 0)
                    @php
                        $regressionStats = [
                            'pass' => count(array_filter($regressionResults, fn($r) => $r['result'] == 'pass')),
                            'fail' => count(array_filter($regressionResults, fn($r) => $r['result'] == 'fail')),
                            'skip' => count(array_filter($regressionResults, fn($r) => $r['result'] == 'skip')),
                        ];
                    @endphp
                    
                    <div class="card report-card mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Regresyon İstatistikleri</h6>
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="stat-number text-success">{{ $regressionStats['pass'] }}</div>
                                    <div class="stat-label">Başarılı</div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-number text-danger">{{ $regressionStats['fail'] }}</div>
                                    <div class="stat-label">Başarısız</div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-number text-warning">{{ $regressionStats['skip'] }}</div>
                                    <div class="stat-label">Atlandı</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card report-card">
                        <div class="card-body">
                            <h6 class="card-title">Son Regresyon Testleri</h6>
                            @foreach(array_slice($regressionResults, 0, 10) as $result)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small>Senaryo #{{ $result['scenario_id'] }}</small>
                                    @if($result['result'] == 'pass')
                                        <span class="badge bg-success">✓</span>
                                    @elseif($result['result'] == 'fail')
                                        <span class="badge bg-danger">✗</span>
                                    @else
                                        <span class="badge bg-warning">⊝</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Henüz regresyon test sonucu bulunmuyor.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleOutput(targetId) {
    const element = document.getElementById(targetId);
    if (element.classList.contains('show')) {
        element.classList.remove('show');
    } else {
        element.classList.add('show');
    }
}

function filterResults(type) {
    // Tab aktifliğini güncelle
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Sonuçları filtrele
    document.querySelectorAll('.result-item').forEach(item => {
        let show = false;
        
        switch(type) {
            case 'all':
                show = true;
                break;
            case 'automation':
                show = item.classList.contains('automation');
                break;
            case 'regression':
                show = item.classList.contains('regression');
                break;
            case 'success':
                show = item.classList.contains('success');
                break;
            case 'failed':
                show = item.classList.contains('failed');
                break;
        }
        
        item.style.display = show ? 'block' : 'none';
    });
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // İlk filtreyi aktif yap
    filterResults('all');
});
</script>
@endsection
