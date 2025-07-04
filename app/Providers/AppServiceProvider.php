<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load JIRA settings from storage into config
        $this->loadJiraSettings();
    }

    /**
     * Load JIRA settings from storage into config
     */
    private function loadJiraSettings(): void
    {
        try {
            if (\Illuminate\Support\Facades\Storage::exists('settings/jira.json')) {
                $jiraSettings = json_decode(\Illuminate\Support\Facades\Storage::get('settings/jira.json'), true);
                
                if ($jiraSettings) {
                    config([
                        'app.jira_url' => $jiraSettings['jira_url'] ?? '',
                        'app.jira_username' => $jiraSettings['jira_username'] ?? '',
                        'app.jira_api_token' => $jiraSettings['jira_api_token'] ?? '',
                        'app.jira_project_key' => $jiraSettings['jira_project_key'] ?? 'TEST',
                        'app.jira_issue_type' => $jiraSettings['jira_issue_type'] ?? 'Bug',
                        'app.jira_priority' => $jiraSettings['jira_priority'] ?? 'Medium'
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silent fail - JIRA settings are optional
        }
    }
}
