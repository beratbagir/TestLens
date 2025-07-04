<!doctype html>
<html lang="tr">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('pageTitle', 'QA Yönetim Paneli')</title>

    <!-- CSS Files -->
    <link href="{{ asset('back/dist/css/tabler.min.css')}}" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"/>
    @stack('stylesheets')
    
    <style>
      /* CSS Değişkenleri - Kapsamlı Dark Theme Color Palette */
      :root {
        /* Ana Renkler */
        --primary-color: #3b82f6;
        --primary-hover: #2563eb;
        --primary-light: #60a5fa;
        --secondary-color: #1d4ed8;
        --accent-color: #f59e0b;
        --accent-light: #fbbf24;
        --accent-hover: #d97706;
        
        /* Gradyan Efektleri */
        --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        --accent-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --success-gradient: linear-gradient(135deg, #10b981 0%, #047857 100%);
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
        --danger-gradient: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        --info-gradient: linear-gradient(135deg, #06b6d4 0%, #0369a1 100%);
        
        /* Arka Plan Renkleri */
        --bg-dark: #0f172a;
        --bg-darker: #020617;
        --bg-darkest: #000000;
        --light-bg: rgba(30, 41, 59, 0.95);
        --glass-bg: rgba(30, 41, 59, 0.9);
        --surface-1: #1e293b;
        --surface-2: #334155;
        --surface-3: #475569;
        --surface-4: #64748b;
        
        /* Metin Renkleri */
        --text-primary: #f8fafc;
        --text-secondary: #e2e8f0;
        --text-muted: #94a3b8;
        --text-light: #cbd5e1;
        --text-dark: #475569;
        --text-darker: #334155;
        
        /* Border ve Shadow */
        --border-primary: rgba(59, 130, 246, 0.3);
        --border-light: rgba(59, 130, 246, 0.2);
        --border-lighter: rgba(59, 130, 246, 0.1);
        --border-accent: rgba(245, 158, 11, 0.3);
        --shadow-light: 0 4px 16px rgba(0, 0, 0, 0.3);
        --shadow-medium: 0 8px 32px rgba(0, 0, 0, 0.4);
        --shadow-heavy: 0 12px 48px rgba(0, 0, 0, 0.5);
        --shadow-hover: 0 8px 32px rgba(59, 130, 246, 0.2);
        --shadow-accent: 0 8px 32px rgba(245, 158, 11, 0.2);
        
        /* Status Renkleri */
        --success-color: #10b981;
        --success-light: #34d399;
        --success-dark: #047857;
        --warning-color: #f59e0b;
        --warning-light: #fbbf24;
        --warning-dark: #b45309;
        --danger-color: #ef4444;
        --danger-light: #f87171;
        --danger-dark: #b91c1c;
        --info-color: #06b6d4;
        --info-light: #22d3ee;
        --info-dark: #0369a1;
        
        /* Opacity Values */
        --opacity-10: rgba(255, 255, 255, 0.1);
        --opacity-20: rgba(255, 255, 255, 0.2);
        --opacity-30: rgba(255, 255, 255, 0.3);
        --opacity-50: rgba(255, 255, 255, 0.5);
        
        /* Transitions */
        --transition-fast: all 0.15s ease;
        --transition-normal: all 0.3s ease;
        --transition-slow: all 0.5s ease;
        --transition-bounce: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      }

      /* Genel Ayarlar - Global Dark Theme */
      * {
        box-sizing: border-box;
      }

      body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-darker) 100%);
        min-height: 100vh;
        color: var(--text-primary) !important;
        line-height: 1.6;
        font-weight: 400;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        margin: 0;
        padding: 0;
      }

      /* Global Text Color Override */
      * {
        color: var(--text-primary) !important;
      }

      /* Specific Text Overrides */
      .text-primary { color: var(--primary-color) !important; }
      .text-secondary { color: var(--text-secondary) !important; }
      .text-success { color: var(--success-color) !important; }
      .text-warning { color: var(--warning-color) !important; }
      .text-danger { color: var(--danger-color) !important; }
      .text-info { color: var(--info-color) !important; }
      .text-muted { color: var(--text-muted) !important; }
      .text-dark { color: var(--text-darker) !important; }
      .text-white { color: white !important; }

      /* HTML Element Overrides */
      h1, h2, h3, h4, h5, h6,
      p, span, div, a, label, 
      small, strong, em, i, b,
      td, th, li, ul, ol {
        color: var(--text-primary) !important;
      }

      /* Icon renkleri */
      .fas, .far, .fab, .fal,
      .icon, i[class*="fa-"] {
        color: var(--text-primary) !important;
      }

      /* Navbar icon renkleri */
      .nav-link .fas,
      .nav-link .far,
      .nav-link i {
        color: var(--text-primary) !important;
      }

      .nav-link:hover .fas,
      .nav-link:hover .far,
      .nav-link:hover i {
        color: white !important;
      }

      /* Container Styling - Dark Glass Effect */
      .container, .container-fluid {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.85) 100%);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 20px;
        padding: 32px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-light);
        backdrop-filter: blur(12px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
      }

      .container::before, .container-fluid::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--accent-gradient);
        opacity: 0.8;
      }

      .container:hover, .container-fluid:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-hover);
        border-color: rgba(59, 130, 246, 0.3);
      }

      /* Text renkleri - Light on dark */
      .container, .container-fluid {
        color: var(--text-primary) !important;
      }

      .container h1, .container h2, .container h3, .container h4, .container h5, .container h6,
      .container-fluid h1, .container-fluid h2, .container-fluid h3, .container-fluid h4, .container-fluid h5, .container-fluid h6 {
        color: var(--text-primary) !important;
        font-weight: 700;
      }

      .container .text-muted, .container-fluid .text-muted {
        color: var(--text-muted) !important;
      }

      .container p, .container span, .container div, .container label, .container small,
      .container-fluid p, .container-fluid span, .container-fluid div, .container-fluid label, .container-fluid small {
        color: var(--text-primary) !important;
      }

      /* Override Bootstrap classes */
      .text-primary { color: var(--primary-color) !important; }
      .text-secondary { color: var(--text-secondary) !important; }
      .text-success { color: var(--success-color) !important; }
      .text-warning { color: var(--warning-color) !important; }
      .text-danger { color: var(--danger-color) !important; }
      .text-info { color: var(--info-color) !important; }
      .text-muted { color: var(--text-muted) !important; }

      .page {
        background: transparent;
      }

      /* Navbar - Sadeleştirilmiş ve yüksek kontrast */
      .navbar {
        background: var(--light-bg);
        backdrop-filter: blur(4px);
        border-bottom: 1px solid var(--border-light);
        box-shadow: var(--shadow-light);
        padding: 12px 0;
      }

      .navbar-brand {
        margin-right: 0;
        padding: 0;
      }

      /* Navigation Links - Yüksek kontrast ve performans odaklı */
      .nav-link {
        color: var(--text-primary) !important;
        font-weight: 600;
        padding: 12px 20px !important;
        border-radius: 8px;
        margin: 0 3px;
        transition: all 0.15s ease;
        background: transparent;
      }

      .nav-link:hover {
        color: white !important;
        background: var(--primary-gradient);
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(76, 81, 191, 0.3);
      }

      .nav-link-icon {
        margin-right: 8px;
        font-size: 1.1em;
      }

      .nav-link-title {
        font-weight: 600;
      }

      /* Page Wrapper */
      .page-wrapper {
        background: transparent;
        padding-top: 20px;
      }

      .page-body {
        background: transparent;
      }

      /* Container */
      .container-xl {
        position: relative;
      }

      /* Card Improvements - Yüksek kontrast ve sadeleştirilmiş */
      .card {
        border: none;
        border-radius: 12px;
        box-shadow: var(--shadow-light);
        background: var(--light-bg);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        color: var(--text-primary);
      }

      .card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
      }

      /* Card Styling - Dark Theme */
      .card {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.85) 100%) !important;
        border: 1px solid rgba(59, 130, 246, 0.2) !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
        backdrop-filter: blur(12px) !important;
        color: var(--text-primary) !important;
      }

      .card-header {
        background: var(--primary-gradient) !important;
        border-bottom: 1px solid rgba(59, 130, 246, 0.2) !important;
        color: white !important;
        border-radius: 16px 16px 0 0 !important;
      }

      .card-body {
        color: var(--text-primary) !important;
      }

      .card-title {
        color: var(--text-primary) !important;
        font-weight: 700 !important;
      }

      .card-text {
        color: var(--text-secondary) !important;
      }

      /* Override Bootstrap card styles */
      .card * {
        color: var(--text-primary) !important;
      }

      .card .text-muted {
        color: var(--text-muted) !important;
      }

      .card .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
      }

      /* Button Improvements - Yüksek kontrast ve performans odaklı */
      .btn-primary, .btn-custom {
        background: var(--primary-gradient);
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        font-weight: 700;
        color: white;
        font-size: 14px;
        transition: all 0.15s ease;
        box-shadow: 0 2px 6px rgba(76, 81, 191, 0.25);
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
      }

      .btn-primary:hover, .btn-custom:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(76, 81, 191, 0.35);
        color: white;
        filter: brightness(1.05);
      }

      .btn-success {
        background: linear-gradient(135deg, var(--success-color) 0%, #047857 100%);
        border: none;
        border-radius: 8px;
        font-weight: 700;
        color: white;
        transition: all 0.15s ease;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
      }

      .btn-warning {
        background: linear-gradient(135deg, var(--warning-color) 0%, #b45309 100%);
        border: none;
        border-radius: 8px;
        font-weight: 700;
        color: white;
        transition: all 0.15s ease;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
      }

      .btn-danger {
        background: linear-gradient(135deg, var(--danger-color) 0%, #b91c1c 100%);
        border: none;
        border-radius: 8px;
        font-weight: 700;
        color: white;
        transition: all 0.15s ease;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
      }

      .btn-info {
        background: linear-gradient(135deg, var(--info-color) 0%, #0369a1 100%);
        border: none;
        border-radius: 8px;
        font-weight: 700;
        color: white;
        transition: all 0.15s ease;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
      }

      /* Form Controls - Dark Theme */
      .form-control, .form-select {
        border-radius: 8px;
        border: 2px solid var(--border-light);
        padding: 12px 16px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(51, 65, 85, 0.7) 100%);
        color: var(--text-primary) !important;
        font-weight: 500;
        font-size: 14px;
      }

      .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.8) 100%);
        color: var(--text-primary) !important;
        outline: none;
      }

      .form-control::placeholder {
        color: var(--text-muted) !important;
        opacity: 0.8;
        font-weight: 400;
      }

      /* Label improvements - Dark Theme */
      .form-label {
        color: var(--text-primary) !important;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 14px;
      }

      /* Alert Styling - Dark Theme */
      .alert {
        border: none;
        border-radius: 8px;
        padding: 16px 20px;
        margin-bottom: 20px;
        font-weight: 600;
        font-size: 14px;
        border: 1px solid rgba(59, 130, 246, 0.2);
      }

      .alert-success {
        background: linear-gradient(135deg, rgba(5, 150, 105, 0.15) 0%, rgba(16, 185, 129, 0.1) 100%);
        color: var(--text-primary) !important;
        border-left: 4px solid var(--success-color);
      }

      .alert-danger {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.1) 100%);
        color: var(--text-primary) !important;
        border-left: 4px solid var(--danger-color);
      }

      .alert-warning {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(217, 119, 6, 0.1) 100%);
        color: var(--text-primary) !important;
        border-left: 4px solid var(--warning-color);
      }

      .alert-info {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 100%);
        color: var(--text-primary) !important;
        border-left: 4px solid var(--info-color);
      }

      /* Badge Styling - Yüksek kontrast */
      .badge {
        border-radius: 12px;
        padding: 6px 12px;
        font-weight: 700;
        font-size: 12px;
        color: white;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
      }

      .badge-custom, .bg-success {
        background: linear-gradient(135deg, var(--success-color) 0%, #047857 100%) !important;
        color: white !important;
      }

      .bg-primary {
        background: var(--primary-gradient) !important;
        color: white !important;
      }

      .bg-warning {
        background: linear-gradient(135deg, var(--warning-color) 0%, #b45309 100%) !important;
        color: white !important;
      }

      .bg-danger {
        background: linear-gradient(135deg, var(--danger-color) 0%, #b91c1c 100%) !important;
        color: white !important;
      }

      .bg-info {
        background: linear-gradient(135deg, var(--info-color) 0%, #0369a1 100%) !important;
        color: white !important;
      }

      /* Progress Bar */
      .progress {
        border-radius: 12px;
        background: rgba(102, 126, 234, 0.1);
        height: 8px;
      }

      .progress-bar {
        background: var(--primary-gradient);
        border-radius: 12px;
      }

      /* Table Styling - Dark Theme Enhanced */
      .table {
        background: var(--glass-bg) !important;
        border-radius: 12px !important;
        overflow: hidden !important;
        color: var(--text-primary) !important;
        box-shadow: var(--shadow-light) !important;
        border: 1px solid var(--border-light) !important;
        margin-bottom: 0 !important;
      }

      .table thead th {
        background: var(--primary-gradient) !important;
        color: white !important;
        border: none !important;
        font-weight: 700 !important;
        padding: 16px 20px !important;
        font-size: 14px !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;
        position: relative !important;
      }

      .table tbody td {
        padding: 16px 20px !important;
        border-color: var(--border-light) !important;
        color: var(--text-primary) !important;
        font-weight: 500 !important;
        font-size: 14px !important;
        background: transparent !important;
      }

      .table tbody tr {
        background: transparent !important;
        transition: var(--transition-fast) !important;
      }

      .table tbody tr:hover {
        background: rgba(59, 130, 246, 0.1) !important;
        transform: translateY(-1px) !important;
      }

      .table tbody tr:nth-child(even) {
        background: rgba(59, 130, 246, 0.05) !important;
      }

      .table tbody tr:nth-child(even):hover {
        background: rgba(59, 130, 246, 0.15) !important;
      }

      /* Table responsive wrapper */
      .table-responsive {
        background: transparent !important;
        border-radius: 12px !important;
        overflow: hidden !important;
      }

      /* Modal Dark Theme - Kapsamlı */
      .modal-content {
        border: none !important;
        border-radius: 16px !important;
        background: var(--glass-bg) !important;
        color: var(--text-primary) !important;
        box-shadow: var(--shadow-heavy) !important;
        backdrop-filter: blur(12px) !important;
        border: 1px solid var(--border-light) !important;
      }

      .modal-header {
        background: var(--primary-gradient) !important;
        color: white !important;
        border-radius: 16px 16px 0 0 !important;
        border-bottom: 1px solid var(--border-light) !important;
        padding: 20px 24px !important;
      }

      .modal-title {
        color: white !important;
        font-weight: 700 !important;
        font-size: 1.25rem !important;
      }

      .modal-body {
        color: var(--text-primary) !important;
        padding: 24px !important;
        background: transparent !important;
      }

      .modal-body * {
        color: var(--text-primary) !important;
      }

      .modal-body .form-label {
        color: var(--text-primary) !important;
        font-weight: 600 !important;
        margin-bottom: 8px !important;
      }

      .modal-body .form-control,
      .modal-body .form-select,
      .modal-body textarea {
        background: var(--surface-1) !important;
        border: 2px solid var(--border-light) !important;
        color: var(--text-primary) !important;
        border-radius: 8px !important;
        padding: 12px 16px !important;
        transition: var(--transition-fast) !important;
      }

      .modal-body .form-control:focus,
      .modal-body .form-select:focus,
      .modal-body textarea:focus {
        background: var(--surface-2) !important;
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
        color: var(--text-primary) !important;
        outline: none !important;
      }

      .modal-body .form-control::placeholder,
      .modal-body textarea::placeholder {
        color: var(--text-muted) !important;
        opacity: 0.8 !important;
      }

      .modal-footer {
        border-top: 1px solid var(--border-light) !important;
        background: rgba(30, 41, 59, 0.3) !important;
        border-radius: 0 0 16px 16px !important;
        padding: 16px 24px !important;
      }

      .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.7) !important;
        backdrop-filter: blur(2px) !important;
      }

      .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%) !important;
        opacity: 0.8 !important;
      }

      .btn-close:hover {
        opacity: 1 !important;
      }

      /* Form Check Elements in Modals */
      .modal-body .form-check-input {
        background-color: var(--surface-1) !important;
        border: 2px solid var(--border-light) !important;
        border-radius: 4px !important;
      }

      .modal-body .form-check-input:checked {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
      }

      .modal-body .form-check-input:focus {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
      }

      .modal-body .form-check-label {
        color: var(--text-primary) !important;
        font-weight: 500 !important;
      }

      /* Scrollbar - Sadeleştirilmiş ve yüksek kontrast */
      ::-webkit-scrollbar {
        width: 8px;
      }

      ::-webkit-scrollbar-track {
        background: rgba(76, 81, 191, 0.08);
        border-radius: 4px;
      }

      ::-webkit-scrollbar-thumb {
        background: var(--primary-gradient);
        border-radius: 4px;
      }

      ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #553c9a 0%, #4c51bf 100%);
      }

      /* Text Colors - Yüksek kontrast ve okunabilirlik */
      .text-primary {
        color: var(--primary-color) !important;
      }

      .text-secondary {
        color: var(--text-secondary) !important;
      }

      .text-muted {
        color: var(--text-muted) !important;
      }

      .text-success {
        color: #065f46 !important;
        font-weight: 600;
      }

      .text-warning {
        color: #92400e !important;
        font-weight: 600;
      }

      .text-danger {
        color: #991b1b !important;
        font-weight: 600;
      }

      .text-info {
        color: #075985 !important;
        font-weight: 600;
      }

      /* Titles ve Headers - Yüksek kontrast */
      h1, h2, h3, h4, h5, h6 {
        color: var(--text-primary);
        font-weight: 700;
        line-height: 1.3;
      }

      .fw-bold {
        color: var(--text-primary) !important;
        font-weight: 800 !important;
      }

      /* Card Headers - Yüksek kontrast */
      .card-header {
        background: var(--primary-gradient);
        color: white;
        border-bottom: none;
        font-weight: 700;
        padding: 16px 20px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
      }

      .card-body {
        color: var(--text-primary);
        padding: 20px;
      }

      .card-title {
        color: var(--text-primary);
        font-weight: 700;
        margin-bottom: 12px;
      }

      .card-text {
        color: var(--text-secondary);
        font-weight: 500;
        line-height: 1.6;
      }

      /* Performance optimizations - Sadeleştirilmiş arka plan */
      .page::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
          radial-gradient(circle at 20% 50%, rgba(76, 81, 191, 0.08) 0%, transparent 50%),
          radial-gradient(circle at 80% 20%, rgba(85, 60, 154, 0.08) 0%, transparent 50%);
        z-index: -1;
        pointer-events: none;
      }

      /* Links - Yüksek kontrast */
      a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.15s ease;
      }

      a:hover {
        color: var(--secondary-color);
        text-decoration: none;
      }

      /* Small text - Yüksek kontrast ve okunabilirlik */
      small, .small {
        color: var(--text-muted);
        font-size: 0.875rem;
        font-weight: 500;
      }

      /* List items - Yüksek kontrast */
      .list-group-item {
        background: white;
        color: var(--text-primary);
        border-color: var(--border-light);
        font-weight: 500;
      }

      /* Input groups - Yüksek kontrast */
      .input-group-text {
        background: var(--light-bg);
        border-color: var(--border-light);
        color: var(--text-primary);
        font-weight: 600;
      }

      /* Logo Styling - Eye Logo */
      .brand-link {
        text-decoration: none !important;
        display: flex;
        align-items: center;
        transition: all 0.15s ease;
      }

      .brand-container {
        display: flex;
        align-items: center;
        gap: 12px;
      }

      .eye-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(4px);
        transition: all 0.15s ease;
      }

      .brand-link:hover .eye-logo {
        background: rgba(255, 255, 255, 0.15);
        transform: scale(1.05);
      }

      .brand-text {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.5px;
        transition: color 0.15s ease;
      }

      .brand-link:hover .brand-text {
        color: var(--primary-color);
      }

      /* Responsive logo */
      @media (max-width: 768px) {
        .brand-text {
          font-size: 1.3rem;
        }
        
        .eye-logo svg {
          width: 32px;
          height: 32px;
        }
      }

      /* Bootstrap Components Dark Theme Override */
      /* Dropdown Menu */
      .dropdown-menu {
        background: var(--glass-bg) !important;
        border: 1px solid var(--border-light) !important;
        border-radius: 12px !important;
        backdrop-filter: blur(12px) !important;
        box-shadow: var(--shadow-medium) !important;
        padding: 8px !important;
      }

      .dropdown-item {
        color: var(--text-primary) !important;
        padding: 10px 16px !important;
        border-radius: 8px !important;
        margin: 2px 0 !important;
        transition: var(--transition-fast) !important;
        font-weight: 500 !important;
      }

      .dropdown-item:hover,
      .dropdown-item:focus {
        background: rgba(59, 130, 246, 0.1) !important;
        color: var(--primary-color) !important;
      }

      .dropdown-item.active {
        background: var(--primary-gradient) !important;
        color: white !important;
      }

      .dropdown-divider {
        border-color: var(--border-light) !important;
        margin: 8px 0 !important;
      }

      /* Pagination */
      .pagination {
        gap: 4px !important;
      }

      .pagination .page-link {
        background: var(--surface-1) !important;
        border: 1px solid var(--border-light) !important;
        color: var(--text-primary) !important;
        padding: 10px 16px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        transition: var(--transition-fast) !important;
        text-decoration: none !important;
      }

      .pagination .page-link:hover {
        background: rgba(59, 130, 246, 0.1) !important;
        border-color: var(--border-primary) !important;
        color: var(--primary-color) !important;
        transform: translateY(-1px) !important;
      }

      .pagination .page-item.active .page-link {
        background: var(--primary-gradient) !important;
        border-color: var(--primary-color) !important;
        color: white !important;
        box-shadow: var(--shadow-hover) !important;
      }

      .pagination .page-item.disabled .page-link {
        background: var(--surface-1) !important;
        border-color: var(--border-lighter) !important;
        color: var(--text-muted) !important;
        opacity: 0.5 !important;
      }

      /* Tooltip */
      .tooltip {
        --bs-tooltip-bg: var(--surface-2) !important;
        --bs-tooltip-color: var(--text-primary) !important;
        --bs-tooltip-border-color: var(--border-light) !important;
      }

      .tooltip-inner {
        background: var(--surface-2) !important;
        color: var(--text-primary) !important;
        border: 1px solid var(--border-light) !important;
        border-radius: 8px !important;
        font-weight: 500 !important;
        padding: 8px 12px !important;
      }

      .tooltip .tooltip-arrow::before {
        border-color: var(--surface-2) !important;
      }

      /* Popover */
      .popover {
        --bs-popover-bg: var(--glass-bg) !important;
        --bs-popover-border-color: var(--border-light) !important;
        backdrop-filter: blur(12px) !important;
      }

      .popover-body {
        color: var(--text-primary) !important;
      }

      .popover-header {
        background: var(--surface-2) !important;
        color: var(--text-primary) !important;
        border-bottom: 1px solid var(--border-light) !important;
        font-weight: 600 !important;
      }

      /* Toast */
      .toast {
        background: var(--glass-bg) !important;
        border: 1px solid var(--border-light) !important;
        color: var(--text-primary) !important;
        backdrop-filter: blur(12px) !important;
      }

      .toast-header {
        background: var(--surface-2) !important;
        color: var(--text-primary) !important;
        border-bottom: 1px solid var(--border-light) !important;
      }

      .toast-body {
        color: var(--text-primary) !important;
      }

      /* Accordion */
      .accordion-item {
        background: var(--glass-bg) !important;
        border: 1px solid var(--border-light) !important;
        margin-bottom: 8px !important;
        border-radius: 12px !important;
        overflow: hidden !important;
      }

      .accordion-header {
        margin-bottom: 0 !important;
      }

      .accordion-button {
        background: var(--surface-1) !important;
        color: var(--text-primary) !important;
        border: none !important;
        font-weight: 600 !important;
        padding: 16px 20px !important;
      }

      .accordion-button:not(.collapsed) {
        background: var(--primary-gradient) !important;
        color: white !important;
        box-shadow: none !important;
      }

      .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
        border-color: var(--primary-color) !important;
      }

      .accordion-body {
        background: var(--glass-bg) !important;
        color: var(--text-primary) !important;
        padding: 20px !important;
      }

      /* List Group */
      .list-group {
        border-radius: 12px !important;
        overflow: hidden !important;
      }

      .list-group-item {
        background: var(--glass-bg) !important;
        color: var(--text-primary) !important;
        border-color: var(--border-light) !important;
        padding: 16px 20px !important;
        transition: var(--transition-fast) !important;
      }

      .list-group-item:hover {
        background: rgba(59, 130, 246, 0.1) !important;
        color: var(--primary-color) !important;
      }

      .list-group-item.active {
        background: var(--primary-gradient) !important;
        border-color: var(--primary-color) !important;
        color: white !important;
      }

      /* Nav Pills & Tabs */
      .nav-pills .nav-link {
        background: var(--surface-1) !important;
        color: var(--text-primary) !important;
        border-radius: 8px !important;
        margin: 0 4px !important;
        padding: 10px 16px !important;
        transition: var(--transition-fast) !important;
        font-weight: 500 !important;
      }

      .nav-pills .nav-link:hover {
        background: rgba(59, 130, 246, 0.1) !important;
        color: var(--primary-color) !important;
      }

      .nav-pills .nav-link.active {
        background: var(--primary-gradient) !important;
        color: white !important;
      }

      .nav-tabs .nav-link {
        color: var(--text-primary) !important;
        border: 1px solid transparent !important;
        border-radius: 8px 8px 0 0 !important;
        padding: 12px 20px !important;
        transition: var(--transition-fast) !important;
        font-weight: 500 !important;
      }

      .nav-tabs .nav-link:hover {
        color: var(--primary-color) !important;
        border-color: var(--border-light) !important;
      }

      .nav-tabs .nav-link.active {
        background: var(--glass-bg) !important;
        color: var(--primary-color) !important;
        border-color: var(--border-light) !important;
        border-bottom-color: transparent !important;
      }

      .tab-content {
        background: var(--glass-bg) !important;
        border: 1px solid var(--border-light) !important;
        border-top: none !important;
        border-radius: 0 0 12px 12px !important;
        padding: 20px !important;
      }

      .tab-pane {
        color: var(--text-primary) !important;
      }

      /* Utility Classes - Dark Theme Enhanced */
      .bg-primary { background: var(--primary-gradient) !important; color: white !important; }
      .bg-secondary { background: var(--surface-2) !important; color: var(--text-primary) !important; }
      .bg-success { background: var(--success-gradient) !important; color: white !important; }
      .bg-warning { background: var(--warning-gradient) !important; color: white !important; }
      .bg-danger { background: var(--danger-gradient) !important; color: white !important; }
      .bg-info { background: var(--info-gradient) !important; color: white !important; }
      .bg-light { background: var(--surface-1) !important; color: var(--text-primary) !important; }
      .bg-dark { background: var(--surface-3) !important; color: var(--text-primary) !important; }

      .border-primary { border-color: var(--border-primary) !important; }
      .border-secondary { border-color: var(--border-light) !important; }
      .border-success { border-color: var(--success-color) !important; }
      .border-warning { border-color: var(--warning-color) !important; }
      .border-danger { border-color: var(--danger-color) !important; }
      .border-info { border-color: var(--info-color) !important; }

      /* Shadow utilities */
      .shadow-sm { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important; }
      .shadow { box-shadow: var(--shadow-light) !important; }
      .shadow-lg { box-shadow: var(--shadow-medium) !important; }
      .shadow-xl { box-shadow: var(--shadow-heavy) !important; }

      /* Input Group Dark Theme */
      .input-group {
        border-radius: 8px !important;
        overflow: hidden !important;
      }

      .input-group .form-control {
        border-radius: 0 !important;
        border-right: none !important;
        background: var(--surface-1) !important;
        color: var(--text-primary) !important;
      }

      .input-group .form-control:focus {
        background: var(--surface-2) !important;
        color: var(--text-primary) !important;
        z-index: 3 !important;
      }

      .input-group-text {
        background: var(--surface-2) !important;
        border-color: var(--border-light) !important;
        color: var(--text-primary) !important;
        font-weight: 600 !important;
        border-radius: 0 !important;
      }

      .input-group .btn {
        border-radius: 0 !important;
        border-left: none !important;
        z-index: 2 !important;
      }

      /* First and last child radius fix */
      .input-group .form-control:first-child,
      .input-group-text:first-child {
        border-radius: 8px 0 0 8px !important;
      }

      .input-group .form-control:last-child,
      .input-group .btn:last-child,
      .input-group-text:last-child {
        border-radius: 0 8px 8px 0 !important;
      }

      /* Offcanvas Dark Theme */
      .offcanvas {
        background: var(--glass-bg) !important;
        color: var(--text-primary) !important;
        backdrop-filter: blur(12px) !important;
        border: 1px solid var(--border-light) !important;
      }

      .offcanvas-header {
        background: var(--surface-2) !important;
        color: var(--text-primary) !important;
        border-bottom: 1px solid var(--border-light) !important;
        padding: 20px 24px !important;
      }

      .offcanvas-title {
        color: var(--text-primary) !important;
        font-weight: 700 !important;
      }

      .offcanvas-body {
        color: var(--text-primary) !important;
        padding: 24px !important;
      }

      /* Progress Bar Enhanced */
      .progress {
        border-radius: 12px !important;
        background: var(--surface-1) !important;
        height: 10px !important;
        overflow: hidden !important;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2) !important;
      }

      .progress-bar {
        background: var(--primary-gradient) !important;
        border-radius: 12px !important;
        transition: width 0.6s ease !important;
        position: relative !important;
        overflow: hidden !important;
      }

      .progress-bar::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        bottom: 0 !important;
        right: 0 !important;
        background-image: linear-gradient(
          45deg,
          rgba(255, 255, 255, 0.15) 25%,
          transparent 25%,
          transparent 50%,
          rgba(255, 255, 255, 0.15) 50%,
          rgba(255, 255, 255, 0.15) 75%,
          transparent 75%,
          transparent
        ) !important;
        background-size: 20px 20px !important;
        animation: progress-bar-stripes 1s linear infinite !important;
      }

      @keyframes progress-bar-stripes {
        0% { background-position: 20px 0; }
        100% { background-position: 0 0; }
      }

      /* Responsive Design Enhancements */
      @media (max-width: 768px) {
        .container, .container-fluid {
          padding: 16px !important;
          margin-bottom: 16px !important;
          border-radius: 12px !important;
        }

        .card {
          border-radius: 12px !important;
        }

        .modal-dialog {
          margin: 16px !important;
        }

        .modal-content {
          border-radius: 12px !important;
        }

        .nav-link {
          padding: 8px 12px !important;
          font-size: 14px !important;
        }

        .btn {
          padding: 8px 16px !important;
          font-size: 14px !important;
        }

        .table {
          font-size: 13px !important;
        }

        .table thead th,
        .table tbody td {
          padding: 12px 8px !important;
        }
      }

      @media (max-width: 576px) {
        .container, .container-fluid {
          padding: 12px !important;
          border-radius: 8px !important;
        }

        .modal-dialog {
          margin: 8px !important;
        }

        .modal-content {
          border-radius: 8px !important;
        }

        .card {
          border-radius: 8px !important;
        }

        .btn {
          padding: 6px 12px !important;
          font-size: 13px !important;
        }

        .form-control, .form-select {
          padding: 8px 12px !important;
          font-size: 14px !important;
        }
      }

      /* Print Styles */
      @media print {
        body {
          background: white !important;
          color: black !important;
        }

        .container, .container-fluid,
        .card, .modal-content,
        .table {
          background: white !important;
          color: black !important;
          border: 1px solid #ddd !important;
          box-shadow: none !important;
        }

        .btn {
          background: #f8f9fa !important;
          color: black !important;
          border: 1px solid #ddd !important;
        }
      }

      /* High Contrast Mode Support */
      @media (prefers-contrast: high) {
        :root {
          --text-primary: #ffffff;
          --text-secondary: #f0f0f0;
          --text-muted: #cccccc;
          --border-light: rgba(255, 255, 255, 0.3);
          --shadow-light: 0 4px 16px rgba(0, 0, 0, 0.5);
        }

        .btn, .form-control, .card {
          border-width: 2px !important;
        }

        .text-primary { color: #ffffff !important; }
        .text-secondary { color: #f0f0f0 !important; }
        .text-muted { color: #cccccc !important; }
      }

      /* Reduced Motion Support */
      @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
          animation-duration: 0.01ms !important;
          animation-iteration-count: 1 !important;
          transition-duration: 0.01ms !important;
          scroll-behavior: auto !important;
        }
      }

      // ...existing code...
    </style>
  </head>

  <body>
    <div class="page">
      
      <!-- Üst Navbar -->
      @include('back.layouts.inc.header')

      <!-- Sidebar Menü -->
      <header class="navbar-expand-md">
        <div class="collapse navbar-collapse" id="navbar-menu">
          <div class="navbar">
            <div class="container-xl">

              <ul class="navbar-nav">

                <li class="nav-item">
                  <a class="nav-link" href="/home">
                    <i class="fas fa-home nav-link-icon"></i>
                    <span class="nav-link-title">Ana Sayfa</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="/test-automation">
                    <i class="fas fa-cogs nav-link-icon"></i>
                    <span class="nav-link-title">Test Otomasyonu</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="/my-scenarios">
                    <i class="fas fa-file-alt nav-link-icon"></i>
                    <span class="nav-link-title">Manuel Senaryolarım</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="/test-reports">
                    <i class="fas fa-chart-line nav-link-icon"></i>
                    <span class="nav-link-title">Test Raporları</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="/jira-tasks">
                    <i class="fab fa-jira nav-link-icon"></i>
                    <span class="nav-link-title">JIRA Taskları</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="/settings">
                    <i class="fas fa-cog nav-link-icon"></i>
                    <span class="nav-link-title">Ayarlar</span>
                  </a>
                </li>

              </ul>
              <div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0"></div>
            </div>
          </div>
        </div>
      </header>

      <!-- Sayfa İçeriği -->
      <div class="page-wrapper">

        <!-- Header Alanı -->
        @yield('pageHeader')

        <!-- Ana İçerik -->
        <div class="page-body">
          <div class="container-xl">
            @yield('content')
          </div>
        </div>

        @include('back.layouts.inc.footer')

      </div>
    </div>

    <!-- JS Libraries -->
    <script src="{{ asset('back/dist/js/tabler.min.js') }}" defer></script>
    @stack('scripts')
  </body>
</html>
