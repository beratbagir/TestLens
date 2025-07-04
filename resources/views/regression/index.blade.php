@extends('back.layouts.pages-layout')

@section('pageTitle', 'Regresyon Testi')

@push('stylesheets')
<style>
    .suit-card {
        transition: transform 0.2s;
        cursor: pointer;
        border: 2px solid transparent;
    }
    .suit-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .suit-card.selected {
        border-color: #007bff;
        background-color: #f8f9ff;
        box-shadow: 0 4px 12px rgba(0,123,255,0.2);
    }
    .selected-counter {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        padding: 15px 25px;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(0,123,255,0.3);
        transition: all 0.3s ease;
    }
    .selected-counter:hover {
        transform: scale(1.05);
    }
    .suit-selection-summary {
        background: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 10px;
        padding: 20px;
        margin: 20px 0;
        border: 1px solid rgba(0,123,255,0.2);
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fas fa-play text-success"></i> Regresyon Testi
            </h4>
            <a href="{{ route('scenarios.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Geri Dön
            </a>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                Regresyon testi yapmak istediğiniz test suit'lerini seçin. Birden fazla suit seçebilirsiniz.
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllBtn">
                                <i class="fas fa-check-square"></i> Tümünü Seç
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="clearAllBtn">
                                <i class="fas fa-times"></i> Seçimi Temizle
                            </button>
                        </div>
                        <div id="selectedInfo" class="text-muted">
                            <small>Seçilen suit sayısı: <span id="selectedCount">0</span></small>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($suits) > 0)
                <div class="row">
                    @foreach($suits as $suit)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card suit-card h-100" onclick="toggleSuit({{ $suit->id }})" data-suit-id="{{ $suit->id }}">
                            <div class="card-body text-center position-relative">
                                <div class="position-absolute top-0 end-0 p-2">
                                    <i class="fas fa-check-circle text-success" style="display: none; font-size: 20px;" id="check-{{ $suit->id }}"></i>
                                </div>
                                <i class="fas fa-folder-open fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">{{ $suit->name }}</h5>
                                <p class="card-text text-muted">
                                    {{ count($suit->scenario_ids) }} senaryo
                                </p>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        Senaryo ID'leri: {{ implode(', ', array_slice($suit->scenario_ids, 0, 3)) }}
                                        @if(count($suit->scenario_ids) > 3)
                                            <span>...</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <form id="regressionForm" action="{{ route('regression.run') }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="suit_ids" id="selectedSuitIds">
                </form>

                <!-- Seçilen suit'ler özeti -->
                <div id="selectionSummary" class="suit-selection-summary" style="display: none;">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-list-check"></i> Seçilen Test Suit'leri
                    </h6>
                    <div id="selectedSuitsList" class="row">
                        <!-- Dinamik olarak doldurulacak -->
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            Toplam <span id="totalScenarios">0</span> senaryo ile regresyon testi yapılacak.
                        </small>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="button" class="btn btn-success btn-lg" id="startRegressionBtn" disabled>
                        <i class="fas fa-rocket"></i> Regresyon Testini Başlat
                        <span id="buttonCounter" class="badge bg-light text-dark ms-2" style="display: none;">0</span>
                    </button>
                </div>

                <!-- Floating Counter -->
                <div id="floatingCounter" class="selected-counter" style="display: none;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-layer-group me-2"></i>
                        <div>
                            <div class="fw-bold"><span id="floatingCount">0</span> Suit Seçildi</div>
                            <small><span id="floatingScenarios">0</span> senaryo</small>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h5>Henüz Test Suit'i Bulunamadı</h5>
                    <p>Regresyon testi yapabilmek için öncelikle test suit'i oluşturmanız gerekiyor.</p>
                    <a href="{{ route('scenarios.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Test Suit Oluştur
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    let selectedSuits = new Set();
    const suitsData = @json($suits->keyBy('id'));

    function toggleSuit(suitId) {
        const card = document.querySelector(`[data-suit-id="${suitId}"]`);
        const checkIcon = document.getElementById(`check-${suitId}`);
        
        if (selectedSuits.has(suitId)) {
            // Suit'i kaldır
            selectedSuits.delete(suitId);
            card.classList.remove('selected');
            checkIcon.style.display = 'none';
        } else {
            // Suit'i ekle
            selectedSuits.add(suitId);
            card.classList.add('selected');
            checkIcon.style.display = 'block';
        }
        
        updateUI();
    }

    function updateUI() {
        const count = selectedSuits.size;
        const totalScenarios = calculateTotalScenarios();
        
        // Sayaçları güncelle
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('floatingCount').textContent = count;
        document.getElementById('floatingScenarios').textContent = totalScenarios;
        document.getElementById('totalScenarios').textContent = totalScenarios;
        
        // Button counter
        const buttonCounter = document.getElementById('buttonCounter');
        if (count > 0) {
            buttonCounter.style.display = 'inline';
            buttonCounter.textContent = count;
        } else {
            buttonCounter.style.display = 'none';
        }
        
        // Floating counter göster/gizle
        const floatingCounter = document.getElementById('floatingCounter');
        floatingCounter.style.display = count > 0 ? 'block' : 'none';
        
        // Selection summary göster/gizle
        const selectionSummary = document.getElementById('selectionSummary');
        if (count > 0) {
            selectionSummary.style.display = 'block';
            updateSelectionSummary();
        } else {
            selectionSummary.style.display = 'none';
        }
        
        // Start button durumu
        const startBtn = document.getElementById('startRegressionBtn');
        startBtn.disabled = count === 0;
        
        // Form input'u güncelle
        document.getElementById('selectedSuitIds').value = Array.from(selectedSuits).join(',');
    }

    function calculateTotalScenarios() {
        let total = 0;
        selectedSuits.forEach(suitId => {
            const suit = suitsData[suitId];
            if (suit && suit.scenario_ids) {
                total += suit.scenario_ids.length;
            }
        });
        return total;
    }

    function updateSelectionSummary() {
        const container = document.getElementById('selectedSuitsList');
        container.innerHTML = '';
        
        selectedSuits.forEach(suitId => {
            const suit = suitsData[suitId];
            if (suit) {
                const col = document.createElement('div');
                col.className = 'col-md-6 col-lg-4 mb-2';
                col.innerHTML = `
                    <div class="d-flex align-items-center p-2 bg-dark rounded border">
                        <i class="fas fa-folder text-primary me-2"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold">${suit.name}</div>
                            <small class="text-muted">${suit.scenario_ids.length} senaryo</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="toggleSuit(${suitId})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                container.appendChild(col);
            }
        });
    }

    // Tümünü seç butonu
    document.getElementById('selectAllBtn').addEventListener('click', function() {
        Object.keys(suitsData).forEach(suitId => {
            if (!selectedSuits.has(parseInt(suitId))) {
                selectedSuits.add(parseInt(suitId));
                const card = document.querySelector(`[data-suit-id="${suitId}"]`);
                const checkIcon = document.getElementById(`check-${suitId}`);
                card.classList.add('selected');
                checkIcon.style.display = 'block';
            }
        });
        updateUI();
    });

    // Seçimi temizle butonu
    document.getElementById('clearAllBtn').addEventListener('click', function() {
        selectedSuits.forEach(suitId => {
            const card = document.querySelector(`[data-suit-id="${suitId}"]`);
            const checkIcon = document.getElementById(`check-${suitId}`);
            card.classList.remove('selected');
            checkIcon.style.display = 'none';
        });
        selectedSuits.clear();
        updateUI();
    });

    // Regresyon başlat butonu
    document.getElementById('startRegressionBtn').addEventListener('click', function() {
        if (selectedSuits.size > 0) {
            document.getElementById('regressionForm').submit();
        }
    });

    // Sayfa yüklendiğinde UI'ı başlat
    updateUI();
</script>
@endpush
@endsection
