@extends('layouts.app')

@section('title', 'Dashboard Guru BK | SISMIK')

@push('styles')
<style>
    /* Dashboard Header */
    .bk-dashboard-header {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(139, 92, 246, 0.3);
    }
    .header-content-wrapper { display: flex; align-items: center; gap: 24px; position: relative; z-index: 2; }
    .header-avatar {
        width: 80px; height: 80px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        border: 3px solid rgba(255,255,255,0.4);
        overflow: hidden;
    }
    .header-avatar i { font-size: 36px; color: white; }
    .header-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .greeting-text { color: rgba(255,255,255,0.8); font-size: 14px; display: block; margin-bottom: 4px; }
    .header-greeting h1 { color: white; font-size: 28px; font-weight: 700; margin: 0; }
    .header-details { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; }
    .detail-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.15); backdrop-filter: blur(5px);
        padding: 6px 12px; border-radius: 20px; font-size: 12px; color: white;
        border: 1px solid rgba(255,255,255,0.2);
    }

    /* Stats Grid */
    .quick-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }
    .stat-card-mini {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }
    .stat-card-mini:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    .stat-card-mini .stat-icon {
        width: 50px; height: 50px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 20px;
    }
    .stat-card-mini.primary .stat-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .stat-card-mini.warning .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-card-mini.info .stat-icon { background: linear-gradient(135deg, #06b6d4, #0891b2); }
    .stat-card-mini.success .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-card-mini.danger .stat-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .stat-card-mini.purple .stat-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .stat-card-mini h3 { margin: 0; font-size: 24px; font-weight: 700; color: #1f2937; }
    .stat-card-mini p { margin: 4px 0 0 0; color: #6b7280; font-size: 13px; }

    /* Chart Card */
    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }
    .chart-header { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
    .chart-header h3 { margin: 0; font-size: 18px; color: #1f2937; }
    .chart-header i { color: #8b5cf6; }

    /* Quick Actions */
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    .action-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 15px;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        border-color: #8b5cf6;
    }
    .action-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .action-icon.primary { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .action-icon.success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .action-icon.warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .action-icon.danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .action-icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
    .action-info h4 { margin: 0; font-size: 15px; font-weight: 600; color: #1f2937; }
    .action-info p { margin: 4px 0 0 0; font-size: 12px; color: #6b7280; }

    /* Recent Activity */
    .activity-list {
        padding: 0;
        margin: 0;
        list-style: none;
    }
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    .activity-content { flex: 1; }
    .activity-content h5 { margin: 0; font-size: 14px; font-weight: 600; color: #1f2937; }
    .activity-content p { margin: 4px 0 0 0; font-size: 12px; color: #6b7280; }
    .activity-time { font-size: 11px; color: #9ca3af; }

    /* Responsive */
    @media (max-width: 768px) {
        .bk-dashboard-header { padding: 20px; margin-bottom: 15px; }
        .header-content-wrapper { flex-direction: column; text-align: center; gap: 15px; }
        .header-avatar { width: 60px; height: 60px; }
        .header-avatar i { font-size: 24px; }
        .header-greeting h1 { font-size: 18px !important; }
        .greeting-text { font-size: 12px; }
        .header-details { justify-content: center; gap: 6px; }
        .detail-badge { font-size: 10px; padding: 4px 8px; }
        
        .quick-stats-grid {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            padding-bottom: 10px;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }
        
        .stat-card-mini {
            min-width: 100px;
            flex-shrink: 0;
            scroll-snap-align: start;
            flex-direction: column;
            text-align: center;
            padding: 12px;
            gap: 8px;
        }
        
        .stat-card-mini .stat-icon {
            width: 36px;
            height: 36px;
            font-size: 14px !important;
        }
        
        .stat-card-mini h3 {
            font-size: 16px !important;
        }
        
        .stat-card-mini p {
            font-size: 9px !important;
        }
        
        .quick-actions-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru-bk')

    <div class="main-content">
        @if($isImpersonating ?? false)
        <!-- Impersonation Banner -->
        <div style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 12px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-user-secret" style="font-size: 20px;"></i>
                <span style="font-weight: 600;">Anda sedang login sebagai guru BK: {{ $guruBK->nama }}</span>
            </div>
            <a href="{{ route('guru_bk.stop-impersonate') }}" style="background: white; color: #d97706; padding: 8px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 13px;">
                <i class="fas fa-arrow-left"></i> Kembali ke Admin
            </a>
        </div>
        @endif

        <!-- Dashboard Header -->
        <div class="bk-dashboard-header">
            <div class="header-content-wrapper">
                @php
                    use Illuminate\Support\Facades\Storage;
                    $hasFoto = $guruBK->foto && Storage::disk('public')->exists('guru_bk/' . $guruBK->foto);
                    $initials = collect(explode(' ', $guruBK->nama))->map(fn($p) => strtoupper(substr($p, 0, 1)))->take(2)->join('');
                @endphp
                <div class="header-avatar">
                    @if($hasFoto)
                        <img src="{{ asset('storage/guru_bk/' . $guruBK->foto) }}" alt="{{ $guruBK->nama }}">
                    @else
                        <span style="font-size: 24px; font-weight: bold; color: white;">{{ $initials ?: 'BK' }}</span>
                    @endif
                </div>
                <div class="header-info">
                    <div class="header-greeting">
                        <span class="greeting-text">Selamat Datang,</span>
                        <h1>{{ $guruBK->nama ?? 'Guru BK' }} 👋</h1>
                    </div>
                    <div class="header-details">
                        @if($periodik)
                            <span class="detail-badge"><i class="fas fa-calendar-alt"></i> {{ $periodik->tahun_pelajaran }} - {{ $periodik->semester }}</span>
                        @endif
                        <span class="detail-badge"><i class="fas fa-user-tie"></i> Guru BK</span>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-check-circle" style="font-size: 24px;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($isTesting ?? false)
        <!-- Testing Mode Banner -->
        <div style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 12px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-exclamation-triangle" style="font-size: 24px;"></i>
            <div>
                <strong>MODE TESTING AKTIF</strong>
                <p style="margin: 0; font-size: 13px; opacity: 0.9;">Sistem sedang menggunakan tanggal testing: {{ $tanggalFormatted ?? 'N/A' }}</p>
            </div>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="chart-card">
            <div class="chart-header">
                <i class="fas fa-chart-pie"></i>
                <h3>Statistik Catatan Bimbingan</h3>
            </div>
            <div class="quick-stats-grid">
                <div class="stat-card-mini primary">
                    <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                    <div>
                        <h3>{{ number_format($totalCatatan) }}</h3>
                        <p>Total Catatan</p>
                    </div>
                </div>
                <div class="stat-card-mini warning">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <h3>{{ number_format($catatanBelum) }}</h3>
                        <p>Belum Ditangani</p>
                    </div>
                </div>
                <div class="stat-card-mini info">
                    <div class="stat-icon"><i class="fas fa-spinner"></i></div>
                    <div>
                        <h3>{{ number_format($catatanProses) }}</h3>
                        <p>Dalam Proses</p>
                    </div>
                </div>
                <div class="stat-card-mini success">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <h3>{{ number_format($catatanSelesai) }}</h3>
                        <p>Selesai</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="chart-card">
            <div class="chart-header">
                <i class="fas fa-bolt"></i>
                <h3>Aksi Cepat</h3>
            </div>
            <div class="quick-actions-grid">
                <a href="{{ route('guru_bk.semua-catatan') }}" class="action-card">
                    <div class="action-icon primary">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="action-info">
                        <h4>Semua Catatan</h4>
                        <p>Lihat semua catatan bimbingan</p>
                    </div>
                </a>
                <a href="{{ route('guru_bk.siswa-bimbingan') }}" class="action-card">
                    <div class="action-icon success">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="action-info">
                        <h4>Siswa Bimbingan</h4>
                        <p>Lihat daftar siswa bimbingan</p>
                    </div>
                </a>
                <a href="{{ route('guru_bk.tugas-tambahan') }}" class="action-card">
                    <div class="action-icon purple">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="action-info">
                        <h4>Tugas Tambahan</h4>
                        <p>Pembina Ekstra & Wali Kelas</p>
                    </div>
                </a>
                <a href="{{ route('guru_bk.pengaduan') }}" class="action-card">
                    <div class="action-icon danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="action-info">
                        <h4>Pengaduan Siswa</h4>
                        <p>Kelola pengaduan yang diteruskan</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="chart-card">
            <div class="chart-header">
                <i class="fas fa-info-circle"></i>
                <h3>Informasi</h3>
            </div>
            <div style="padding: 20px; background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(124, 58, 237, 0.05)); border-radius: 12px; border: 1px solid rgba(139, 92, 246, 0.2);">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-heart" style="color: white; font-size: 20px;"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0; color: #1f2937; font-size: 16px;">Selamat Bertugas!</h4>
                        <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">Terima kasih atas dedikasi Anda dalam membimbing siswa-siswi kami.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
