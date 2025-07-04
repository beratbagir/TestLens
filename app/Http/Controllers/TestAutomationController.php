<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class TestAutomationController extends Controller
{
    public function index()
    {
        // Mevcut test dosyalarını listele
        $testFiles = $this->getTestFiles();
        
        // Debug
        \Log::info('Test files found: ' . count($testFiles));
        foreach ($testFiles as $file) {
            \Log::info('Test file: ' . $file['name'] . ' - Project: ' . ($file['project_name'] ?? 'none'));
        }
        
        // Son çalışma sonuçlarını getir
        $lastResults = $this->getLastResults();
        
        return view('test-automation.index', compact('testFiles', 'lastResults'));
    }

    public function runTest(Request $request)
    {
        $request->validate([
            'test_path' => 'required|string',
            'test_name' => 'required|string'
        ]);

        $testPath = $request->test_path;
        $testName = $request->test_name;
        
        try {
            // Test başlangıç zamanı
            $startTime = microtime(true);
            
            // Test dosyasının tam yolunu al
            // Eğer testPath zaten storage/ ile başlıyorsa base_path ekleme
            if (str_starts_with($testPath, 'storage/')) {
                $fullTestPath = base_path($testPath);
            } else {
                $fullTestPath = $testPath;
            }
            
            // Test dosyasının var olup olmadığını kontrol et
            if (!file_exists($fullTestPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test dosyası bulunamadı: ' . $fullTestPath
                ], 404);
            }
            
            // Test dosyasının dizinini al
            $testDir = dirname($fullTestPath);
            
            // package.json ve playwright.config dosyasını bul
            $workingDir = $this->findPlaywrightProjectRoot($testDir);
            
            // Test dosyasının working directory'e göre relative path'ini hesapla
            $relativeTestPath = str_replace($workingDir . '/', '', $fullTestPath);
            
            // Spesifik test dosyasını çalıştır
            $command = sprintf(
                'cd %s && PATH="/usr/local/bin:$PATH" npx playwright test %s',
                escapeshellarg($workingDir),
                escapeshellarg($relativeTestPath)
            );
            
            // Komutu çalıştır
            $result = Process::timeout(300)->run($command);
            
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000); // milisaniye
            
            // Debug - komut çıktısını log'la
            \Log::info('Command executed: ' . $command);
            \Log::info('Exit code: ' . $result->exitCode());
            \Log::info('Output: ' . $result->output());
            \Log::info('Error output: ' . $result->errorOutput());
            \Log::info('Duration: ' . $duration . 'ms');
            
            // Test sonuçlarını ayrıştır
            $testResults = $this->parseTestResults($result->output(), $result->errorOutput());
            $summaryText = $this->getTestSummaryText($testResults);
            
            // Sonuçları işle
            $output = [
                'timestamp' => date('Y-m-d H:i:s'),
                'test_name' => $testName,
                'exit_code' => $result->exitCode(),
                'output' => $result->output(),
                'error_output' => $result->errorOutput(),
                'success' => $result->successful(),
                'duration' => $duration,
                'command' => $command,
                'working_directory' => $workingDir,
                'test_file' => $relativeTestPath,
                'test_results' => $testResults,
                'summary' => $summaryText
            ];
            
            return response()->json([
                'success' => true,
                'message' => $result->successful() ? 'Test başarıyla tamamlandı' : 'Test başarısız oldu',
                'result' => $output
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test çalıştırılırken hata oluştu: ' . $e->getMessage()
            ], 500);
        } finally {
            // Test sonucunu kaydet
            if (isset($output)) {
                $this->saveTestResult($output);
            }
        }
    }
    
    private function findPlaywrightProjectRoot($startDir)
    {
        $currentDir = $startDir;
        $maxLevels = 5; // Maksimum 5 seviye yukarı git
        $level = 0;
        
        while ($level < $maxLevels) {
            // playwright.config dosyasını ara
            $playwrightConfig = $currentDir . '/playwright.config.ts';
            $playwrightConfigJs = $currentDir . '/playwright.config.js';
            $packageJson = $currentDir . '/package.json';
            
            if (file_exists($playwrightConfig) || file_exists($playwrightConfigJs) || file_exists($packageJson)) {
                // Eğer yüklenen bir projeyse, dependencies'lerin kurulu olduğundan emin ol
                if (strpos($currentDir, 'storage/app/projects/') !== false) {
                    $this->ensureProjectDependencies($currentDir);
                }
                return $currentDir;
            }
            
            // Bir üst dizine geç
            $parentDir = dirname($currentDir);
            if ($parentDir === $currentDir) {
                // Root'a ulaştık
                break;
            }
            $currentDir = $parentDir;
            $level++;
        }
        
        // Hiçbir şey bulunamazsa başlangıç dizinini döndür
        return $startDir;
    }
    
    private function ensureProjectDependencies($projectDir)
    {
        $nodeModules = $projectDir . '/node_modules';
        $packageJson = $projectDir . '/package.json';
        $lockFile = $projectDir . '/package-lock.json';
        
        // package.json varsa ama node_modules yoksa veya çok eskiyse yeniden kur
        if (file_exists($packageJson)) {
            $needsInstall = false;
            
            if (!is_dir($nodeModules)) {
                $needsInstall = true;
            } else {
                // Lock file varsa ve node_modules'den daha yeniyse yeniden kur
                if (file_exists($lockFile)) {
                    if (filemtime($lockFile) > filemtime($nodeModules)) {
                        $needsInstall = true;
                    }
                }
            }
            
            if ($needsInstall) {
                \Log::info('Installing dependencies for project: ' . $projectDir);
                // Sessiz npm install
                $installCommand = sprintf('cd %s && PATH="/usr/local/bin:$PATH" npm install --silent', escapeshellarg($projectDir));
                Process::timeout(120)->run($installCommand);
                
                // Playwright install
                $playwrightCommand = sprintf('cd %s && PATH="/usr/local/bin:$PATH" npx playwright install chromium --quiet', escapeshellarg($projectDir));
                Process::timeout(60)->run($playwrightCommand);
            }
        }
    }

    public function runAllTests()
    {
        try {
            // Test başlangıç zamanı
            $startTime = microtime(true);
            
            // Base dizininden tüm testleri çalıştır
            $workingDir = base_path();
            $command = sprintf('cd %s && PATH="/usr/local/bin:$PATH" npx playwright test', escapeshellarg($workingDir));
            
            $result = Process::timeout(600)->run($command);
            
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000);
            
            // Test sonuçlarını ayrıştır
            $testResults = $this->parseTestResults($result->output(), $result->errorOutput());
            $summaryText = $this->getTestSummaryText($testResults);
            
            $output = [
                'timestamp' => date('Y-m-d H:i:s'),
                'test_name' => 'Tüm Testler',
                'exit_code' => $result->exitCode(),
                'output' => $result->output(),
                'error_output' => $result->errorOutput(),
                'success' => $result->successful(),
                'duration' => $duration,
                'command' => $command,
                'working_directory' => $workingDir,
                'test_results' => $testResults,
                'summary' => $summaryText
            ];
            
            return response()->json([
                'success' => true,
                'message' => $result->successful() ? 'Tüm testler başarıyla tamamlandı' : 'Bazı testler başarısız oldu',
                'result' => $output
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Testler çalıştırılırken hata oluştu: ' . $e->getMessage()
            ], 500);
        } finally {
            // Test sonucunu kaydet
            if (isset($output)) {
                $this->saveTestResult($output);
            }
        }
    }

    public function getTestResults()
    {
        return response()->json([]);
    }

    private function getTestFiles()
    {
        $testFiles = [];
        
        // Önce ayarlardan test dizinlerini alalım
        $settings = $this->getTestAutomationSettings();
        $testDirectories = $settings['test_directories'] ?? [
            'tests/e2e',
            'tests/playwright', 
            'playwright/tests',
            'e2e',
            'tests'
        ];
        
        foreach ($testDirectories as $directory) {
            // Eğer dizin "storage/app/projects/" ile başlıyorsa, bu yüklenmiş bir proje
            if (strpos($directory, 'storage/app/projects/') === 0) {
                $fullPath = base_path($directory);
                $projectName = $this->extractProjectNameFromPath($directory);
                
                if (is_dir($fullPath)) {
                    $this->scanDirectory($fullPath, $directory, $projectName, $testFiles);
                }
            } else {
                // Normal proje dizinleri
                $fullPath = base_path($directory);
                
                if (is_dir($fullPath)) {
                    $this->scanDirectory($fullPath, $directory, null, $testFiles);
                }
            }
        }
        
        return $testFiles;
    }
    
    private function scanDirectory($fullPath, $directory, $projectName, &$testFiles)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fullPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && preg_match('/\.(spec|test)\.(js|ts)$/', $file->getFilename())) {
                $fileName = $file->getFilename();
                $baseName = preg_replace('/\.(spec|test)\.(js|ts)$/i', '', $fileName);
                
                // __MACOSX klasörünü ve gizli dosyaları ignore et
                $relativePath = str_replace($fullPath . '/', '', $file->getPathname());
                if (strpos($relativePath, '__MACOSX') !== false || strpos($fileName, '._') === 0) {
                    continue;
                }
                
                $testFiles[] = [
                    'name' => $baseName,
                    'path' => str_replace(base_path() . '/', '', $file->getPathname()),
                    'full_path' => $file->getPathname(),
                    'directory' => $directory,
                    'project_name' => $projectName,
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime()
                ];
            }
        }
    }
    
    private function extractProjectNameFromPath($path)
    {
        // "storage/app/projects/ProjectName/..." formatından ProjectName'i çıkar
        $parts = explode('/', $path);
        if (count($parts) >= 4 && $parts[0] === 'storage' && $parts[1] === 'app' && $parts[2] === 'projects') {
            return $parts[3];
        }
        return null;
    }
    
    private function getTestAutomationSettings()
    {
        $defaultSettings = [
            'test_directories' => ['tests/e2e', 'tests/playwright', 'playwright/tests'],
            'default_reporter' => 'json',
            'timeout' => 30000,
            'headless' => true,
            'browser' => 'chromium',
            'uploaded_projects' => []
        ];
        
        if (\Illuminate\Support\Facades\Storage::exists('settings/test-automation.json')) {
            $settings = json_decode(\Illuminate\Support\Facades\Storage::get('settings/test-automation.json'), true);
            return array_merge($defaultSettings, $settings);
        }
        
        return $defaultSettings;
    }

    private function parseTestResults($output, $errorOutput)
    {
        $results = [
            'total' => 0,
            'passed' => 0,
            'failed' => 0,
            'skipped' => 0,
            'duration' => null,
            'tests' => [],
            'summary' => null
        ];

        // Playwright çıktısından test sonuçlarını ayrıştır
        $lines = explode("\n", $output . "\n" . $errorOutput);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Test sayılarını bul (örn: "5 passed (1.1m)")
            if (preg_match('/(\d+)\s+passed/', $line, $matches)) {
                $results['passed'] = (int)$matches[1];
                $results['total'] += $results['passed'];
            }
            
            if (preg_match('/(\d+)\s+failed/', $line, $matches)) {
                $results['failed'] = (int)$matches[1];
                $results['total'] += $results['failed'];
            }
            
            if (preg_match('/(\d+)\s+skipped/', $line, $matches)) {
                $results['skipped'] = (int)$matches[1];
                $results['total'] += $results['skipped'];
            }
            
            // Süreyi bul (örn: "(1.1m)" veya "(30s)")
            if (preg_match('/\(([0-9.]+[ms])\)/', $line, $matches)) {
                $results['duration'] = $matches[1];
            }
            
            // Özet satırını bul
            if (strpos($line, 'passed') !== false || strpos($line, 'failed') !== false) {
                if (preg_match('/^\s*(\d+.*(?:passed|failed|skipped).*)/', $line)) {
                    $results['summary'] = $line;
                }
            }
            
            // Bireysel test sonuçlarını bul
            if (preg_match('/^\s*[✓✗×]\s*(.+)$/', $line, $matches)) {
                $testName = trim($matches[1]);
                $status = strpos($line, '✓') !== false ? 'passed' : 'failed';
                
                $results['tests'][] = [
                    'name' => $testName,
                    'status' => $status
                ];
            }
        }
        
        return $results;
    }

    private function getTestSummaryText($results)
    {
        $parts = [];
        
        if ($results['total'] > 0) {
            if ($results['passed'] > 0) {
                $parts[] = $results['passed'] . ' başarılı';
            }
            if ($results['failed'] > 0) {
                $parts[] = $results['failed'] . ' başarısız';
            }
            if ($results['skipped'] > 0) {
                $parts[] = $results['skipped'] . ' atlandı';
            }
            
            $summary = 'Toplam ' . $results['total'] . ' test: ' . implode(', ', $parts);
            
            if ($results['duration']) {
                $summary .= ' (' . $results['duration'] . ')';
            }
            
            return $summary;
        }
        
        return 'Test sonucu bulunamadı';
    }

    private function saveTestResult($result)
    {
        $testResultsFile = storage_path('app/test-results/test-history.json');
        
        // Dizini oluştur
        $directory = dirname($testResultsFile);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Mevcut sonuçları oku
        $history = [];
        if (file_exists($testResultsFile)) {
            $content = file_get_contents($testResultsFile);
            $history = json_decode($content, true) ?: [];
        }
        
        // Yeni sonucu ekle
        $history[] = $result;
        
        // Son 50 sonucu tut
        if (count($history) > 50) {
            $history = array_slice($history, -50);
        }
        
        // Dosyaya kaydet
        file_put_contents($testResultsFile, json_encode($history, JSON_PRETTY_PRINT));
    }

    private function getLastResults()
    {
        $testResultsFile = storage_path('app/test-results/test-history.json');
        
        if (file_exists($testResultsFile)) {
            $content = file_get_contents($testResultsFile);
            $history = json_decode($content, true) ?: [];
            
            // Son 10 sonucu döndür (en yeniden eskiye)
            return array_reverse(array_slice($history, -10));
        }
        
        return [];
    }
}
