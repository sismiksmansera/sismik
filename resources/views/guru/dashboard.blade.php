@extends('layouts.app')

@section('title', 'Dashboard Guru | SISMIK')

@push('styles')
<style>
    /* Dashboard Header */
    .guru-dashboard-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
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
    }
    .stat-card-mini .stat-icon {
        width: 50px; height: 50px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 20px;
    }
    .stat-card-mini.primary .stat-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .stat-card-mini.warning .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-card-mini.success .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-card-mini.danger .stat-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }
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
    .chart-header i { color: #10b981; }

    /* Schedule Section */
    .schedule-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .schedule-title { display: flex; align-items: center; gap: 15px; }
    .schedule-icon {
        width: 50px; height: 50px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
    }
    .schedule-icon i { color: white; font-size: 20px; }
    .schedule-info h2 { margin: 0; color: #1f2937; font-size: 20px; font-weight: 700; }
    .schedule-info p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }
    
    .legend-badges { display: flex; gap: 10px; flex-wrap: wrap; }
    .legend-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .legend-success { background: rgba(16,185,129,0.1); color: #10b981; }
    .legend-danger { background: rgba(239,68,68,0.1); color: #ef4444; }

    /* Schedule Grid */
    .schedule-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; }
    .schedule-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
    }
    .schedule-card-header {
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    /* Status: Sudah Presensi (Green) */
    .schedule-card.status-done .schedule-card-header {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    .schedule-card.status-done .schedule-card-header:hover {
        background: linear-gradient(135deg, #059669, #047857);
    }
    
    /* Status: Belum Presensi (Gray) */
    .schedule-card.status-pending .schedule-card-header {
        background: linear-gradient(135deg, #6b7280, #4b5563);
    }
    .schedule-card.status-pending .schedule-card-header:hover {
        background: linear-gradient(135deg, #4b5563, #374151);
    }
    
    /* Status: Izin (Orange) */
    .schedule-card.status-izin .schedule-card-header {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    .schedule-card.status-izin .schedule-card-header:hover {
        background: linear-gradient(135deg, #d97706, #b45309);
    }
    .schedule-card-header strong { font-size: 16px; }
    .jam-badge { background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 12px; font-size: 12px; }
    
    /* Collapsible toggle */
    .toggle-icon {
        transition: transform 0.3s ease;
        margin-left: 10px;
        font-size: 14px;
    }
    .schedule-card.collapsed .toggle-icon {
        transform: rotate(-90deg);
    }
    
    /* Card body collapsible */
    .schedule-card-body { 
        padding: 20px; 
        transition: all 0.3s ease;
        max-height: 500px;
        overflow: hidden;
    }
    .schedule-card.collapsed .schedule-card-body {
        max-height: 0;
        padding: 0 20px;
    }
    
    /* Collapsed info shown in header */
    .collapsed-info {
        display: none;
        font-size: 11px;
        opacity: 0.9;
        margin-top: 4px;
    }
    .schedule-card.collapsed .collapsed-info {
        display: block;
    }
    
    /* Status badge in collapsed view */
    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
    }
    .status-badge.status-done {
        background: rgba(16, 185, 129, 0.3);
        color: #d1fae5;
    }
    .status-badge.status-pending {
        background: rgba(239, 68, 68, 0.3);
        color: #fecaca;
    }
    .status-badge.status-izin {
        background: rgba(245, 158, 11, 0.3);
        color: #fef3c7;
    }
    
    .mapel-info { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 15px; }
    .mapel-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }
    .mapel-details { flex: 1; }
    .mapel-name { font-weight: 600; color: #1f2937; font-size: 15px; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
    .mapel-time { font-size: 13px; color: #6b7280; }

    /* Action Buttons */
    .action-buttons { display: flex; flex-direction: column; gap: 8px; }
    .action-btn {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        padding: 10px 14px;
        color: white;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    .action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .action-btn.disabled { background: #9ca3af !important; cursor: not-allowed; opacity: 0.7; }
    .btn-presensi-do { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .btn-presensi-done { background: linear-gradient(135deg, #10b981, #059669); }
    .btn-penilaian-do { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .btn-penilaian-done { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .btn-izin-do { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .btn-izin-done { background: linear-gradient(135deg, #ec4899, #db2777); }

    /* Empty State */
    .empty-state {
        background: white;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
    }
    .empty-state i { font-size: 48px; color: #d1d5db; margin-bottom: 15px; }
    .empty-state h3 { color: #4b5563; margin: 0 0 10px 0; }
    .empty-state p { color: #9ca3af; margin: 0; }

    @media (max-width: 768px) {
        .guru-dashboard-header { padding: 20px; margin-bottom: 15px; }
        .header-content-wrapper { flex-direction: column; text-align: center; gap: 15px; }
        .header-avatar { width: 60px; height: 60px; }
        .header-avatar i { font-size: 24px; }
        .header-greeting h1 { font-size: 18px !important; }
        .greeting-text { font-size: 12px; }
        .header-details { justify-content: center; gap: 6px; }
        .detail-badge { font-size: 10px; padding: 4px 8px; }
        
        .schedule-grid { grid-template-columns: 1fr; }
        
        /* Stats cards - horizontal scroll */
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
        
        /* Chart card mobile */
        .chart-card {
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .chart-header h3 {
            font-size: 14px !important;
        }
        
        /* Schedule section mobile */
        .schedule-header {
            gap: 10px;
        }
        
        .schedule-icon {
            width: 40px;
            height: 40px;
        }
        
        .schedule-icon i {
            font-size: 16px;
        }
        
        .schedule-info h2 {
            font-size: 14px !important;
        }
        
        .schedule-info p {
            font-size: 11px !important;
        }
        
        .legend-badge {
            font-size: 10px;
            padding: 4px 8px;
        }
        
        /* Schedule cards mobile */
        .schedule-card-header {
            padding: 12px 15px;
        }
        
        .schedule-card-header strong {
            font-size: 13px;
        }
        
        .jam-badge {
            font-size: 10px;
            padding: 3px 8px;
        }
        
        .schedule-card-body {
            padding: 15px;
        }
        
        .mapel-icon {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }
        
        .mapel-name {
            font-size: 12px !important;
        }
        
        .mapel-time {
            font-size: 11px !important;
        }
        
        .action-btn {
            padding: 8px 10px;
            font-size: 11px;
        }
        
        .empty-state {
            padding: 25px;
        }
        
        .empty-state i {
            font-size: 36px;
        }
        
        .empty-state h3 {
            font-size: 14px;
        }
        
        .empty-state p {
            font-size: 12px;
        }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')

    <div class="main-content">
        @if($isImpersonating ?? false)
        <div style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 12px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-user-secret" style="font-size: 20px;"></i>
                <span style="font-weight: 600;">Anda sedang login sebagai guru: {{ $guru->nama }}</span>
            </div>
            <a href="{{ route('guru.stop-impersonate') }}" style="background: white; color: #d97706; padding: 8px 16px; border-radius: 8px; font-weight: 600; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Kembali ke Admin
            </a>
        </div>
        @endif

        <!-- Dashboard Header -->
        <div class="guru-dashboard-header">
            <div class="header-content-wrapper">
                @php
                    use Illuminate\Support\Facades\Storage;
                    $hasFoto = $guru->foto && Storage::disk('public')->exists('guru/' . $guru->foto);
                    $initials = collect(explode(' ', $guru->nama))->map(fn($p) => strtoupper(substr($p, 0, 1)))->take(2)->join('');
                @endphp
                <div class="header-avatar">
                    @if($hasFoto)
                        <img src="{{ asset('storage/guru/' . $guru->foto) }}" alt="{{ $guru->nama }}">
                    @else
                        <span style="font-size: 24px; font-weight: bold; color: white;">{{ $initials ?: 'G' }}</span>
                    @endif
                </div>
                <div class="header-info">
                    <div class="header-greeting">
                        <span class="greeting-text">Selamat Datang,</span>
                        <h1>{{ $guru->nama ?? 'Guru' }} 👋</h1>
                    </div>
                    <div class="header-details">
                        @if($periodik)
                            <span class="detail-badge"><i class="fas fa-calendar-alt"></i> {{ $periodik->tahun_pelajaran }} - {{ $periodik->semester }}</span>
                        @endif
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

        <!-- Stats Cards -->
        <div class="chart-card">
            <div class="chart-header">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3>Aktivitas Mengajar</h3>
            </div>
            <div class="quick-stats-grid">
                <div class="stat-card-mini primary">
                    <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div>
                        <h3>{{ $jumlahKelasHariIni }}</h3>
                        <p>Kelas Hari Ini</p>
                    </div>
                </div>
                <div class="stat-card-mini warning">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <h3>{{ $totalJamHariIni }} JP</h3>
                        <p>Jam Pelajaran</p>
                    </div>
                </div>
                <div class="stat-card-mini {{ $persentasePresensi >= 80 ? 'success' : ($persentasePresensi >= 50 ? 'warning' : 'danger') }}">
                    <div class="stat-icon"><i class="fas fa-clipboard-check"></i></div>
                    <div>
                        <h3>{{ $persentasePresensi }}%</h3>
                        <p>Presensi Hari Ini</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Mengajar -->
        <div class="schedule-section">
            <div class="schedule-header">
                <div class="schedule-title">
                    <div class="schedule-icon"><i class="fas fa-calendar-day"></i></div>
                    <div class="schedule-info">
                        <h2>
                            Jadwal Mengajar Hari Ini
                            @if($isTesting ?? false)
                                <span style="background: #f59e0b; color: white; font-size: 10px; padding: 2px 8px; border-radius: 10px; margin-left: 8px;">TESTING</span>
                            @endif
                        </h2>
                        <p>{{ $hariIni }}, {{ \Carbon\Carbon::parse($tanggalHariIni)->format('d F Y') }}</p>
                    </div>
                </div>
                <div class="legend-badges">
                    <span class="legend-badge legend-success"><i class="fas fa-check-circle"></i> Sudah Presensi</span>
                    <span class="legend-badge legend-danger"><i class="fas fa-times-circle"></i> Belum Presensi</span>
                </div>
            </div>

            @if(empty($jadwalPerMapelRombel))
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h3>Tidak Ada Jadwal</h3>
                    <p>Tidak ada jadwal mengajar untuk hari {{ $hariIni }}</p>
                </div>
            @else
                <div class="schedule-grid">
                    @foreach($jadwalPerMapelRombel as $jadwal)
                        @php
                            $jamText = \App\Http\Controllers\Guru\DashboardController::formatJamRange($jadwal['jam_list']);
                            $firstJam = min($jadwal['jam_list']);
                            $lastJam = max($jadwal['jam_list']);
                            $waktuMulai = isset($jamSetting[$firstJam]['mulai']) ? substr($jamSetting[$firstJam]['mulai'], 0, 5) : '-';
                            $waktuSelesai = isset($jamSetting[$lastJam]['selesai']) ? substr($jamSetting[$lastJam]['selesai'], 0, 5) : '-';
                            $waktuText = ($waktuMulai !== '-' && $waktuSelesai !== '-') ? "($waktuMulai - $waktuSelesai)" : '';
                            $jamKeParam = implode(',', $jadwal['jam_list']);
                        @endphp
                        <div class="schedule-card collapsed {{ $jadwal['sudah_izin'] ? 'status-izin' : ($jadwal['sudah_presensi'] ? 'status-done' : 'status-pending') }}" onclick="toggleScheduleCard(this)">
                            <div class="schedule-card-header">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="fas fa-school"></i>
                                        <strong>{{ $jadwal['nama_rombel'] }}</strong>
                                        <span class="jam-badge">{{ $jamText }}</span>
                                        <i class="fas fa-chevron-down toggle-icon"></i>
                                    </div>
                                    <div class="collapsed-info">
                                        <div><i class="fas fa-book"></i> {{ $jadwal['nama_mapel'] }}
                                        @if($waktuText)
                                            &nbsp;•&nbsp;<i class="fas fa-clock"></i> {{ $waktuText }}
                                        @endif
                                        </div>
                                        <div style="margin-top: 6px;">
                                            @if($jadwal['sudah_izin'])
                                                <span class="status-badge status-izin"><i class="fas fa-calendar-minus"></i> Izin</span>
                                            @elseif($jadwal['sudah_presensi'])
                                                <span class="status-badge status-done"><i class="fas fa-check-circle"></i> Sudah Presensi</span>
                                            @else
                                                <span class="status-badge status-pending"><i class="fas fa-times-circle"></i> Belum Presensi</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="schedule-card-body">
                                <div class="mapel-info">
                                    <div class="mapel-icon" style="{{ $jadwal['sudah_presensi'] ? 'background: rgba(16,185,129,0.1); color: #10b981;' : 'background: rgba(239,68,68,0.1); color: #ef4444;' }}">
                                        <i class="fas {{ $jadwal['sudah_presensi'] ? 'fa-check' : 'fa-times' }}"></i>
                                    </div>
                                    <div class="mapel-details">
                                        <div class="mapel-name">
                                            <i class="fas fa-book" style="color: #10b981;"></i>
                                            {{ $jadwal['nama_mapel'] }}
                                        </div>
                                        @if($waktuText)
                                            <div class="mapel-time"><i class="fas fa-clock"></i> {{ $waktuText }}</div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="action-buttons">
                                    @if($jadwal['sudah_izin'])
                                        <div class="action-btn disabled"><i class="fas fa-ban"></i> Presensi (Izin)</div>
                                    @elseif($jadwal['sudah_presensi'])
                                        <a href="{{ route('guru.presensi.index', ['id_rombel' => $jadwal['id_rombel'], 'mapel' => $jadwal['nama_mapel'], 'jam_ke' => $jamKeParam, 'tanggal' => $tanggalHariIni, 'from' => 'dashboard']) }}" class="action-btn btn-presensi-done">
                                            <i class="fas fa-eye"></i> Lihat Presensi
                                        </a>
                                    @else
                                        <a href="{{ route('guru.presensi.index', ['id_rombel' => $jadwal['id_rombel'], 'mapel' => $jadwal['nama_mapel'], 'jam_ke' => $jamKeParam, 'tanggal' => $tanggalHariIni, 'from' => 'dashboard']) }}" class="action-btn btn-presensi-do">
                                            <i class="fas fa-clipboard-check"></i> Lakukan Presensi
                                        </a>
                                    @endif

                                    @if($jadwal['sudah_izin'])
                                        <div class="action-btn disabled"><i class="fas fa-ban"></i> Penilaian (Izin)</div>
                                    @elseif($jadwal['sudah_penilaian'])
                                        <a href="{{ route('guru.penilaian.index', ['id_rombel' => $jadwal['id_rombel'], 'mapel' => $jadwal['nama_mapel'], 'jam_ke' => $jamKeParam, 'tanggal' => $tanggalHariIni, 'from' => 'dashboard']) }}" class="action-btn btn-penilaian-done">
                                            <i class="fas fa-eye"></i> Lihat Penilaian
                                        </a>
                                    @else
                                        <a href="{{ route('guru.penilaian.index', ['id_rombel' => $jadwal['id_rombel'], 'mapel' => $jadwal['nama_mapel'], 'jam_ke' => $jamKeParam, 'tanggal' => $tanggalHariIni, 'from' => 'dashboard']) }}" class="action-btn btn-penilaian-do">
                                            <i class="fas fa-edit"></i> Input Penilaian
                                        </a>
                                    @endif

                                    @if($jadwal['sudah_izin'])
                                        <a href="{{ route('guru.izin-guru.index', ['id_rombel' => $jadwal['id_rombel'], 'mapel' => $jadwal['nama_mapel'], 'jam_ke' => $jamKeParam, 'tanggal' => $tanggalHariIni, 'from' => 'dashboard']) }}" class="action-btn btn-izin-done">
                                            <i class="fas fa-eye"></i> Lihat Izin
                                        </a>
                                    @else
                                        <a href="{{ route('guru.izin-guru.index', ['id_rombel' => $jadwal['id_rombel'], 'mapel' => $jadwal['nama_mapel'], 'jam_ke' => $jamKeParam, 'tanggal' => $tanggalHariIni, 'from' => 'dashboard']) }}" class="action-btn btn-izin-do">
                                            <i class="fas fa-user-clock"></i> Izin Guru
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleScheduleCard(card) {
        // Don't toggle if clicking on a link/button inside the card
        if (event.target.closest('.action-btn') || event.target.closest('a')) {
            return;
        }
        card.classList.toggle('collapsed');
    }
    
    // Expand all cards on desktop, collapse on mobile
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth > 768) {
            document.querySelectorAll('.schedule-card').forEach(function(card) {
                card.classList.remove('collapsed');
            });
        }
    });
</script>
@endpush
