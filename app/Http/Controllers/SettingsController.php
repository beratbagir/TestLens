<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;

class SettingsController extends Controller
{
    public function index()
    {
        // Test otomasyonu ayarlarını getir
        $settings = $this->getTestAutomationSettings();
        
        // JIRA ayarlarını ekle
        $jiraSettings = $this->getJiraSettings();
        $settings = array_merge($settings, $jiraSettings);
        
        return view('settings.index', compact('settings'));
    }
    
    public function updateTestAutomation(Request $request)
    {
        $request->validate([
            'test_directories' => 'array',
            'test_directories.*' => 'string',
            'default_reporter' => 'string',
            'timeout' => 'integer|min:1000',
            'headless' => 'boolean',
            'browser' => 'string'
        ]);
        
        $settings = [
            'test_directories' => $request->test_directories ?? ['tests/e2e', 'tests/playwright'],
            'default_reporter' => $request->default_reporter ?? 'json',
            'timeout' => $request->timeout ?? 30000,
            'headless' => $request->headless ?? true,
            'browser' => $request->browser ?? 'chromium'
        ];
        
        Storage::put('settings/test-automation.json', json_encode($settings, JSON_PRETTY_PRINT));
        
        return redirect()->route('settings.index')->with('success', 'Test otomasyonu ayarları güncellendi!');
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
        
        if (Storage::exists('settings/test-automation.json')) {
            $settings = json_decode(Storage::get('settings/test-automation.json'), true);
            return array_merge($defaultSettings, $settings);
        }
        
        return $defaultSettings;
    }
    
    public function uploadProject(Request $request)
    {
        // Runtime'da PHP ayarlarını değiştir
        ini_set('upload_max_filesize', '100M');
        ini_set('post_max_size', '100M');
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', '300');
        ini_set('max_input_time', '300');
        
        $request->validate([
            'file_data' => 'required|string',
            'file_name' => 'required|string'
        ]);
        
        try {
            $fileData = $request->input('file_data');
            $fileName = $request->input('file_name');
            $projectName = pathinfo($fileName, PATHINFO_FILENAME);
            
            // Base64 decode
            $zipContent = base64_decode($fileData);
            if ($zipContent === false) {
                return redirect()->route('settings.index')
                    ->with('error', 'Dosya formatı hatalı. Lütfen geçerli bir ZIP dosyası yükleyin.');
            }
            
            // Storage dizinini oluştur
            $projectDir = storage_path('app/projects/' . $projectName);
            if (File::exists($projectDir)) {
                File::deleteDirectory($projectDir);
            }
            File::makeDirectory($projectDir, 0755, true);
            
            // Zip dosyasını geçici olarak kaydet
            $tempZipPath = storage_path('app/temp/' . uniqid() . '.zip');
            File::makeDirectory(dirname($tempZipPath), 0755, true);
            file_put_contents($tempZipPath, $zipContent);
            
            // Zip dosyasını extract et
            $zip = new ZipArchive;
            if ($zip->open($tempZipPath) === TRUE) {
                $zip->extractTo($projectDir);
                $zip->close();
                
                // Geçici zip dosyasını sil
                File::delete($tempZipPath);
                
                // Test dosyalarını bul
                $testFiles = $this->findTestFiles($projectDir);
                
                if (empty($testFiles['files'])) {
                    // Eğer test dosyası bulunamazsa projeyi sil
                    File::deleteDirectory($projectDir);
                    return redirect()->route('settings.index')
                        ->with('error', 'Yüklenen projede test dosyası bulunamadı. Lütfen /tests dizininde .spec.js veya .test.js dosyalarının olduğundan emin olun.');
                }
                
                // Test dizinlerini ayarlara ekle
                $this->updateTestDirectoriesWithProject($projectName, $testFiles);
                
                return redirect()->route('settings.index')
                    ->with('success', "Proje '{$projectName}' başarıyla yüklendi! {$testFiles['count']} test dosyası bulundu.");
                    
            } else {
                File::delete($tempZipPath);
                return redirect()->route('settings.index')
                    ->with('error', 'Zip dosyası açılamadı. Lütfen geçerli bir zip dosyası yüklediğinizden emin olun.');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('settings.index')
                ->with('error', 'Proje yüklenirken hata oluştu: ' . $e->getMessage());
        }
    }
    
    private function findTestFiles($projectDir)
    {
        $testFiles = [];
        $count = 0;
        
        // Recursive olarak tüm .spec.js ve .test.js dosyalarını bul
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($projectDir)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filename = $file->getFilename();
                $relativePath = str_replace($projectDir . '/', '', $file->getPathname());
                
                // __MACOSX klasörünü ve gizli dosyaları ignore et
                if (strpos($relativePath, '__MACOSX') !== false || strpos($filename, '._') === 0) {
                    continue;
                }
                
                // Test dosyası pattern'leri
                if (preg_match('/\.(spec|test)\.(js|ts)$/i', $filename)) {
                    
                    $testFiles[] = $relativePath;
                    $count++;
                }
            }
        }
        
        return [
            'files' => $testFiles,
            'count' => $count,
            'directories' => $this->extractUniqueDirectories($testFiles)
        ];
    }
    
    private function extractUniqueDirectories($testFiles)
    {
        $directories = [];
        
        foreach ($testFiles as $file) {
            $dir = dirname($file);
            if ($dir && $dir !== '.' && !in_array($dir, $directories)) {
                $directories[] = $dir;
            }
        }
        
        return $directories;
    }
    
    private function updateTestDirectoriesWithProject($projectName, $testFiles)
    {
        $settings = $this->getTestAutomationSettings();
        $newDirectories = [];
        
        // Mevcut dizinleri koru
        $newDirectories = $settings['test_directories'];
        
        // Sadece gerçek test dosyalarının bulunduğu dizinleri ekle
        foreach ($testFiles['directories'] as $dir) {
            $projectPath = "storage/app/projects/{$projectName}/{$dir}";
            if (!in_array($projectPath, $newDirectories)) {
                $newDirectories[] = $projectPath;
            }
        }
        
        // Eğer hiç alt dizin yoksa ana proje dizinini ekle
        if (empty($testFiles['directories'])) {
            $projectPath = "storage/app/projects/{$projectName}";
            if (!in_array($projectPath, $newDirectories)) {
                $newDirectories[] = $projectPath;
            }
        }
        
        $settings['test_directories'] = $newDirectories;
        $settings['uploaded_projects'][$projectName] = [
            'uploaded_at' => now()->toDateTimeString(),
            'test_files' => $testFiles['files'],
            'test_count' => $testFiles['count']
        ];
        
        Storage::put('settings/test-automation.json', json_encode($settings, JSON_PRETTY_PRINT));
    }
    
    public function deleteProject($projectName)
    {
        try {
            $projectDir = storage_path('app/projects/' . $projectName);
            
            if (File::exists($projectDir)) {
                File::deleteDirectory($projectDir);
            }
            
            // Ayarlardan da kaldır
            $settings = $this->getTestAutomationSettings();
            
            // Test dizinlerinden proje ile ilgili olanları kaldır
            $settings['test_directories'] = array_filter($settings['test_directories'], function($dir) use ($projectName) {
                return strpos($dir, "storage/app/projects/{$projectName}") === false;
            });
            
            // Uploaded projects listesinden kaldır
            if (isset($settings['uploaded_projects'][$projectName])) {
                unset($settings['uploaded_projects'][$projectName]);
            }
            
            Storage::put('settings/test-automation.json', json_encode($settings, JSON_PRETTY_PRINT));
            
            return redirect()->route('settings.index')
                ->with('success', "Proje '{$projectName}' başarıyla silindi!");
                
        } catch (\Exception $e) {
            return redirect()->route('settings.index')
                ->with('error', 'Proje silinirken hata oluştu: ' . $e->getMessage());
        }
    }
    
    public function startUpload(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'filesize' => 'required|integer',
            'total_chunks' => 'required|integer'
        ]);
        
        $sessionId = uniqid('upload_');
        $uploadDir = storage_path("app/temp/uploads/{$sessionId}");
        
        File::makeDirectory($uploadDir, 0755, true);
        
        // Upload bilgilerini session'a kaydet
        session([
            "upload_{$sessionId}" => [
                'filename' => $request->filename,
                'filesize' => $request->filesize,
                'total_chunks' => $request->total_chunks,
                'upload_dir' => $uploadDir,
                'uploaded_chunks' => []
            ]
        ]);
        
        return response()->json([
            'success' => true,
            'session_id' => $sessionId
        ]);
    }
    
    public function uploadChunk(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'chunk_index' => 'required|integer',
            'total_chunks' => 'required|integer',
            'chunk' => 'required|file'
        ]);
        
        $sessionId = $request->session_id;
        $uploadData = session("upload_{$sessionId}");
        
        if (!$uploadData) {
            return response()->json([
                'success' => false,
                'message' => 'Upload session bulunamadı'
            ], 400);
        }
        
        $chunkIndex = $request->chunk_index;
        $chunk = $request->file('chunk');
        $chunkPath = $uploadData['upload_dir'] . "/chunk_{$chunkIndex}";
        
        // Chunk'ı kaydet
        $chunk->move(dirname($chunkPath), basename($chunkPath));
        
        // Uploaded chunks listesini güncelle
        $uploadData['uploaded_chunks'][] = $chunkIndex;
        session(["upload_{$sessionId}" => $uploadData]);
        
        return response()->json([
            'success' => true,
            'chunk_index' => $chunkIndex,
            'uploaded_chunks' => count($uploadData['uploaded_chunks'])
        ]);
    }
    
    public function completeUpload(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);
        
        $sessionId = $request->session_id;
        $uploadData = session("upload_{$sessionId}");
        
        if (!$uploadData) {
            return response()->json([
                'success' => false,
                'message' => 'Upload session bulunamadı'
            ], 400);
        }
        
        try {
            // Tüm chunk'ları birleştir
            $finalFilePath = storage_path('app/temp/' . $uploadData['filename']);
            $finalFile = fopen($finalFilePath, 'wb');
            
            for ($i = 0; $i < $uploadData['total_chunks']; $i++) {
                $chunkPath = $uploadData['upload_dir'] . "/chunk_{$i}";
                if (file_exists($chunkPath)) {
                    $chunkData = file_get_contents($chunkPath);
                    fwrite($finalFile, $chunkData);
                    unlink($chunkPath); // Chunk'ı sil
                }
            }
            fclose($finalFile);
            
            // Upload dizinini temizle
            File::deleteDirectory($uploadData['upload_dir']);
            
            // Dosyayı process et (mevcut uploadProject mantığını kullan)
            $result = $this->processUploadedFile($finalFilePath, $uploadData['filename']);
            
            // Session'ı temizle
            session()->forget("upload_{$sessionId}");
            
            // Geçici dosyayı sil
            if (file_exists($finalFilePath)) {
                unlink($finalFilePath);
            }
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            // Temizlik
            if (isset($uploadData['upload_dir']) && is_dir($uploadData['upload_dir'])) {
                File::deleteDirectory($uploadData['upload_dir']);
            }
            session()->forget("upload_{$sessionId}");
            
            return response()->json([
                'success' => false,
                'message' => 'Upload tamamlanırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function processUploadedFile($filePath, $originalName)
    {
        $projectName = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Storage dizinini oluştur
        $projectDir = storage_path('app/projects/' . $projectName);
        if (File::exists($projectDir)) {
            File::deleteDirectory($projectDir);
        }
        File::makeDirectory($projectDir, 0755, true);
        
        // Zip dosyasını extract et
        $zip = new ZipArchive;
        if ($zip->open($filePath) === TRUE) {
            $zip->extractTo($projectDir);
            $zip->close();
            
            // Test dosyalarını bul
            $testFiles = $this->findTestFiles($projectDir);
            
            if (empty($testFiles['files'])) {
                // Eğer test dosyası bulunamazsa projeyi sil
                File::deleteDirectory($projectDir);
                return [
                    'success' => false,
                    'message' => 'Yüklenen projede test dosyası bulunamadı. Lütfen /tests dizininde .spec.js veya .test.js dosyalarının olduğundan emin olun.'
                ];
            }
            
            // Test dizinlerini ayarlara ekle
            $this->updateTestDirectoriesWithProject($projectName, $testFiles);
            
            return [
                'success' => true,
                'message' => "Proje '{$projectName}' başarıyla yüklendi! {$testFiles['count']} test dosyası bulundu."
            ];
            
        } else {
            return [
                'success' => false,
                'message' => 'Zip dosyası açılamadı. Lütfen geçerli bir zip dosyası yüklediğinizden emin olun.'
            ];
        }
    }
    
    private function convertToBytes($value)
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $num = (int) $value;
        
        switch($last) {
            case 'g': $num *= 1024;
            case 'm': $num *= 1024;
            case 'k': $num *= 1024;
        }
        
        return $num;
    }
    
    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
        
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
    
    public function updateJira(Request $request)
    {
        $request->validate([
            'jira_url' => 'required|url',
            'jira_username' => 'required|string',
            'jira_api_token' => 'required|string',
            'jira_project_key' => 'required|string',
            'jira_issue_type' => 'required|string',
            'jira_priority' => 'required|string'
        ]);

        $settings = [
            'jira_url' => rtrim($request->jira_url, '/'),
            'jira_username' => $request->jira_username,
            'jira_api_token' => $request->jira_api_token,
            'jira_project_key' => strtoupper($request->jira_project_key),
            'jira_issue_type' => $request->jira_issue_type,
            'jira_priority' => $request->jira_priority
        ];

        Storage::put('settings/jira.json', json_encode($settings, JSON_PRETTY_PRINT));
        
        // Update config values
        config([
            'app.jira_url' => $settings['jira_url'],
            'app.jira_username' => $settings['jira_username'],
            'app.jira_api_token' => $settings['jira_api_token'],
            'app.jira_project_key' => $settings['jira_project_key'],
            'app.jira_issue_type' => $settings['jira_issue_type'],
            'app.jira_priority' => $settings['jira_priority']
        ]);

        return redirect()->route('settings.index')->with('success', 'JIRA ayarları başarıyla güncellendi!');
    }

    public function testJiraConnection(Request $request)
    {
        $request->validate([
            'jira_url' => 'required|url',
            'jira_username' => 'required|string',
            'jira_api_token' => 'required|string'
        ]);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->withBasicAuth($request->jira_username, $request->jira_api_token)
                ->get(rtrim($request->jira_url, '/') . '/rest/api/2/myself');

            if ($response->successful()) {
                $userInfo = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => 'JIRA bağlantısı başarılı',
                    'user_info' => $userInfo
                ]);
            } else {
                throw new \Exception('Kimlik doğrulama başarısız. HTTP ' . $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bağlantı hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getJiraSettings()
    {
        $defaultSettings = [
            'jira_url' => '',
            'jira_username' => '',
            'jira_api_token' => '',
            'jira_project_key' => 'TEST',
            'jira_issue_type' => 'Bug',
            'jira_priority' => 'Medium'
        ];

        if (Storage::exists('settings/jira.json')) {
            $settings = json_decode(Storage::get('settings/jira.json'), true);
            return array_merge($defaultSettings, $settings);
        }

        return $defaultSettings;
    }
}
