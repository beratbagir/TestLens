<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class JiraController extends Controller
{
    public function index()
    {
        $settings = $this->getJiraSettings();
        
        if (!$this->isJiraConfigured($settings)) {
            return view('jira.index', [
                'tasks' => [],
                'error' => 'JIRA ayarları eksik. Lütfen ayarlar sayfasından JIRA entegrasyonunu yapılandırın.'
            ]);
        }

        try {
            $tasks = $this->fetchJiraTasks($settings);
            return view('jira.index', [
                'tasks' => $tasks,
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            return view('jira.index', [
                'tasks' => [],
                'error' => 'JIRA taskları alınırken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }

    public function fetchTasks(Request $request)
    {
        $settings = $this->getJiraSettings();
        
        if (!$this->isJiraConfigured($settings)) {
            return response()->json([
                'success' => false,
                'message' => 'JIRA ayarları eksik'
            ], 400);
        }

        try {
            $jql = $request->get('jql', 'assignee = currentUser() AND resolution = Unresolved ORDER BY updated DESC');
            $tasks = $this->fetchJiraTasks($settings, $jql);
            
            return response()->json([
                'success' => true,
                'tasks' => $tasks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function addComment(Request $request, $issueKey)
    {
        $request->validate([
            'comment' => 'required|string|min:1'
        ]);

        $settings = $this->getJiraSettings();
        
        if (!$this->isJiraConfigured($settings)) {
            return response()->json([
                'success' => false,
                'message' => 'JIRA ayarları eksik'
            ], 400);
        }

        try {
            $response = Http::withBasicAuth($settings['jira_username'], $settings['jira_api_token'])
                ->post($settings['jira_url'] . '/rest/api/2/issue/' . $issueKey . '/comment', [
                    'body' => $request->comment
                ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Yorum başarıyla eklendi'
                ]);
            } else {
                throw new \Exception('JIRA API hatası: ' . $response->body());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createIssue(Request $request)
    {
        $request->validate([
            'summary' => 'required|string|max:255',
            'description' => 'required|string',
            'issue_type' => 'sometimes|string',
            'priority' => 'sometimes|string',
            'project_key' => 'sometimes|string'
        ]);

        $settings = $this->getJiraSettings();
        
        if (!$this->isJiraConfigured($settings)) {
            return response()->json([
                'success' => false,
                'message' => 'JIRA ayarları eksik'
            ], 400);
        }

        try {
            $issueData = [
                'fields' => [
                    'project' => [
                        'key' => $request->get('project_key', $settings['jira_project_key'])
                    ],
                    'summary' => $request->summary,
                    'description' => $request->description,
                    'issuetype' => [
                        'name' => $request->get('issue_type', $settings['jira_issue_type'])
                    ],
                    'priority' => [
                        'name' => $request->get('priority', $settings['jira_priority'])
                    ]
                ]
            ];

            $response = Http::withBasicAuth($settings['jira_username'], $settings['jira_api_token'])
                ->post($settings['jira_url'] . '/rest/api/2/issue', $issueData);

            if ($response->successful()) {
                $issue = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => 'Issue başarıyla oluşturuldu',
                    'issue_key' => $issue['key'],
                    'issue_url' => $settings['jira_url'] . '/browse/' . $issue['key']
                ]);
            } else {
                throw new \Exception('JIRA API hatası: ' . $response->body());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function testConnection(Request $request)
    {
        $request->validate([
            'jira_url' => 'required|url',
            'jira_username' => 'required|string',
            'jira_api_token' => 'required|string'
        ]);

        try {
            $response = Http::timeout(10)
                ->withBasicAuth($request->jira_username, $request->jira_api_token)
                ->get($request->jira_url . '/rest/api/2/myself');

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
        return [
            'jira_url' => config('app.jira_url'),
            'jira_username' => config('app.jira_username'),
            'jira_api_token' => config('app.jira_api_token'),
            'jira_project_key' => config('app.jira_project_key', 'TEST'),
            'jira_issue_type' => config('app.jira_issue_type', 'Bug'),
            'jira_priority' => config('app.jira_priority', 'Medium')
        ];
    }

    private function isJiraConfigured($settings)
    {
        return !empty($settings['jira_url']) && 
               !empty($settings['jira_username']) && 
               !empty($settings['jira_api_token']);
    }

    private function fetchJiraTasks($settings, $jql = null)
    {
        $defaultJql = 'assignee = currentUser() AND resolution = Unresolved ORDER BY updated DESC';
        $jql = $jql ?: $defaultJql;
        
        $cacheKey = 'jira_tasks_' . md5($jql . $settings['jira_username']);
        
        return Cache::remember($cacheKey, 300, function () use ($settings, $jql) {
            $response = Http::timeout(30)
                ->withBasicAuth($settings['jira_username'], $settings['jira_api_token'])
                ->get($settings['jira_url'] . '/rest/api/2/search', [
                    'jql' => $jql,
                    'maxResults' => 50,
                    'fields' => 'summary,status,priority,assignee,reporter,created,updated,description,issuetype,project'
                ]);

            if (!$response->successful()) {
                throw new \Exception('JIRA API hatası: ' . $response->body());
            }

            $data = $response->json();
            return $data['issues'] ?? [];
        });
    }
}
