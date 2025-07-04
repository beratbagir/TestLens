<?php

namespace App\Http\Controllers;

use App\Models\Scenario;
use App\Models\TestSuit;
use App\Models\RegressionResult;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegressionExport;
use App\Exports\MultiRegressionExport;

class RegressionController extends Controller
{
    public function index()
    {
        $scenarios = Scenario::all();
        $suits = TestSuit::all();
        
        return view('regression.index', compact('scenarios', 'suits'));
    }

    public function run(Request $request)
    {
        $request->validate([
            'suit_ids' => 'required|string'
        ]);

        // Seçilen suit ID'lerini ayrıştır
        $suitIds = array_filter(explode(',', $request->suit_ids));
        
        if (empty($suitIds)) {
            return redirect()->back()->with('error', 'En az bir test suit seçmelisiniz.');
        }

        // Suit'leri getir
        $suits = TestSuit::whereIn('id', $suitIds)->get();
        
        if ($suits->isEmpty()) {
            return redirect()->back()->with('error', 'Seçilen test suit\'leri bulunamadı.');
        }

        // Tüm senaryo ID'lerini topla
        $allScenarioIds = [];
        foreach ($suits as $suit) {
            $allScenarioIds = array_merge($allScenarioIds, $suit->scenario_ids);
        }
        $allScenarioIds = array_unique($allScenarioIds);

        // Senaryoları getir
        $scenarios = Scenario::whereIn('id', $allScenarioIds)->get();
        
        return view('regression.run', compact('suits', 'scenarios'));
    }

    public function saveResults(Request $request)
    {
        $request->validate([
            'suit_ids' => 'required|string',
            'scenario_results' => 'required|array',
            'scenario_results.*' => 'required|in:pass,fail,skip'
        ]);

        // Seçilen suit ID'lerini ayrıştır
        $suitIds = array_filter(explode(',', $request->suit_ids));
        
        // Her suit için ayrı regresyon sonucu kaydet
        $regressionResults = [];
        foreach ($suitIds as $suitId) {
            $suit = TestSuit::find($suitId);
            if ($suit) {
                // Bu suit'e ait senaryoların sonuçlarını filtrele
                $suitScenarioResults = array_intersect_key(
                    $request->scenario_results,
                    array_flip($suit->scenario_ids)
                );
                
                if (!empty($suitScenarioResults)) {
                    $regressionResult = RegressionResult::create([
                        'suit_id' => $suitId,
                        'results' => $suitScenarioResults,
                        'run_date' => now()
                    ]);
                    $regressionResults[] = $regressionResult;
                }
            }
        }

        // Ana sayfadaki kutucukları güncelle - session'da sonuçları sakla
        $existingResults = session('regression_results', []);
        
        foreach($request->scenario_results as $scenarioId => $result) {
            // Bu senaryo için mevcut sonuçları kontrol et
            $usedPositions = [];
            if (isset($existingResults[$scenarioId])) {
                $usedPositions = array_column($existingResults[$scenarioId], 'position');
            }
            
            // 1'den 20'ye kadar boş pozisyon bul
            $targetPosition = 1;
            for ($i = 1; $i <= 20; $i++) {
                if (!in_array($i, $usedPositions)) {
                    $targetPosition = $i;
                    break;
                }
            }
            
            // Mevcut sonuçları koru ve yeni sonucu ekle
            if (!isset($existingResults[$scenarioId])) {
                $existingResults[$scenarioId] = [];
            }
            
            $existingResults[$scenarioId][] = [
                'position' => $targetPosition,
                'result' => $result,
                'date' => now()->format('Y-m-d H:i:s')
            ];
        }
        
        session(['regression_results' => $existingResults]);

        return response()->json([
            'success' => true,
            'regression_ids' => collect($regressionResults)->pluck('id')->toArray(),
            'message' => count($regressionResults) . ' test suit için sonuçlar başarıyla kaydedildi!',
            'suits_processed' => count($regressionResults)
        ]);
    }

    public function exportToExcel($id)
    {
        $regressionResult = RegressionResult::findOrFail($id);
        $suit = TestSuit::findOrFail($regressionResult->suit_id);
        $scenarios = Scenario::whereIn('id', $suit->scenario_ids)->get();

        $filename = 'regression_test_' . Str::slug($suit->name) . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new RegressionExport($regressionResult, $scenarios, $suit), $filename);
    }
    
    public function exportMultipleToExcel(Request $request)
    {
        $request->validate([
            'regression_ids' => 'required|string'
        ]);

        $regressionIds = array_filter(explode(',', $request->regression_ids));
        $regressionResults = RegressionResult::whereIn('id', $regressionIds)->get();
        
        if ($regressionResults->isEmpty()) {
            return redirect()->back()->with('error', 'Regresyon sonuçları bulunamadı.');
        }

        // İlgili suit'leri getir
        $suitIds = $regressionResults->pluck('suit_id')->unique();
        $suits = TestSuit::whereIn('id', $suitIds)->get();

        // Tüm senaryoları topla
        $allScenarioIds = [];
        foreach ($suits as $suit) {
            $allScenarioIds = array_merge($allScenarioIds, $suit->scenario_ids);
        }
        $allScenarioIds = array_unique($allScenarioIds);
        $scenarios = Scenario::whereIn('id', $allScenarioIds)->get();

        $filename = 'multi_regression_test_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new MultiRegressionExport($regressionResults, $scenarios, $suits), 
            $filename
        );
    }
}
