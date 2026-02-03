<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SISMIK - Guru BK')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #7c3aed;
            --primary-dark: #6d28d9;
            --primary-light: #a78bfa;
            --secondary: #1F2937;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --info: #3B82F6;
            --light: #F3F4F6;
            --dark: #111827;
            --white: #FFFFFF;
            --gray-100: #F9FAFB;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-400: #9CA3AF;
            --gray-500: #6B7280;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
            
            /* Sidebar dimensions */
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gray-100);
            min-height: 100vh;
        }

        body.no-scroll {
            overflow: hidden;
        }

        .layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
            overflow-x: hidden;
        }

        /* ============================
           SIDEBAR STYLES
        ============================ */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--secondary) 0%, var(--dark) 100%);
            color: var(--white);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .sidebar-header:hover {
            background: rgba(255,255,255,0.05);
        }

        .sidebar-logo {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            object-fit: contain;
            margin: 0 auto 10px;
            display: block;
            transition: all 0.3s ease;
        }

        .sidebar-header .brand-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 4px;
            transition: all 0.3s ease;
        }

        .sidebar-header p {
            font-size: 12px;
            color: var(--gray-400);
            transition: all 0.3s ease;
        }

        .toggle-indicator {
            margin-top: 8px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .sidebar-menu {
            padding: 20px 0;
            flex: 1;
            overflow-y: auto;
        }

        .menu-label {
            padding: 10px 24px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gray-500);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            color: var(--gray-300);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            position: relative;
        }

        .menu-item:hover {
            background: rgba(124, 58, 237, 0.1);
            color: var(--primary);
            border-left-color: var(--primary);
            transform: translateX(5px);
        }

        .menu-item.active {
            background: rgba(124, 58, 237, 0.15);
            color: var(--primary);
            border-left-color: var(--primary);
        }

        .menu-item i {
            width: 24px;
            margin-right: 12px;
            font-size: 18px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .menu-item:hover i {
            transform: scale(1.1);
            color: var(--primary-light);
        }

        .menu-item span {
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        /* ============================
           COLLAPSED STATE (Desktop)
        ============================ */
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar.collapsed .sidebar-logo {
            width: 40px;
            height: 40px;
        }

        .sidebar.collapsed .sidebar-header .brand-name,
        .sidebar.collapsed .sidebar-header p,
        .sidebar.collapsed .toggle-indicator {
            opacity: 0;
            height: 0;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        .sidebar.collapsed .menu-label {
            opacity: 0;
            height: 0;
            padding: 0;
            margin: 0;
            overflow: hidden;
        }

        .sidebar.collapsed .menu-item {
            justify-content: center;
            padding: 14px 0;
        }

        .sidebar.collapsed .menu-item i {
            margin-right: 0;
            font-size: 20px;
        }

        .sidebar.collapsed .menu-item span {
            position: absolute;
            left: calc(var(--sidebar-collapsed-width) + 10px);
            background: var(--secondary);
            color: var(--white);
            padding: 10px 16px;
            border-radius: 8px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            pointer-events: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            z-index: 1100;
            font-size: 13px;
        }

        .sidebar.collapsed .menu-item span::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 50%;
            transform: translateY(-50%);
            border-width: 8px;
            border-style: solid;
            border-color: transparent var(--secondary) transparent transparent;
        }

        .sidebar.collapsed .menu-item:hover span {
            opacity: 1;
            visibility: visible;
            left: calc(var(--sidebar-collapsed-width) + 15px);
        }

        .sidebar.collapsed .toggle-indicator {
            transform: rotate(180deg);
        }

        /* ============================
           MAIN CONTENT
        ============================ */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: 100vh;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: calc(100% - var(--sidebar-width));
        }

        .sidebar.collapsed ~ .main-content,
        .main-content.sidebar-collapsed {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        /* ============================
           MOBILE TOGGLE BUTTON - Half Oval Style
        ============================ */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            background: linear-gradient(180deg, var(--secondary) 0%, var(--dark) 100%);
            color: var(--primary);
            border: none;
            padding: 0;
            border-radius: 0 30px 30px 0;
            cursor: pointer;
            z-index: 1100;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            width: 25px;
            height: 60px;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .mobile-toggle:hover {
            width: 30px;
            background: linear-gradient(180deg, var(--dark) 0%, var(--secondary) 100%);
        }

        .mobile-toggle:active {
            transform: translateY(-50%) scale(0.95);
        }

        .mobile-toggle i {
            color: var(--primary);
            font-size: 12px;
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
            z-index: 900;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* ============================
           RESPONSIVE - MOBILE
        ============================ */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width);
                height: 100vh;
                height: 100dvh;
                min-height: -webkit-fill-available;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .sidebar.mobile-open .sidebar-logo {
                width: 50px;
                height: 50px;
            }

            .sidebar.mobile-open .sidebar-header .brand-name {
                font-size: 18px;
            }

            .sidebar.mobile-open .sidebar-header p,
            .sidebar.mobile-open .toggle-indicator {
                opacity: 1;
                height: auto;
            }

            .sidebar.mobile-open .menu-label {
                opacity: 1;
                height: auto;
                padding: 10px 24px;
            }

            .sidebar.mobile-open .menu-item {
                justify-content: flex-start;
                padding: 14px 24px;
            }

            .sidebar.mobile-open .menu-item i {
                margin-right: 12px;
                font-size: 18px;
            }

            .sidebar.mobile-open .menu-item span {
                position: static;
                opacity: 1;
                visibility: visible;
                background: transparent;
                padding: 0;
                box-shadow: none;
            }

            .sidebar.mobile-open .menu-item span::before {
                display: none;
            }

            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100vw;
                padding: 15px 10px 20px 10px;
                overflow-x: hidden;
                box-sizing: border-box;
            }

            .mobile-toggle {
                display: flex;
            }

            .toggle-indicator {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Mobile Toggle Button - Half Oval -->
    <button class="mobile-toggle" id="mobileToggle">
        <i class="fas fa-chevron-right"></i>
    </button>
    
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Include Sidebar -->
    @include('layouts.partials.sidebar-guru-bk')
    
    <!-- Main Content -->
    @yield('content')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarHeader = document.querySelector('.sidebar-header');
            const mainContent = document.querySelector('.main-content');
            const mobileToggle = document.getElementById('mobileToggle');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (!sidebar) return;
            
            const isMobile = () => window.innerWidth <= 768;
            
            // DESKTOP: Toggle collapsed on header click
            if (sidebarHeader) {
                sidebarHeader.addEventListener('click', function(e) {
                    if (!isMobile()) {
                        e.preventDefault();
                        sidebar.classList.toggle('collapsed');
                        if (mainContent) {
                            mainContent.classList.toggle('sidebar-collapsed');
                        }
                        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                    }
                });
            }
            
            // Restore collapsed state on desktop
            const savedState = localStorage.getItem('sidebarCollapsed');
            if (savedState === 'true' && !isMobile()) {
                sidebar.classList.add('collapsed');
                if (mainContent) mainContent.classList.add('sidebar-collapsed');
            }
            
            // MOBILE: Toggle sidebar visibility
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('mobile-open');
                    if (overlay) overlay.classList.toggle('show');
                    document.body.classList.toggle('no-scroll');
                });
            }
            
            // Close sidebar when clicking overlay
            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('show');
                    document.body.classList.remove('no-scroll');
                });
            }
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (!isMobile()) {
                    sidebar.classList.remove('mobile-open');
                    if (overlay) overlay.classList.remove('show');
                    document.body.classList.remove('no-scroll');
                } else {
                    sidebar.classList.remove('collapsed');
                    if (mainContent) mainContent.classList.remove('sidebar-collapsed');
                }
            });
        });
    </script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
