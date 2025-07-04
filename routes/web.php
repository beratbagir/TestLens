<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestSuitController;
use App\Http\Controllers\ScenarioController;
use App\Http\Controllers\RegressionController;
use App\Http\Controllers\TestAutomationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\JiraController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/my-scenarios', [ScenarioController::class, 'index'])->name('scenarios.index');
Route::post('/my-scenarios', [ScenarioController::class, 'store'])->name('scenarios.store');
Route::post('/suits', [TestSuitController::class, 'store'])->name('suits.store');
Route::put('/suits/{id}', [TestSuitController::class, 'update'])->name('suits.update');
Route::delete('/suits/{id}', [TestSuitController::class, 'destroy'])->name('suits.destroy');
Route::post('/suits/{id}/remove-scenario', [TestSuitController::class, 'removeScenario'])->name('suits.removeScenario');
Route::put('/my-scenarios/{id}', [ScenarioController::class, 'update'])->name('scenarios.update');
Route::delete('/my-scenarios/{id}', [ScenarioController::class, 'destroy'])->name('scenarios.destroy');
Route::post('/my-scenarios/{id}/reset-results', [ScenarioController::class, 'resetResults'])->name('scenarios.resetResults');
Route::post('/my-scenarios/{id}/reset-single-result', [ScenarioController::class, 'resetSingleResult'])->name('scenarios.resetSingleResult');

// Regresyon Test Route'ları
Route::get('/regression', [RegressionController::class, 'index'])->name('regression.index');
Route::post('/regression/run', [RegressionController::class, 'run'])->name('regression.run');
Route::post('/regression/save-results', [RegressionController::class, 'saveResults'])->name('regression.saveResults');
Route::get('/regression/export/{id}', [RegressionController::class, 'exportToExcel'])->name('regression.export');
Route::post('/regression/export-multiple', [RegressionController::class, 'exportMultipleToExcel'])->name('regression.exportMultiple');

// Test Otomasyonu Route'ları
Route::get('/test-automation', [TestAutomationController::class, 'index'])->name('test-automation.index');
Route::post('/test-automation/run-test', [TestAutomationController::class, 'runTest'])->name('test-automation.runTest');
Route::post('/test-automation/run-multiple', [TestAutomationController::class, 'runMultipleTests'])->name('test-automation.runMultiple');
Route::post('/test-automation/run-all', [TestAutomationController::class, 'runAllTests'])->name('test-automation.runAll');
Route::post('/test-automation/clear-results', [TestAutomationController::class, 'clearResults'])->name('test-automation.clearResults');
Route::get('/test-automation/results', [TestAutomationController::class, 'getTestResults'])->name('test-automation.results');

// Test Raporları Route'ları
Route::get('/test-reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/test-reports/download/{filename}', [ReportController::class, 'downloadResult'])->name('reports.download');
Route::delete('/test-reports/delete/{filename}', [ReportController::class, 'deleteResult'])->name('reports.delete');

// Ayarlar Route'ları
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('/settings/test-automation', [SettingsController::class, 'updateTestAutomation'])->name('settings.updateTestAutomation');
Route::post('/settings/jira', [SettingsController::class, 'updateJira'])->name('settings.updateJira');
Route::post('/settings/test-jira-connection', [SettingsController::class, 'testJiraConnection'])->name('settings.testJiraConnection');
Route::post('/settings/upload-project', [SettingsController::class, 'uploadProject'])
    ->middleware(\App\Http\Middleware\IncreaseUploadLimits::class)
    ->name('settings.uploadProject');
Route::delete('/settings/delete-project/{projectName}', [SettingsController::class, 'deleteProject'])->name('settings.deleteProject');

// JIRA Route'ları
Route::get('/jira-tasks', [JiraController::class, 'index'])->name('jira.index');
Route::post('/jira-tasks/fetch', [JiraController::class, 'fetchTasks'])->name('jira.fetchTasks');
Route::post('/jira-tasks/{issueKey}/comment', [JiraController::class, 'addComment'])->name('jira.addComment');
Route::post('/jira-tasks/create-issue', [JiraController::class, 'createIssue'])->name('jira.createIssue');

// Chunked Upload Routes
Route::post('/settings/start-upload', [SettingsController::class, 'startUpload'])->name('settings.startUpload');
Route::post('/settings/upload-chunk', [SettingsController::class, 'uploadChunk'])->name('settings.uploadChunk');
Route::post('/settings/complete-upload', [SettingsController::class, 'completeUpload'])->name('settings.completeUpload');

// Debug route for PHP settings
Route::get('/debug-php', function() {
    return response()->json([
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'file_uploads' => ini_get('file_uploads'),
        'max_input_time' => ini_get('max_input_time'),
    ]);
});
