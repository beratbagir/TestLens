@extends('back.layouts.pages-layout')
@section('pageTitle', 'JIRA Taskları')

@push('stylesheets')
<style>
    /* JIRA Task Card Styling */
    .jira-task-card {
        border: none;
        border-radius: 12px;
        background: rgba(0, 82, 204, 0.1);
        backdrop-filter: blur(4px);
        box-shadow: 0 4px 20px rgba(76, 81, 191, 0.12);
        transition: all 0.2s ease;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .jira-task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(76, 81, 191, 0.18);
    }

    .task-header {
        background: linear-gradient(135deg, #0052cc 0%, #0065ff 100%);
        color: white;
        padding: 16px 20px;
        position: relative;
        overflow: hidden;
    }

    .task-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 60px;
        height: 60px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        transform: translate(15px, -15px);
    }

    .task-key {
        font-size: 14px;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.2);
        padding: 4px 12px;
        border-radius: 16px;
        display: inline-block;
        margin-bottom: 8px;
    }

    .task-summary {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        line-height: 1.4;
    }

    .task-body {
        padding: 20px;
    }

    .task-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
        align-items: center;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #64748b;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 16px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-todo { background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: white; }
    .status-progress { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; }
    .status-done { background: linear-gradient(135deg, #10b981 0%, #047857 100%); color: white; }
    .status-review { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }

    .priority-badge {
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .priority-highest { background: black; color: #dc2626; }
    .priority-high { background: black; color: #d97706; }
    .priority-medium { background: black; color: #2563eb; }
    .priority-low { background: black; color: #059669; }
    .priority-lowest { background: black; color: #6b7280; }

    .task-description {
        background: rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        padding: 16px;
        margin: 16px 0;
        font-size: 14px;
        line-height: 1.6;
        color: #475569;
        max-height: 150px;
        overflow-y: auto;
    }

    .task-actions {
        display: flex;
        gap: 8px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e2e8f0;
    }

    .btn-jira {
        background: linear-gradient(135deg, #0052cc 0%, #0065ff 100%);
        border: none;
        border-radius: 6px;
        padding: 8px 16px;
        font-weight: 600;
        transition: all 0.15s ease;
        color: white;
        font-size: 13px;
    }

    .btn-jira:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 82, 204, 0.3);
        color: white;
    }

    .btn-jira-outline {
        background: transparent;
        border: 2px solid #0052cc;
        color: #0052cc;
    }

    .btn-jira-outline:hover {
        background: #0052cc;
        color: white;
    }

    /* Comment Section */
    .comment-section {
        background: #f8fafc;
        border-radius: 8px;
        padding: 16px;
        margin-top: 16px;
        display: none;
    }

    .comment-form textarea {
        border-radius: 6px;
        border: 1px solid #d1d5db;
        padding: 12px;
        resize: vertical;
        min-height: 80px;
    }

    .comment-form textarea:focus {
        border-color: #0052cc;
        box-shadow: 0 0 0 3px rgba(0, 82, 204, 0.1);
        outline: none;
    }

    /* Create Issue Modal */
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        background: linear-gradient(135deg, #0052cc 0%, #0065ff 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        border-bottom: none;
    }

    .modal-title {
        font-weight: 700;
    }

    .btn-close {
        filter: invert(1);
    }

    /* Filters */
    .filter-section {
        background: rgba(0, 0, 0, 0.15);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(76, 81, 191, 0.08);
    }

    .jql-input {
        border-radius: 6px;
        border: 1px solid #d1d5db;
        padding: 8px 12px;
        font-family: 'Monaco', 'Menlo', monospace;
        font-size: 13px;
    }

    .jql-input:focus {
        border-color: #0052cc;
        box-shadow: 0 0 0 3px rgba(0, 82, 204, 0.1);
        outline: none;
    }

    /* Loading State */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        backdrop-filter: blur(2px);
        z-index: 1000;
    }

    .spinner-border {
        width: 2rem;
        height: 2rem;
        color: #0052cc;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }

    .empty-state i {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 20px;
    }

    .empty-state h5 {
        color: #475569;
        margin-bottom: 12px;
    }

    .empty-state p {
        margin-bottom: 0;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold">
                        <i class="fab fa-jira text-primary me-2"></i>
                        JIRA Taskları
                    </h2>
                    <p class="text-muted">JIRA projelerinizden taskları görüntüleyin ve yönetin</p>
                </div>
                <div>
                    <button type="button" class="btn btn-jira" data-bs-toggle="modal" data-bs-target="#createIssueModal">
                        <i class="fas fa-plus me-2"></i>
                        Yeni Issue Oluştur
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (isset($error))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ $error }}
            <a href="{{ route('settings.index') }}" class="btn btn-outline-primary btn-sm ms-3">
                <i class="fas fa-cog me-1"></i>
                Ayarlara Git
            </a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (!isset($error))
        <!-- Filters -->
        <div class="filter-section">
            <div class="row align-items-end">
                <div class="col-md-8">
                    <label class="form-label fw-bold">
                        <i class="fas fa-search me-2"></i>
                        JQL Sorgusu
                    </label>
                    <input type="text" class="form-control jql-input" id="jqlInput" 
                           value="assignee = currentUser() AND resolution = Unresolved ORDER BY updated DESC"
                           placeholder="assignee = currentUser() AND resolution = Unresolved">
                    <div class="form-text">
                        <small class="text-muted">
                            JIRA Query Language (JQL) kullanarak taskları filtreleyin
                        </small>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-jira" id="filterTasks">
                        <i class="fas fa-filter me-2"></i>
                        Taskları Filtrele
                    </button>
                    <button type="button" class="btn btn-jira-outline ms-2" id="refreshTasks">
                        <i class="fas fa-sync-alt me-2"></i>
                        Yenile
                    </button>
                </div>
            </div>
        </div>

        <!-- Tasks Container -->
        <div id="tasksContainer" class="position-relative">
            @if (empty($tasks))
                <div class="empty-state">
                    <i class="fab fa-jira"></i>
                    <h5>Henüz task bulunamadı</h5>
                    <p>JIRA projenizde size atanmış açık task bulunmuyor veya filtrenizi güncelleyin.</p>
                </div>
            @else
                <div class="row">
                    @foreach ($tasks as $task)
                        <div class="col-12">
                            <div class="jira-task-card" data-task-key="{{ $task['key'] }}">
                                <div class="task-header">
                                    <div class="task-key">{{ $task['key'] }}</div>
                                    <h6 class="task-summary">{{ $task['fields']['summary'] }}</h6>
                                </div>
                                <div class="task-body">
                                    <div class="task-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-flag"></i>
                                            <span class="status-badge status-{{ strtolower(str_replace(' ', '', $task['fields']['status']['name'])) }}">
                                                {{ $task['fields']['status']['name'] }}
                                            </span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span class="priority-badge priority-{{ strtolower($task['fields']['priority']['name']) }}">
                                                {{ $task['fields']['priority']['name'] }}
                                            </span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-user"></i>
                                            <span>{{ $task['fields']['assignee']['displayName'] ?? 'Atanmamış' }}</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-project-diagram"></i>
                                            <span>{{ $task['fields']['project']['name'] }}</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-calendar"></i>
                                            <span>{{ date('d.m.Y H:i', strtotime($task['fields']['updated'])) }}</span>
                                        </div>
                                    </div>

                                    @if (!empty($task['fields']['description']))
                                        <div class="task-description">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($task['fields']['description']), 300) }}
                                        </div>
                                    @endif

                                    <div class="task-actions">
                                        <a href="{{ $settings['jira_url'] ?? '#' }}/browse/{{ $task['key'] }}" 
                                           target="_blank" class="btn btn-jira btn-sm">
                                            <i class="fas fa-external-link-alt me-1"></i>
                                            JIRA'da Aç
                                        </a>
                                        <button type="button" class="btn btn-jira-outline btn-sm toggle-comment" 
                                                data-task-key="{{ $task['key'] }}">
                                            <i class="fas fa-comment me-1"></i>
                                            Yorum Ekle
                                        </button>
                                    </div>

                                    <!-- Comment Section -->
                                    <div class="comment-section" id="comment-{{ $task['key'] }}">
                                        <form class="comment-form" data-task-key="{{ $task['key'] }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Yorum</label>
                                                <textarea name="comment" class="form-control" rows="3" 
                                                          placeholder="Bu task ile ilgili yorumunuzu yazın..."></textarea>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-jira btn-sm">
                                                    <i class="fas fa-save me-1"></i>
                                                    Yorumu Kaydet
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-sm cancel-comment">
                                                    İptal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Create Issue Modal -->
<div class="modal fade" id="createIssueModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Yeni JIRA Issue Oluştur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createIssueForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Başlık (Summary)</label>
                            <input type="text" name="summary" class="form-control" required 
                                   placeholder="Issue başlığını girin">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Proje Anahtarı</label>
                            <input type="text" name="project_key" class="form-control" 
                                   value="{{ $settings['jira_project_key'] ?? 'TEST' }}" 
                                   style="text-transform: uppercase;">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Issue Tipi</label>
                            <select name="issue_type" class="form-select">
                                <option value="Bug" {{ ($settings['jira_issue_type'] ?? '') == 'Bug' ? 'selected' : '' }}>Bug</option>
                                <option value="Task" {{ ($settings['jira_issue_type'] ?? '') == 'Task' ? 'selected' : '' }}>Task</option>
                                <option value="Story" {{ ($settings['jira_issue_type'] ?? '') == 'Story' ? 'selected' : '' }}>Story</option>
                                <option value="Test" {{ ($settings['jira_issue_type'] ?? '') == 'Test' ? 'selected' : '' }}>Test</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Öncelik</label>
                            <select name="priority" class="form-select">
                                <option value="Highest" {{ ($settings['jira_priority'] ?? '') == 'Highest' ? 'selected' : '' }}>Highest</option>
                                <option value="High" {{ ($settings['jira_priority'] ?? '') == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Medium" {{ ($settings['jira_priority'] ?? '') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="Low" {{ ($settings['jira_priority'] ?? '') == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Lowest" {{ ($settings['jira_priority'] ?? '') == 'Lowest' ? 'selected' : '' }}>Lowest</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Açıklama (Description)</label>
                        <textarea name="description" class="form-control" rows="6" required
                                  placeholder="Issue açıklamasını detaylı olarak yazın..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="submit" class="btn btn-jira" form="createIssueForm">
                    <i class="fas fa-save me-1"></i>
                    Issue Oluştur
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle comment section
    document.querySelectorAll('.toggle-comment').forEach(btn => {
        btn.addEventListener('click', function() {
            const taskKey = this.dataset.taskKey;
            const commentSection = document.getElementById('comment-' + taskKey);
            
            if (commentSection.style.display === 'none' || !commentSection.style.display) {
                commentSection.style.display = 'block';
                this.innerHTML = '<i class="fas fa-times me-1"></i>İptal';
            } else {
                commentSection.style.display = 'none';
                this.innerHTML = '<i class="fas fa-comment me-1"></i>Yorum Ekle';
            }
        });
    });

    // Cancel comment
    document.querySelectorAll('.cancel-comment').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.comment-section');
            const taskKey = form.id.replace('comment-', '');
            const toggleBtn = document.querySelector(`[data-task-key="${taskKey}"]`);
            
            form.style.display = 'none';
            toggleBtn.innerHTML = '<i class="fas fa-comment me-1"></i>Yorum Ekle';
            form.querySelector('textarea').value = '';
        });
    });

    // Submit comment
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const taskKey = this.dataset.taskKey;
            const comment = this.querySelector('textarea').value.trim();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            if (!comment) {
                alert('Lütfen bir yorum yazın!');
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Kaydediliyor...';
            
            try {
                const response = await fetch(`/jira-tasks/${taskKey}/comment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ comment: comment })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    this.querySelector('textarea').value = '';
                    this.closest('.comment-section').style.display = 'none';
                    document.querySelector(`[data-task-key="${taskKey}"]`).innerHTML = '<i class="fas fa-comment me-1"></i>Yorum Ekle';
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showAlert('danger', 'Yorum eklenirken hata oluştu: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    });

    // Create issue form
    document.getElementById('createIssueForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = document.querySelector('#createIssueModal button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Oluşturuluyor...';
        
        try {
            const response = await fetch('/jira-tasks/create-issue', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert('success', `Issue başarıyla oluşturuldu: ${result.issue_key}`);
                bootstrap.Modal.getInstance(document.getElementById('createIssueModal')).hide();
                this.reset();
                // Refresh tasks
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            showAlert('danger', 'Issue oluşturulurken hata oluştu: ' + error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Filter tasks
    document.getElementById('filterTasks').addEventListener('click', async function() {
        const jql = document.getElementById('jqlInput').value.trim();
        await loadTasks(jql);
    });

    // Refresh tasks
    document.getElementById('refreshTasks').addEventListener('click', async function() {
        const jql = document.getElementById('jqlInput').value.trim();
        await loadTasks(jql);
    });

    // JQL input enter key
    document.getElementById('jqlInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('filterTasks').click();
        }
    });
});

async function loadTasks(jql = '') {
    const container = document.getElementById('tasksContainer');
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = '<div class="spinner-border" role="status"></div>';
    
    container.appendChild(loadingOverlay);
    
    try {
        const response = await fetch('/jira-tasks/fetch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ jql: jql })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Reload page to show new tasks
            location.reload();
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        showAlert('danger', 'Tasklar yüklenirken hata oluştu: ' + error.message);
    } finally {
        container.removeChild(loadingOverlay);
    }
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container');
    const firstChild = container.querySelector('.row');
    container.insertBefore(alertDiv, firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endsection
