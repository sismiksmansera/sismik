@extends('layouts.app')

@section('title', 'Keamanan & Akses Login | SISMIK')

@push('styles')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(30, 58, 138, 0.3);
    }
    .dashboard-header h1 { margin: 0 0 8px 0; font-size: 1.75rem; display: flex; align-items: center; gap: 12px; }
    .dashboard-header .role-badge {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 13px;
    }
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .stat-content { display: flex; align-items: center; gap: 15px; }
    .stat-icon {
        width: 50px; height: 50px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 20px;
    }
    .stat-info h3 { margin: 0; font-size: 24px; color: #1f2937; }
    .stat-info p { margin: 0; color: #6b7280; font-size: 14px; }
    
    /* Section Card */
    .section-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        margin-bottom: 30px;
        overflow: hidden;
    }
    .section-header {
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    .section-title { display: flex; align-items: center; gap: 12px; }
    .section-title i { font-size: 24px; }
    .section-title h2 { margin: 0; font-size: 18px; }
    .section-title p { margin: 0; font-size: 12px; opacity: 0.9; }
    
    /* Table */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th {
        padding: 12px 15px;
        text-align: left;
        color: #374151;
        font-weight: 600;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }
    .data-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #e5e7eb;
    }
    .data-table th.text-center, .data-table td.text-center { text-align: center; }
    
    /* Empty State */
    .empty-state { padding: 40px; text-align: center; }
    .empty-state i { font-size: 48px; color: #10b981; margin-bottom: 15px; }
    .empty-state h3 { color: #4b5563; margin: 0 0 10px 0; }
    .empty-state p { color: #9ca3af; margin: 0; }
    
    /* Status Badges */
    .badge-success { background: #d1fae5; color: #065f46; padding: 3px 10px; border-radius: 10px; font-size: 12px; }
    .badge-danger { background: #fee2e2; color: #991b1b; padding: 3px 10px; border-radius: 10px; font-size: 12px; }
    .badge-warning { background: #fef3c7; color: #92400e; }
    .badge-info { background: #dbeafe; color: #1e40af; }
    .badge-purple { background: #f3e8ff; color: #7c3aed; }
    
    code { background: #f3f4f6; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
    
    .scrollable-table { max-height: 400px; overflow-y: auto; }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="dashboard-header">
            <h1><i class="fas fa-shield-alt"></i> Keamanan & Akses Login</h1>
            <span class="role-badge">Admin Security Panel</span>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #28a745;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ count($lockedUsers) }}</h3>
                        <p>User Terkunci</p>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $failedAttemptsToday }}</h3>
                        <p>Gagal Login Hari Ini</p>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $successfulLoginsToday }}</h3>
                        <p>Login Sukses Hari Ini</p>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $totalAttemptsToday }}</h3>
                        <p>Total Percobaan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Locked Users Section -->
        <div class="section-card">
            <div class="section-header" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;">
                <div class="section-title">
                    <i class="fas fa-user-lock"></i>
                    <div>
                        <h2>User Terkunci</h2>
                        <p>User dengan 5+ percobaan gagal dalam 15 menit terakhir</p>
                    </div>
                </div>
                @if(count($lockedUsers) > 0)
                    <form method="POST" action="{{ route('admin.keamanan.clear-all') }}" onsubmit="return confirm('Hapus SEMUA lockout? User yang terkunci akan bisa login lagi.');">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                            <i class="fas fa-unlock-alt"></i> Reset Semua
                        </button>
                    </form>
                @endif
            </div>
            
            @if(count($lockedUsers) == 0)
                <div class="empty-state">
                    <i class="fas fa-smile"></i>
                    <h3>Tidak Ada User Terkunci</h3>
                    <p>Semua user dapat login dengan normal.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>IP Address</th>
                                <th class="text-center">Percobaan</th>
                                <th class="text-center">Terakhir</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lockedUsers as $user)
                                <tr>
                                    <td><strong>{{ $user->username }}</strong></td>
                                    <td><code>{{ $user->ip_address }}</code></td>
                                    <td class="text-center">
                                        <span class="badge-danger" style="padding: 4px 12px; font-weight: 600;">
                                            {{ $user->attempts }}x gagal
                                        </span>
                                    </td>
                                    <td class="text-center" style="color: #6b7280; font-size: 13px;">
                                        {{ date('H:i:s', strtotime($user->last_attempt)) }}
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('admin.keamanan.unlock') }}" style="display: inline;" onsubmit="return confirm('Reset lockout untuk user ini?');">
                                            @csrf
                                            <input type="hidden" name="username" value="{{ $user->username }}">
                                            <input type="hidden" name="ip" value="{{ $user->ip_address }}">
                                            <button type="submit" class="btn btn-primary" style="background: #10b981; padding: 8px 16px; font-size: 13px;">
                                                <i class="fas fa-unlock"></i> Unlock
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Recent Login Attempts -->
        <div class="section-card">
            <div class="section-header" style="background: linear-gradient(135deg, #3b82f6, #2563eb); color: white;">
                <div class="section-title">
                    <i class="fas fa-history"></i>
                    <div>
                        <h2>Riwayat Percobaan Login</h2>
                        <p>50 percobaan terakhir</p>
                    </div>
                </div>
            </div>
            
            <div class="scrollable-table">
                <table class="data-table">
                    <thead style="position: sticky; top: 0; background: #f9fafb;">
                        <tr>
                            <th>Waktu</th>
                            <th>Username</th>
                            <th>IP Address</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAttempts as $attempt)
                            <tr>
                                <td style="color: #6b7280; font-size: 13px;">
                                    {{ date('d/m H:i:s', strtotime($attempt->attempt_time)) }}
                                </td>
                                <td>{{ $attempt->username }}</td>
                                <td><code>{{ $attempt->ip_address }}</code></td>
                                <td class="text-center">
                                    @if($attempt->success)
                                        <span class="badge-success"><i class="fas fa-check"></i> Sukses</span>
                                    @else
                                        <span class="badge-danger"><i class="fas fa-times"></i> Gagal</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Security Logs -->
        <div class="section-card">
            <div class="section-header" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;">
                <div class="section-title">
                    <i class="fas fa-clipboard-list"></i>
                    <div>
                        <h2>Log Keamanan</h2>
                        <p>Aktivitas keamanan terbaru</p>
                    </div>
                </div>
            </div>
            
            <div class="scrollable-table">
                <table class="data-table">
                    <thead style="position: sticky; top: 0; background: #f9fafb;">
                        <tr>
                            <th>Waktu</th>
                            <th>Aksi</th>
                            <th>Username</th>
                            <th>Detail</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $actionColors = [
                                'login_success' => 'badge-success',
                                'password_migrated' => 'badge-info',
                                'admin_unlock' => 'badge-warning',
                                'admin_clear_all_lockouts' => 'badge-purple',
                            ];
                        @endphp
                        @foreach($securityLogs as $log)
                            <tr>
                                <td style="color: #6b7280; font-size: 13px; white-space: nowrap;">
                                    {{ date('d/m H:i', strtotime($log->created_at)) }}
                                </td>
                                <td>
                                    <span class="{{ $actionColors[$log->action] ?? '' }}" style="padding: 3px 10px; border-radius: 10px; font-size: 12px; font-weight: 500;">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td style="font-size: 13px;">{{ $log->username ?? '-' }}</td>
                                <td style="color: #6b7280; font-size: 12px; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->details }}">
                                    {{ $log->details }}
                                </td>
                                <td><code style="font-size: 11px;">{{ $log->ip_address }}</code></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
