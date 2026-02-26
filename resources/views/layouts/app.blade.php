<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ISU-Flow Leave Management System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Modern Clean Interface */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-name {
            font-weight: 600;
            color: #333;
        }
        
        .user-role {
            font-size: 0.85rem;
            color: #666;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 80px;
            bottom: 0;
            width: 280px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem 1rem;
            overflow-y: auto;
            z-index: 999;
        }
        
        .nav-section {
            margin-bottom: 2rem;
        }
        
        .nav-title {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 0.5rem;
            letter-spacing: 0.05em;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #555;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        .nav-link:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            transform: translateX(4px);
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .nav-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            margin-top: 80px;
            padding: 2rem;
            min-height: calc(100vh - 80px);
        }
        
        /* Cards */
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.2s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        
        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 1);
        }
        
        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .table th {
            background: rgba(102, 126, 234, 0.1);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
        }
        
        .table td {
            padding: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .table tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }
        
        /* Badges */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-success {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }
        
        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }
        
        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }
        
        .badge-info {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    @auth
        <!-- Header -->
        <header class="header">
            <div class="logo">ISU-Flow</div>
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->full_name }}</div>
                    <div class="user-role">{{ Auth::user()->roles->first()->name ?? 'User' }}</div>
                </div>
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Sidebar -->
        <nav class="sidebar">
            @if(Auth::user()->hasRole('admin'))
                <div class="nav-section">
                    <div class="nav-title">Admin Menu</div>
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('hr.designation-documents.index') }}" class="nav-link {{ request()->routeIs('hr.designation-documents.*') ? 'active' : '' }}">
                        <i class="fas fa-file-contract nav-icon"></i>
                        Designation Documents
                    </a>
                    <a href="{{ route('hr.leave-applications.index') }}" class="nav-link {{ request()->routeIs('hr.leave-applications.index') ? 'active' : '' }}">
                        <i class="fas fa-file-alt nav-icon"></i>
                        Leave Applications
                    </a>
                    <a href="{{ route('hr.employees.index') }}" class="nav-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}">
                        <i class="fas fa-users nav-icon"></i>
                        Employees
                    </a>
                    <a href="{{ route('leave-credits.index') }}" class="nav-link {{ request()->routeIs('leave-credits.*') ? 'active' : '' }}">
                        <i class="fas fa-coins nav-icon"></i>
                        Leave Credits
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        Reports
                    </a>
                </div>
            @elseif(Auth::user()->hasRole('hr'))
                <div class="nav-section">
                    <div class="nav-title">HR Menu</div>
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('hr.designation-documents.index') }}" class="nav-link {{ request()->routeIs('hr.designation-documents.*') ? 'active' : '' }}">
                        <i class="fas fa-file-contract nav-icon"></i>
                        Designation Documents
                    </a>
                    <a href="{{ route('hr.leave-applications.index') }}" class="nav-link {{ request()->routeIs('hr.leave-applications.index') ? 'active' : '' }}">
                        <i class="fas fa-file-alt nav-icon"></i>
                        Leave Applications
                    </a>
                    <a href="{{ route('leave-credits.index') }}" class="nav-link {{ request()->routeIs('leave-credits.*') ? 'active' : '' }}">
                        <i class="fas fa-coins nav-icon"></i>
                        Leave Credits
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        Reports
                    </a>
                </div>
            @else
                <div class="nav-section">
                    <div class="nav-title">Employee Menu</div>
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('dashboard.leave-balances') }}" class="nav-link {{ request()->routeIs('dashboard.leave-balances') ? 'active' : '' }}">
                        <i class="fas fa-balance-scale nav-icon"></i>
                        Leave Balances
                    </a>
                    <a href="{{ route('dashboard.leave-history') }}" class="nav-link {{ request()->routeIs('dashboard.leave-history') ? 'active' : '' }}">
                        <i class="fas fa-history nav-icon"></i>
                        Leave History
                    </a>
                    <a href="{{ route('leave-applications.create') }}" class="nav-link {{ request()->routeIs('leave-applications.create') ? 'active' : '' }}">
                        <i class="fas fa-plus nav-icon"></i>
                        Apply for Leave
                    </a>
                    <a href="{{ route('designation-documents.create') }}" class="nav-link {{ request()->routeIs('designation-documents.create') ? 'active' : '' }}">
                        <i class="fas fa-upload nav-icon"></i>
                        Upload Designation
                    </a>
                    <a href="{{ route('leave-applications.index') }}" class="nav-link {{ request()->routeIs('leave-applications.index') ? 'active' : '' }}">
                        <i class="fas fa-file-alt nav-icon"></i>
                        My Applications
                    </a>
                </div>
            @endif
        </nav>
    @endauth

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
            <div style="background: rgba(34, 197, 94, 0.1); color: #16a34a; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid rgba(34, 197, 94, 0.2);">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div style="background: rgba(239, 68, 68, 0.1); color: #dc2626; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid rgba(239, 68, 68, 0.2);">
                {{ session('error') }}
            </div>
        @endif
        
        @if(session('info'))
            <div style="background: rgba(59, 130, 246, 0.1); color: #2563eb; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid rgba(59, 130, 246, 0.2);">
                {{ session('info') }}
            </div>
        @endif
        
        @yield('content')
    </main>
</body>
</html>
