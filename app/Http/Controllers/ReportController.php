<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // Test otomasyonu sonuçlarını getir
        $automationResults = $this->getAutomationResults();
        
        // Regresyon test sonuçlarını getir
        $regressionResults = $this->getRegressionResults();
        
        // Test istatistiklerini hesapla
        $statistics = $this->calculateStatistics($automationResults);
        
        return view('reports.index', compact('automationResults', 'regressionResults', 'statistics'));
    }
    
    private function getAutomationResults()
    {
        // Test otomasyonu geçmişinden sonuçları al
        $testHistoryFile = storage_path('app/test-results/test-history.json');
        
        if (file_exists($testHistoryFile)) {
            $content = file_get_contents($testHistoryFile);
            $history = json_decode($content, true) ?: [];
            
            // En son 20 test sonucunu döndür
            return array_reverse(array_slice($history, -20));
        }
        
        return [];
    }
    
    private function calculateStatistics($results)
    {
        $stats = [
            'total_tests' => 0,
            'passed_tests' => 0,
            'failed_tests' => 0,
            'success_rate' => 0,
            'recent_tests' => []
        ];
        
        foreach ($results as $result) {
            if (isset($result['test_results']) && $result['test_results']['total'] > 0) {
                $testResults = $result['test_results'];
                $stats['total_tests'] += $testResults['total'];
                $stats['passed_tests'] += $testResults['passed'];
                $stats['failed_tests'] += $testResults['failed'];
                
                // Son testleri kaydet
                $stats['recent_tests'][] = [
                    'name' => $result['test_name'],
                    'timestamp' => $result['timestamp'],
                    'total' => $testResults['total'],
                    'passed' => $testResults['passed'],
                    'failed' => $testResults['failed'],
                    'skipped' => $testResults['skipped'],
                    'duration' => $testResults['duration'] ?? null,
                    'success' => $result['success']
                ];
            }
        }
        
        // Başarı oranını hesapla
        if ($stats['total_tests'] > 0) {
            $stats['success_rate'] = round(($stats['passed_tests'] / $stats['total_tests']) * 100, 1);
        }
        
        return $stats;
    }
    
    private function getRegressionResults()
    {
        // Session'dan regresyon sonuçlarını al
        $sessionResults = session('regression_results', []);
        
        $results = [];
        foreach ($sessionResults as $scenarioId => $tests) {
            if (is_array($tests)) {
                foreach ($tests as $test) {
                    $results[] = [
                        'scenario_id' => $scenarioId,
                        'position' => $test['position'] ?? 0,
                        'result' => $test['result'] ?? 'unknown',
                        'date' => $test['date'] ?? 'Bilinmeyen',
                        'type' => 'regression'
                    ];
                }
            }
        }
        
        return $results;
    }
    
    public function downloadResult($filename)
    {
        $filePath = 'test-results/' . $filename;
        
        if (!Storage::exists($filePath)) {
            abort(404, 'Dosya bulunamadı');
        }
        
        return Storage::download($filePath);
    }
    
    public function deleteResult($filename)
    {
        $filePath = 'test-results/' . $filename;
        
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
            return redirect()->route('reports.index')->with('success', 'Test sonucu silindi!');
        }
        
        return redirect()->route('reports.index')->with('error', 'Dosya bulunamadı!');
    }
}
