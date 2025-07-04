@extends('back.layouts.pages-layout')

@section('pageTitle', 'Regresyon Testi - Çoklu Suit')

@push('stylesheets')
<style>
    .scenario-card {
        transition: all 0.3s;
        border-left: 4px solid #dee2e6;
    }
    .scenario-card.pass {
        border-left-color: #28a745;
        background-color: #f8fff9;
    }
    .scenario-card.fail {
        border-left-color: #dc3545;
        background-color: #fff8f8;
    }
    .scenario-card.skip {
        border-left-color: #ffc107;
        background-color: #fffdf8;
    }
    .result-buttons .btn {
        margin: 2px;
    }
    .progress-info {
        font-size: 14px;
        margin-top: 10px;
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">
                        <i class="fas fa-play text-success"></i> Regresyon Testi
                    </h4>
                    @if(count($suits) == 1)
                        <small class="text-muted">Test Suit: {{ $suits->first()->name }}</small>
                    @else
                        <small class="text-muted">{{ count($suits) }} Test Suit Seçildi: {{ $suits->pluck('name')->join(', ') }}</small>
                    @endif
                </div>
                <a href="{{ route('regression.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Geri Dön
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Suit Bilgisi -->
            @if(count($suits) > 1)
            <div class="alert alert-info mb-4">
                <h6 class="fw-bold mb-2">
                    <i class="fas fa-layer-group"></i> Seçilen Test Suit'leri
                </h6>
                <div class="row">
                    @foreach($suits as $suit)
                    <div class="col-md-6 col-lg-4 mb-2">
                        <div class="d-flex align-items-center p-2 bg-dark rounded border">
                            <i class="fas fa-folder text-primary me-2"></i>
                            <div>
                                <div class="fw-bold">{{ $suit->name }}</div>
                                <small class="text-muted">{{ count($suit->scenario_ids) }} senaryo</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- İlerleme Çubuğu -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold">Test İlerlemesi</span>
                    <span id="progressText">0 / {{ count($scenarios) }}</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 0%" id="progressBar"></div>
                </div>
                <div class="progress-info text-center">
                    <span class="badge bg-success me-2">Başarılı: <span id="passCount">0</span></span>
                    <span class="badge bg-danger me-2">Başarısız: <span id="failCount">0</span></span>
                    <span class="badge bg-warning">Atlandı: <span id="skipCount">0</span></span>
                </div>
            </div>

            <!-- Senaryolar -->
            <form id="regressionForm">
                @csrf
                <input type="hidden" name="suit_ids" value="{{ $suits->pluck('id')->join(',') }}">
                
                @foreach($scenarios as $scenario)
                <div class="card scenario-card mb-3" data-scenario-id="{{ $scenario->id }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="card-title">
                                    <span class="badge bg-secondary me-2">{{ $loop->iteration }}</span>
                                    {{ $scenario->title }}
                                </h5>
                                <p class="card-text">{{ $scenario->description }}</p>
                                
                                @if($scenario->steps)
                                <div class="mt-2">
                                    <strong>Test Adımları:</strong>
                                    <ol class="mt-1">
                                        @foreach($scenario->steps as $step)
                                        <li>{{ $step }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                                @endif

                                @if($scenario->screenshots)
                                <div class="mt-2">
                                    <strong>Ekran Görüntüleri:</strong>
                                    <div class="mt-1">
                                        @foreach($scenario->screenshots as $img)
                                        <img src="{{ asset('storage/' . $img) }}" class="img-thumbnail me-2" style="width: 60px; height: 60px; object-fit: cover;">
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            <div class="col-md-4 text-end">
                                <p class="fw-bold">Test Sonucu:</p>
                                <div class="result-buttons">
                                    <button type="button" class="btn btn-success" onclick="setResult({{ $scenario->id }}, 'pass')">
                                        <i class="fas fa-check"></i> Geçti
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="setResult({{ $scenario->id }}, 'fail')">
                                        <i class="fas fa-times"></i> Başarısız
                                    </button>
                                    <button type="button" class="btn btn-warning" onclick="setResult({{ $scenario->id }}, 'skip')">
                                        <i class="fas fa-forward"></i> Atla
                                    </button>
                                </div>
                                <input type="hidden" name="scenario_results[{{ $scenario->id }}]" id="result_{{ $scenario->id }}">
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="text-center mt-4">
                    <button type="button" class="btn btn-primary btn-lg" id="saveResultsBtn" disabled>
                        <i class="fas fa-save"></i> Sonuçları Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const totalScenarios = {{ count($scenarios) }};
    let completedCount = 0;
    let passCount = 0;
    let failCount = 0;
    let skipCount = 0;

    function setResult(scenarioId, result) {
        const card = document.querySelector(`[data-scenario-id="${scenarioId}"]`);
        const input = document.getElementById(`result_${scenarioId}`);
        
        // Önceki sonucu temizle
        const oldResult = input.value;
        if (oldResult) {
            completedCount--;
            if (oldResult === 'pass') passCount--;
            else if (oldResult === 'fail') failCount--;
            else if (oldResult === 'skip') skipCount--;
        }

        // Yeni sonucu set et
        input.value = result;
        card.className = `card scenario-card mb-3 ${result}`;
        
        // Sayaçları güncelle
        completedCount++;
        if (result === 'pass') passCount++;
        else if (result === 'fail') failCount++;
        else if (result === 'skip') skipCount++;

        updateProgress();
    }

    function updateProgress() {
        const progressPercent = (completedCount / totalScenarios) * 100;
        
        document.getElementById('progressBar').style.width = progressPercent + '%';
        document.getElementById('progressText').textContent = `${completedCount} / ${totalScenarios}`;
        document.getElementById('passCount').textContent = passCount;
        document.getElementById('failCount').textContent = failCount;
        document.getElementById('skipCount').textContent = skipCount;

        // Tüm senaryolar tamamlandıysa kaydet butonunu aktif et
        document.getElementById('saveResultsBtn').disabled = completedCount < totalScenarios;
    }

    document.getElementById('saveResultsBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('regressionForm'));
        
        fetch("{{ route('regression.saveResults') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let message = `${data.suits_processed} test suit için sonuçlar kaydedildi!`;
                
                // Excel export seçeneği sun
                if (data.regression_ids && data.regression_ids.length > 0) {
                    message += '\n\nBu testin sonuçlarını Excel formatında indirmek ister misiniz?';
                    if (confirm(message)) {
                        // Çoklu regression export için yeni endpoint kullan
                        const exportForm = document.createElement('form');
                        exportForm.method = 'POST';
                        exportForm.action = '{{ route("regression.exportMultiple") }}';
                        
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        exportForm.appendChild(csrfInput);
                        
                        const idsInput = document.createElement('input');
                        idsInput.type = 'hidden';
                        idsInput.name = 'regression_ids';
                        idsInput.value = data.regression_ids.join(',');
                        exportForm.appendChild(idsInput);
                        
                        document.body.appendChild(exportForm);
                        exportForm.submit();
                        document.body.removeChild(exportForm);
                    }
                } else {
                    alert(message);
                }
                
                // Ana sayfaya yönlendir
                setTimeout(() => {
                    window.location.href = "{{ route('scenarios.index') }}";
                }, 2000);
            } else {
                alert('Bir hata oluştu: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu!');
        });
    });
</script>
@endpush
@endsection
