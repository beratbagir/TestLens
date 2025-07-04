@extends('back.layouts.pages-layout')

@section('pageTitle', 'Kendi Senaryolarım')

@push('stylesheets')
<style>
    /* Container Text Colors Override */
    .container *, .container-fluid * {
        color: var(--text-primary) !important;
    }
    
    .container .text-muted, .container-fluid .text-muted {
        color: var(--text-muted) !important;
    }

    .bg-gradient-primary {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 8px 32px rgba(15, 118, 110, 0.25);
    }
    
    .scenario-table {
        table-layout: fixed;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.85) 100%);
        border-radius: 16px;
        overflow: hidden;
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: var(--text-primary) !important;
    }
    .scenario-table th {
        background: var(--primary-gradient);
        font-weight: 700;
        text-align: center;
        padding: 16px 12px;
        border: none;
        color: white;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .scenario-table td {
        vertical-align: middle;
        padding: 12px;
        border: 1px solid rgba(59, 130, 246, 0.2);
        word-wrap: break-word;
        background-color: transparent;
        color: var(--text-primary) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: var(--text-primary);
    }
    .scenario-table tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.1);
        transform: translateX(2px);
    }
    
    /* Kolon genişlikleri - optimize edilmiş oranlar */
    .col-number { width: 4%; }
    .col-title { width: 15%; }
    .col-description { width: 35%; }
    .col-screenshots { width: 12%; }
    .col-checkboxes { width: 22%; }
    .col-actions { width: 12%; }
    
    /* Checkbox sütunu için ekstra stil */
    .col-checkbox { width: 3%; }
    
    .thumb-img {
        width: 60px;
        height: 40px;
        object-fit: cover;
        margin: 2px;
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    .thumb-img:hover {
        transform: scale(1.15);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        border-color: var(--primary-color);
    }
    
    .toggle-badge {
        width: 14px;
        height: 14px;
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 3px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        margin: 1px;
        cursor: pointer;
        font-size: 10px;
        background-color: rgba(30, 41, 59, 0.5);
        transition: all 0.2s ease;
        line-height: 1;
        font-weight: bold;
        color: var(--text-primary) !important;
    }
    .toggle-badge:hover {
        transform: scale(1.25);
        box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
        z-index: 10;
        position: relative;
    }
    .toggle-badge.success {
        background: linear-gradient(135deg, var(--success-color) 0%, #047857 100%);
        color: #fff;
        border-color: var(--success-color);
    }
    .toggle-badge.danger {
        background: linear-gradient(135deg, var(--danger-color) 0%, #b91c1c 100%);
        color: #fff;
        border-color: var(--danger-color);
    }
    .toggle-badge.warning {
        background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
        color: #fff;
        border-color: var(--warning-color);
    }
    
    .checkboxes-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1px;
        max-width: 100%;
        padding: 4px;
    }
    
    .suit-header {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(16, 185, 129, 0.1) 100%);
        padding: 18px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 10px;
        cursor: pointer;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: var(--text-primary) !important;
    }
    .suit-header:hover {
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.2);
        transform: translateY(-2px);
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(16, 185, 129, 0.15) 100%);
        border-color: rgba(59, 130, 246, 0.4);
    }
    .suit-header strong {
        font-size: 16px;
        color: var(--text-primary) !important;
    }
    
    .suit-body {
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 10px;
        margin-bottom: 25px;
        display: none;
        padding: 20px;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(51, 65, 85, 0.7) 100%);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        color: var(--text-primary) !important;
    }
    
    .description-content {
        font-size: 13px;
        line-height: 1.5;
        color: var(--text-secondary) !important;
    }
    .description-content strong {
        color: var(--text-primary) !important;
        font-weight: 600;
    }
    .description-content ol {
        margin: 8px 0 0 16px;
        padding: 0;
    }
    .description-content li {
        margin-bottom: 4px;
        font-size: 12px;
        color: var(--text-muted) !important;
    }
    
    .btn-group-actions {
        display: flex;
        flex-direction: column;
        gap: 4px;
        align-items: center;
    }
    .btn-group-actions .btn {
        padding: 6px 10px;
        font-size: 11px;
        border-radius: 6px;
        min-width: 70px;
        transition: all 0.2s ease;
        font-weight: 500;
    }
    .btn-group-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.2);
    }
    
    /* Card genel stiller */
    .card {
        border-radius: 12px;
        overflow: hidden;
    }
    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }
    .card-body {
        padding: 24px;
    }
    
    /* Alert stiller */
    .alert {
        border-radius: 8px;
        border: none;
        margin-bottom: 20px;
    }
    
    /* Responsive düzenlemeler */
    @media (max-width: 1200px) {
        .col-description { width: 30%; }
        .col-checkboxes { width: 25%; }
        .col-actions { width: 15%; }
    }
    
    @media (max-width: 992px) {
        .toggle-badge {
            width: 12px;
            height: 12px;
            font-size: 9px;
            margin: 0.5px;
        }
        .thumb-img {
            width: 50px;
            height: 35px;
        }
        .description-content {
            font-size: 12px;
        }
        .checkboxes-container {
            gap: 0.5px;
            padding: 2px;
        }
    }
    
    @media (max-width: 768px) {
        .scenario-table {
            font-size: 11px;
        }
        .toggle-badge {
            width: 10px;
            height: 10px;
            font-size: 8px;
        }
        .checkboxes-container {
            gap: 0;
        }
        .btn-group-actions .btn {
            font-size: 10px;
            padding: 4px 6px;
            min-width: 60px;
        }
        .card-body {
            padding: 16px;
        }
        .suit-header {
            padding: 12px;
        }
    }

    /* Suit Dropdown Dark Theme */
    #suitDropdown {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(51, 65, 85, 0.9) 100%) !important;
        border: 1px solid rgba(59, 130, 246, 0.2) !important;
        border-radius: 12px !important;
        backdrop-filter: blur(12px) !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
        color: var(--text-primary) !important;
    }

    #suitDropdown h6 {
        color: var(--text-primary) !important;
    }

    #suitDropdown .form-control {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(51, 65, 85, 0.7) 100%) !important;
        border: 2px solid rgba(59, 130, 246, 0.2) !important;
        color: var(--text-primary) !important;
    }

    #suitDropdown .form-control:focus {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.8) 100%) !important;
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
        color: var(--text-primary) !important;
    }

    #suitDropdown .form-control::placeholder {
        color: var(--text-muted) !important;
    }

    /* Modal Dark Theme Overrides */
    .modal-content {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(51, 65, 85, 0.9) 100%) !important;
        border: 1px solid rgba(59, 130, 246, 0.2) !important;
        border-radius: 16px !important;
        backdrop-filter: blur(12px) !important;
        color: var(--text-primary) !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4) !important;
    }

    .modal-header {
        background: var(--primary-gradient) !important;
        border-bottom: 1px solid rgba(59, 130, 246, 0.2) !important;
        color: white !important;
        border-radius: 16px 16px 0 0 !important;
    }

    .modal-title {
        color: white !important;
        font-weight: 700 !important;
    }

    .modal-body {
        color: var(--text-primary) !important;
    }

    .modal-body .form-label {
        color: var(--text-primary) !important;
        font-weight: 600 !important;
    }

    .modal-body .form-control {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(51, 65, 85, 0.7) 100%) !important;
        border: 2px solid rgba(59, 130, 246, 0.2) !important;
        color: var(--text-primary) !important;
        border-radius: 8px !important;
    }

    .modal-body .form-control:focus {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.8) 100%) !important;
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
        color: var(--text-primary) !important;
    }

    .modal-body .form-control::placeholder {
        color: var(--text-muted) !important;
    }

    .modal-footer {
        border-top: 1px solid rgba(59, 130, 246, 0.2) !important;
        background: rgba(30, 41, 59, 0.3) !important;
        border-radius: 0 0 16px 16px !important;
    }

    .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%) !important;
    }
</style>
@endpush

@section('content')
<div class="container mt-4">

    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-file-alt me-2"></i>Kendi Senaryolarım
            </h4>

            <div class="d-flex align-items-center">
                <div id="suitActions" class="me-2" style="display:none; position:relative;">
                    <button class="btn btn-success" id="suitButton">
                        <i class="fas fa-folder-plus"></i> Test Suit Oluştur
                    </button>
                    <div id="suitDropdown" class="card p-3 shadow-sm" style="display:none; position:absolute; right:0; top:110%; z-index:10;">
                        <h6 class="fw-bold mb-2">Yeni Test Suit</h6>
                        <input type="text" id="suitName" class="form-control mb-2" placeholder="Suit adı">
                        <button class="btn btn-primary btn-sm w-100" id="confirmSuit">Oluştur</button>
                    </div>
                </div>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createScenarioModal">
                    <i class="fas fa-plus"></i> Yeni Senaryo
                </button>
                
                <a href="{{ route('regression.index') }}" class="btn btn-success ms-2">
                    <i class="fas fa-play"></i> Regresyon Koş
                </a>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <h5 class="fw-bold mb-3">Test Suitler:</h5>
            @if(count($suits) > 0)
                @foreach($suits as $suit)
                <div class="suit-container">
                    <div class="suit-header">
                        <div>
                            <strong>{{ $suit->name }}</strong> - ({{ count($suit->scenario_ids) }} senaryo)
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editSuit{{ $suit->id }}">
                                <i class="fas fa-edit"></i> Düzenle
                            </button>
                            <form action="{{ route('suits.destroy', $suit->id) }}" method="POST" style="display:inline;" class="me-2">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" type="submit" onclick="return confirm('Bu test suiti silmek istediğinizden emin misiniz?')">
                                    <i class="fas fa-trash"></i> Sil
                                </button>
                            </form>
                            <button class="btn btn-dark btn-sm toggleSuitBtn">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="suit-body">
                        @if(isset($scenarios) && count($scenarios) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered scenario-table align-middle text-center">
                                <thead>
                                    <tr>
                                        <th class="col-number">#</th>
                                        <th class="col-title">Başlık</th>
                                        <th class="col-description">Açıklama ve Adımlar</th>
                                        <th class="col-screenshots">Görseller</th>
                                        <th class="col-checkboxes">Test Sonuçları (1-20)</th>
                                        <th class="col-actions">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($scenarios->whereIn('id', $suit->scenario_ids) as $scenario)
                                    <tr>
                                        <td class="col-number fw-bold text-primary">{{ $loop->iteration }}</td>
                                        <td class="col-title fw-bold text-start">{{ $scenario->title }}</td>
                                        <td class="col-description text-start">
                                            <div class="description-content">
                                                <strong>Açıklama:</strong> {{ Str::limit($scenario->description, 80) }}
                                                @if($scenario->steps && count($scenario->steps) > 0)
                                                    <div class="mt-2">
                                                        <strong>Adımlar:</strong>
                                                        <ol>
                                                            @foreach(array_slice($scenario->steps, 0, 3) as $step)
                                                                <li>{{ Str::limit($step, 60) }}</li>
                                                            @endforeach
                                                            @if(count($scenario->steps) > 3)
                                                                <li class="text-muted">+{{ count($scenario->steps) - 3 }} adım daha...</li>
                                                            @endif
                                                        </ol>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="col-screenshots text-center">
                                            @if($scenario->screenshots && count($scenario->screenshots) > 0)
                                                @foreach(array_slice($scenario->screenshots, 0, 2) as $img)
                                                    <img src="{{ asset('storage/' . $img) }}" class="thumb-img" onclick="window.open(this.src, '_blank')">
                                                @endforeach
                                                @if(count($scenario->screenshots) > 2)
                                                    <div class="text-muted small">+{{ count($scenario->screenshots) - 2 }} görsel</div>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="col-checkboxes">
                                            <div class="checkboxes-container">
                                                @for($i = 1; $i <= 20; $i++)
                                                    @php
                                                        $regressionResults = session('regression_results', []);
                                                        $scenarioData = $regressionResults[$scenario->id] ?? [];
                                                        $scenarioResults = is_array($scenarioData) ? $scenarioData : [];
                                                        $badgeClass = '';
                                                        $badgeText = '';
                                                        $badgeTitle = 'Test yapılmadı (Pozisyon ' . $i . ')';
                                                        
                                                        if (!empty($scenarioResults)) {
                                                            foreach($scenarioResults as $result) {
                                                                if(isset($result['position']) && $result['position'] == $i) {
                                                                    if($result['result'] === 'pass') {
                                                                        $badgeClass = 'success';
                                                                        $badgeText = '✓';
                                                                        $badgeTitle = 'Test Başarılı - Pos:' . $i . ' (' . ($result['date'] ?? 'Bilinmeyen tarih') . ')';
                                                                    } elseif($result['result'] === 'fail') {
                                                                        $badgeClass = 'danger';
                                                                        $badgeText = '✗';
                                                                        $badgeTitle = 'Test Başarısız - Pos:' . $i . ' (' . ($result['date'] ?? 'Bilinmeyen tarih') . ')';
                                                                    } elseif($result['result'] === 'skip') {
                                                                        $badgeClass = 'warning';
                                                                        $badgeText = '⊝';
                                                                        $badgeTitle = 'Test Atlandı - Pos:' . $i . ' (' . ($result['date'] ?? 'Bilinmeyen tarih') . ')';
                                                                    }
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <span class="toggle-badge {{ $badgeClass }}" title="{{ $badgeTitle }}" 
                                                          data-scenario="{{ $scenario->id }}" data-position="{{ $i }}" 
                                                          onclick="toggleBadgeStatus({{ $scenario->id }}, {{ $i }})">{{ $badgeText }}</span>
                                                @endfor
                                            </div>
                                        </td>
                                        <td class="col-actions">
                                            <div class="btn-group-actions">
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editScenario{{ $scenario->id }}" title="Senaryoyu düzenle">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-info btn-sm" onclick="resetScenarioResults({{ $scenario->id }})" title="Tüm sonuçları sıfırla">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                                <button class="btn btn-secondary btn-sm" onclick="removeFromSuit({{ $scenario->id }}, {{ $suit->id }})" title="Suit'ten çıkar">
                                                    <i class="fas fa-minus-circle"></i>
                                                </button>
                                                <form action="{{ route('scenarios.destroy', $scenario->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm" type="submit" onclick="return confirm('Bu senaryoyu tamamen silmek istediğinizden emin misiniz?')" title="Senaryoyu sil">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Düzenleme Modalı -->
                                    <div class="modal fade" id="editScenario{{ $scenario->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="{{ route('scenarios.update', $scenario->id) }}" method="POST" class="modal-content">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Senaryo Düzenle</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label>Başlık</label>
                                                    <input type="text" name="title" class="form-control mb-3" value="{{ $scenario->title }}" required>
                                                    <label>Açıklama</label>
                                                    <textarea name="description" class="form-control" rows="3" required>{{ $scenario->description }}</textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                    <button class="btn btn-primary" type="submit">Kaydet</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div class="alert alert-info">Bu suitte senaryo yok.</div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Henüz test suit'i oluşturulmamış.
                </div>
            @endif

            @php
                // Suit'lerde bulunan tüm senaryo ID'lerini topla
                $suitScenarioIds = [];
                foreach($suits as $suit) {
                    $suitScenarioIds = array_merge($suitScenarioIds, $suit->scenario_ids);
                }
                $suitScenarioIds = array_unique($suitScenarioIds);
                
                // Suit dışı senaryoları filtrele
                $unassignedScenarios = $scenarios->whereNotIn('id', $suitScenarioIds);
            @endphp

            @if(count($unassignedScenarios) > 0)
            <h5 class="fw-bold mb-3 mt-4">Suit Dışı Senaryolar:</h5>
            <div class="table-responsive">
                <table class="table table-bordered scenario-table align-middle text-center">
                    <thead>
                        <tr>
                            <th class="col-checkbox">
                                <input type="checkbox" id="selectAll" title="Tümünü seç">
                            </th>
                            <th class="col-number">#</th>
                            <th class="col-title">Başlık</th>
                            <th class="col-description">Açıklama ve Adımlar</th>
                            <th class="col-screenshots">Görseller</th>
                            <th class="col-checkboxes">Test Sonuçları (1-20)</th>
                            <th class="col-actions">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unassignedScenarios as $scenario)
                        <tr>
                            <td class="col-checkbox">
                                <input type="checkbox" class="scenario-checkbox" value="{{ $scenario->id }}">
                            </td>
                            <td class="col-number fw-bold text-primary">{{ $loop->iteration }}</td>
                            <td class="col-title fw-bold text-start">{{ $scenario->title }}</td>
                            <td class="col-description text-start">
                                <div class="description-content">
                                    <strong>Açıklama:</strong> {{ Str::limit($scenario->description, 80) }}
                                    @if($scenario->steps && count($scenario->steps) > 0)
                                        <div class="mt-2">
                                            <strong>Adımlar:</strong>
                                            <ol>
                                                @foreach(array_slice($scenario->steps, 0, 3) as $step)
                                                    <li>{{ Str::limit($step, 60) }}</li>
                                                @endforeach
                                                @if(count($scenario->steps) > 3)
                                                    <li class="text-muted">+{{ count($scenario->steps) - 3 }} adım daha...</li>
                                                @endif
                                            </ol>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="col-screenshots text-center">
                                @if($scenario->screenshots && count($scenario->screenshots) > 0)
                                    @foreach(array_slice($scenario->screenshots, 0, 2) as $img)
                                        <img src="{{ asset('storage/' . $img) }}" class="thumb-img" onclick="window.open(this.src, '_blank')">
                                    @endforeach
                                    @if(count($scenario->screenshots) > 2)
                                        <div class="text-muted small">+{{ count($scenario->screenshots) - 2 }} görsel</div>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="col-checkboxes">
                                <div class="checkboxes-container">
                                    @for($i = 1; $i <= 20; $i++)
                                        @php
                                            $badgeClass = '';
                                            $badgeText = '';
                                            $badgeTitle = 'Test yapılmadı (Pozisyon ' . $i . ')';
                                            
                                            $regressionResults = session('regression_results', []);
                                            if (isset($regressionResults[$scenario->id])) {
                                                $scenarioData = $regressionResults[$scenario->id];
                                                if (is_array($scenarioData)) {
                                                    foreach($scenarioData as $result) {
                                                        if (isset($result['position']) && $result['position'] == $i) {
                                                            if ($result['result'] == 'pass') {
                                                                $badgeClass = 'success';
                                                                $badgeText = '✓';
                                                                $badgeTitle = 'Test Başarılı - Pos:' . $i . ' (' . ($result['date'] ?? 'Bilinmeyen tarih') . ')';
                                                            } elseif ($result['result'] == 'fail') {
                                                                $badgeClass = 'danger';
                                                                $badgeText = '✗';
                                                                $badgeTitle = 'Test Başarısız - Pos:' . $i . ' (' . ($result['date'] ?? 'Bilinmeyen tarih') . ')';
                                                            } else {
                                                                $badgeClass = 'warning';
                                                                $badgeText = '⊝';
                                                                $badgeTitle = 'Test Atlandı - Pos:' . $i . ' (' . ($result['date'] ?? 'Bilinmeyen tarih') . ')';
                                                            }
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        <span class="toggle-badge {{ $badgeClass }}" title="{{ $badgeTitle }}" 
                                              data-scenario="{{ $scenario->id }}" data-position="{{ $i }}" 
                                              onclick="toggleBadgeStatus({{ $scenario->id }}, {{ $i }})">{{ $badgeText }}</span>
                                    @endfor
                                </div>
                            </td>
                            <td class="col-actions">
                                <div class="btn-group-actions">
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editScenarioUnassigned{{ $scenario->id }}" title="Senaryoyu düzenle">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-info btn-sm" onclick="resetScenarioResults({{ $scenario->id }})" title="Tüm sonuçları sıfırla">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <form action="{{ route('scenarios.destroy', $scenario->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit" onclick="return confirm('Bu senaryoyu tamamen silmek istediğinizden emin misiniz?')" title="Senaryoyu sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Düzenleme Modalı -->
                        <div class="modal fade" id="editScenarioUnassigned{{ $scenario->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('scenarios.update', $scenario->id) }}" method="POST" class="modal-content">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Senaryo Düzenle</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label>Başlık</label>
                                        <input type="text" name="title" class="form-control mb-3" value="{{ $scenario->title }}" required>
                                        <label>Açıklama</label>
                                        <textarea name="description" class="form-control" rows="3" required>{{ $scenario->description }}</textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                        <button class="btn btn-primary" type="submit">Kaydet</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Test Suit Düzenleme Modalları -->
@foreach($suits as $suit)
<div class="modal fade" id="editSuit{{ $suit->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('suits.update', $suit->id) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Test Suit Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label>Suit Adı</label>
                <input type="text" name="name" class="form-control mb-3" value="{{ $suit->name }}" required>
                
                <label>Senaryolar</label>
                <div class="border p-3 rounded">
                    @if(isset($scenarios) && count($scenarios) > 0)
                        @foreach($scenarios as $scenario)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="scenario_ids[]" 
                                       value="{{ $scenario->id }}" id="scenario{{ $scenario->id }}_suit{{ $suit->id }}"
                                       {{ in_array($scenario->id, $suit->scenario_ids) ? 'checked' : '' }}>
                                <label class="form-check-label" for="scenario{{ $scenario->id }}_suit{{ $suit->id }}">
                                    {{ $scenario->title }}
                                </label>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Henüz senaryo bulunmuyor.</p>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button class="btn btn-primary" type="submit">Güncelle</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Yeni Senaryo Modal -->
<div class="modal fade" id="createScenarioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('scenarios.store') }}" method="POST" class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Yeni Senaryo Oluştur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label>Başlık</label>
                <input type="text" name="title" class="form-control mb-3" value="{{ old('title') }}" required>
                <label>Açıklama</label>
                <textarea name="description" class="form-control mb-3" rows="3" required>{{ old('description') }}</textarea>
                <label>Adımlar</label>
                <div id="step-list">
                    @for($i=1; $i<=3; $i++)
                        <div class="input-group mb-2">
                            <span class="input-group-text">Adım {{ $i }}</span>
                            <input type="text" name="steps[]" class="form-control" placeholder="Adım açıklaması">
                        </div>
                    @endfor
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-step-btn">+ Adım Ekle</button>
                <label>Ekran Görüntüleri</label>
                <input type="file" name="screenshot[]" class="form-control" accept="image/*" multiple>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button class="btn btn-primary" type="submit">Kaydet</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('add-step-btn').addEventListener('click', () => {
        const index = document.querySelectorAll('#step-list input').length + 1;
        const div = document.createElement('div');
        div.className = "input-group mb-2";
        div.innerHTML = `<span class="input-group-text">Adım ${index}</span>
                         <input type="text" name="steps[]" class="form-control" placeholder="Adım açıklaması">`;
        document.getElementById('step-list').appendChild(div);
    });

    document.querySelectorAll('.toggleSuitBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const body = btn.closest('.suit-container').querySelector('.suit-body');
            body.style.display = body.style.display === 'none' || body.style.display === '' ? 'block' : 'none';
            btn.querySelector('i').classList.toggle('fa-chevron-down');
            btn.querySelector('i').classList.toggle('fa-chevron-up');
        });
    });

    document.querySelectorAll('.toggle-badge').forEach(badge => {
        badge.addEventListener('click', () => {
            const choice = confirm("✅ Yeşil tik için OK, ❌ Kırmızı çarpı için Cancel tıklayın.");
            if (choice) {
                badge.classList.remove('danger');
                badge.classList.add('success');
                badge.textContent = '✔';
            } else {
                badge.classList.remove('success');
                badge.classList.add('danger');
                badge.textContent = '✖';
            }
        });
    });

    const suitActions = document.getElementById('suitActions');
    const suitButton = document.getElementById('suitButton');
    const suitDropdown = document.getElementById('suitDropdown');
    const selectAll = document.getElementById('selectAll');

    function updateSuitVisibility() {
        const checkedCount = document.querySelectorAll('.scenario-checkbox:checked').length;
        suitActions.style.display = checkedCount >= 2 ? 'block' : 'none';
    }

    document.querySelectorAll('.scenario-checkbox').forEach(cb => cb.addEventListener('change', updateSuitVisibility));
    selectAll.addEventListener('change', () => {
        document.querySelectorAll('.scenario-checkbox').forEach(cb => cb.checked = selectAll.checked);
        updateSuitVisibility();
    });

    suitButton.addEventListener('click', () => {
        suitDropdown.style.display = suitDropdown.style.display === 'block' ? 'none' : 'block';
    });

    document.getElementById('confirmSuit').addEventListener('click', () => {
        const name = document.getElementById('suitName').value.trim();
        const ids = Array.from(document.querySelectorAll('.scenario-checkbox:checked')).map(cb => cb.value);
        if (!name || ids.length < 2) return alert("Suit adı girin ve en az 2 senaryo seçin.");

        fetch("{{ route('suits.store') }}", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body: JSON.stringify({ name, scenario_ids: ids })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) location.reload();
            else alert("Hata oluştu.");
        })
        .catch(() => alert("Bir hata meydana geldi."));
    });

    // Test suitinden senaryo çıkarma fonksiyonu
    function removeFromSuit(scenarioId, suitId) {
        if (!confirm('Bu senaryoyu test suitinden çıkarmak istediğinizden emin misiniz?')) {
            return;
        }

        fetch(`/suits/${suitId}/remove-scenario`, {
            method: "POST",
            headers: { 
                "Content-Type": "application/json", 
                "X-CSRF-TOKEN": "{{ csrf_token() }}" 
            },
            body: JSON.stringify({ scenario_id: scenarioId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert("Hata oluştu: " + (data.message || "Bilinmeyen hata"));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Bir hata meydana geldi.");
        });
    }

    // Senaryo sonuçlarını sıfırlama fonksiyonu
    function resetScenarioResults(scenarioId) {
        if (!confirm('Bu senaryonun test sonuçlarını sıfırlamak istediğinizden emin misiniz?')) {
            return;
        }

        fetch(`/my-scenarios/${scenarioId}/reset-results`, {
            method: "POST",
            headers: { 
                "Content-Type": "application/json", 
                "X-CSRF-TOKEN": "{{ csrf_token() }}" 
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Sayfayı yenile
                location.reload();
            } else {
                alert("Hata oluştu: " + (data.message || "Bilinmeyen hata"));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Bir hata meydana geldi.");
        });
    }

    // Tekil kutucuk durumunu değiştirme fonksiyonu
    function toggleBadgeStatus(scenarioId, position) {
        if (!confirm('Bu kutucuğu sıfırlamak istediğinizden emin misiniz?')) {
            return;
        }

        fetch(`/my-scenarios/${scenarioId}/reset-single-result`, {
            method: "POST",
            headers: { 
                "Content-Type": "application/json", 
                "X-CSRF-TOKEN": "{{ csrf_token() }}" 
            },
            body: JSON.stringify({ position: position })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Sayfayı yenile
                location.reload();
            } else {
                alert("Hata oluştu: " + (data.message || "Bilinmeyen hata"));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Bir hata meydana geldi.");
        });
    }
</script>
@endpush
@endsection
