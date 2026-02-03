@extends('layouts.app')

@section('title', 'Tugas Tambahan')

@push('styles')
<style>
/* HEADER SECTION - Teal gradient */
.tugas-header-section {
    background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    color: white;
    text-align: center;
}

.tugas-header-section .header-icon-large {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: white;
    margin: 0 auto 20px;
}

.tugas-header-section .page-title {
    font-size: 28px;
    font-weight: 700;
    margin: 0;
    color: white;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* STATS GRID */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e5e7eb;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    flex-shrink: 0;
}

.stat-icon.primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }

.stat-info { flex: 1; min-width: 0; }
.stat-info h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
}
.stat-info p { margin: 4px 0 0 0; color: #6b7280; font-size: 12px; font-weight: 500; }

/* SECTION HEADER */
.section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.section-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.section-icon.blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.section-icon.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

.section-header h2 {
    margin: 0;
    color: #1f2937;
    font-size: 1.25rem;
}

/* TASK CARDS GRID */
.task-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

/* TASK CARD */
.task-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.task-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}

.task-card-header {
    padding: 20px;
    color: white;
    display: flex;
    align-items: center;
    gap: 15px;
}

.task-card-header.ekstra { background: linear-gradient(135deg, var(--card-color) 0%, var(--card-color-dark) 100%); }
.task-card-header.wali { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

.task-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.task-info { flex: 1; }
.task-info h3 {
    margin: 0 0 6px 0;
    font-size: 1.1rem;
    font-weight: 700;
}

.task-badge {
    background: rgba(255,255,255,0.25);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.task-card-body {
    padding: 20px;
}

.task-stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 15px;
}

.task-stat-item {
    background: #f8fafc;
    padding: 12px;
    border-radius: 10px;
    text-align: center;
    transition: all 0.2s ease;
    text-decoration: none;
    display: block;
}

.task-stat-item:hover {
    transform: translateY(-2px);
}

.task-stat-item.clickable:hover {
    background: rgba(16, 185, 129, 0.15);
}

.task-stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

.task-stat-value.green { color: #10b981; }
.task-stat-value.blue { color: #3b82f6; }
.task-stat-value.yellow { color: #f59e0b; }
.task-stat-value.dark { color: #1f2937; }

.task-stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    margin-top: 4px;
}

/* EMPTY STATE */
.empty-state {
    background: white;
    border-radius: 16px;
    padding: 60px 30px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.empty-icon-wrapper {
    width: 100px;
    height: 100px;
    background: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
}

.empty-icon-wrapper i {
    font-size: 40px;
    color: #9ca3af;
}

.empty-state h3 {
    margin: 0 0 10px 0;
    color: #1f2937;
    font-size: 1.25rem;
}

.empty-state p {
    margin: 0 0 25px 0;
    color: #6b7280;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.btn-home {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #8b5cf6;
    color: white;
    padding: 12px 25px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-home:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    color: white;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .tugas-header-section { padding: 20px 15px; }
    .tugas-header-section .header-icon-large {
        width: 60px; height: 60px; font-size: 28px; margin-bottom: 15px;
    }
    .tugas-header-section .page-title { font-size: 20px; }
    
    .stats-grid { grid-template-columns: repeat(3, 1fr); gap: 8px; }
    .stat-card {
        flex-direction: column; text-align: center; padding: 12px 8px; gap: 8px;
    }
    .stat-icon { width: 35px; height: 35px; font-size: 14px; }
    .stat-info h3 { font-size: 16px; }
    .stat-info p { font-size: 10px; }
    
    .task-cards-grid { grid-template-columns: 1fr; gap: 15px; }
    
    .task-info h3 { font-size: 0.95rem; }
    .task-badge { font-size: 0.65rem; }
    .task-stat-value { font-size: 1rem; }
    .task-stat-label { font-size: 0.7rem; }
    
    .section-header h2 { font-size: 1rem; }
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')
    
    <div class="main-content">
        <div class="tugas-tambahan-page">
            <!-- HEADER SECTION -->
            <div class="tugas-header-section">
                <div class="header-icon-large">
                    <i class="fas fa-tasks"></i>
                </div>
                <h1 class="page-title">Tugas Tambahan</h1>
            </div>

            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-futbol"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ count($tugasPembina) }}</h3>
                        <p>Pembina Ekstrakurikuler</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ count($tugasWaliKelas) }}</h3>
                        <p>Wali Kelas</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $tahunPelajaran }}</h3>
                        <p>{{ ucfirst($semesterAktif) }}</p>
                    </div>
                </div>
            </div>

            @if($totalTugas > 0)
                <!-- PEMBINA EKSTRAKURIKULER -->
                @if(count($tugasPembina) > 0)
                    <div class="section-header">
                        <div class="section-icon blue">
                            <i class="fas fa-futbol"></i>
                        </div>
                        <h2>Pembina Ekstrakurikuler</h2>
                    </div>
                    
                    <div class="task-cards-grid">
                        @foreach($tugasPembina as $ekstra)
                            @php
                                $icons = [
                                    'Pramuka' => 'fa-campground', 'Paskibra' => 'fa-flag', 'PMR' => 'fa-heartbeat',
                                    'OSIS' => 'fa-users-cog', 'Basket' => 'fa-basketball-ball', 'Futsal' => 'fa-futbol',
                                    'Voli' => 'fa-volleyball-ball', 'Seni Musik' => 'fa-music', 'Seni Tari' => 'fa-gem',
                                    'English Club' => 'fa-language', 'IT Club' => 'fa-laptop-code', 'KIR' => 'fa-flask'
                                ];
                                $colors = [
                                    'Pramuka' => '#3b82f6', 'Paskibra' => '#ef4444', 'PMR' => '#dc2626',
                                    'OSIS' => '#8b5cf6', 'Basket' => '#f59e0b', 'Futsal' => '#10b981',
                                    'Voli' => '#ec4899', 'Seni Musik' => '#06b6d4', 'Seni Tari' => '#f97316',
                                    'English Club' => '#6366f1', 'IT Club' => '#0ea5e9', 'KIR' => '#84cc16'
                                ];
                                $icon = $icons[$ekstra['nama']] ?? 'fa-star';
                                $color = $colors[$ekstra['nama']] ?? '#6b7280';
                            @endphp
                            
                            <div class="task-card" style="--card-color: {{ $color }}; --card-color-dark: {{ $color }}dd;">
                                <div class="task-card-header ekstra" style="background: linear-gradient(135deg, {{ $color }} 0%, {{ $color }}dd 100%);">
                                    <div class="task-icon">
                                        <i class="fas {{ $icon }}"></i>
                                    </div>
                                    <div class="task-info">
                                        <h3>{{ $ekstra['nama'] }}</h3>
                                        <span class="task-badge">
                                            <i class="fas fa-medal"></i> {{ $ekstra['posisi'] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="task-card-body">
                                    <div class="task-stats-grid">
                                        <a href="{{ route('guru.anggota-ekstrakurikuler', ['id' => $ekstra['id']]) }}" class="task-stat-item clickable">
                                            <div class="task-stat-value" style="color: {{ $color }};">{{ $ekstra['jumlah_anggota'] }}</div>
                                            <div class="task-stat-label">Anggota</div>
                                        </a>
                                        <a href="{{ route('guru.lihat-prestasi', ['type' => 'ekstra', 'id' => $ekstra['id']]) }}" class="task-stat-item clickable">
                                            <div class="task-stat-value yellow"><i class="fas fa-trophy"></i> {{ $ekstra['jumlah_prestasi'] }}</div>
                                            <div class="task-stat-label">Prestasi</div>
                                        </a>
                                        <div class="task-stat-item">
                                            <div class="task-stat-value dark">{{ $ekstra['semester'] }}</div>
                                            <div class="task-stat-label">Semester</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- WALI KELAS -->
                @if(count($tugasWaliKelas) > 0)
                    <div class="section-header">
                        <div class="section-icon green">
                            <i class="fas fa-users"></i>
                        </div>
                        <h2>Wali Kelas</h2>
                    </div>
                    
                    <div class="task-cards-grid">
                        @foreach($tugasWaliKelas as $rombel)
                            <div class="task-card">
                                <div class="task-card-header wali">
                                    <div class="task-icon">
                                        <i class="fas fa-chalkboard"></i>
                                    </div>
                                    <div class="task-info">
                                        <h3>{{ $rombel['nama'] }}</h3>
                                        <span class="task-badge">
                                            <i class="fas fa-user-tie"></i> Wali Kelas
                                        </span>
                                    </div>
                                </div>
                                <div class="task-card-body">
                                    <div class="task-stats-grid">
                                        <a href="{{ route('guru.anggota-rombel', ['id' => $rombel['id'], 'tahun' => $tahunPelajaran, 'semester' => $semesterAktif]) }}" class="task-stat-item clickable">
                                            <div class="task-stat-value green">{{ $rombel['jumlah_siswa'] }}</div>
                                            <div class="task-stat-label">Siswa</div>
                                        </a>
                                        <a href="{{ route('guru.lihat-prestasi', ['type' => 'rombel', 'id' => $rombel['id']]) }}" class="task-stat-item clickable">
                                            <div class="task-stat-value yellow"><i class="fas fa-trophy"></i> {{ $rombel['jumlah_prestasi'] }}</div>
                                            <div class="task-stat-label">Prestasi</div>
                                        </a>
                                        <div class="task-stat-item">
                                            <div class="task-stat-value dark">Kelas {{ $rombel['tingkat'] }}</div>
                                            <div class="task-stat-label">Tingkat</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <!-- EMPTY STATE -->
                <div class="empty-state">
                    <div class="empty-icon-wrapper">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3>Belum Ada Tugas Tambahan</h3>
                    <p>
                        Anda belum memiliki tugas tambahan seperti pembina ekstrakurikuler atau wali kelas untuk periode 
                        <strong>{{ $tahunPelajaran }} - {{ ucfirst($semesterAktif) }}</strong>
                    </p>
                    <a href="{{ route('guru.dashboard') }}" class="btn-home">
                        <i class="fas fa-home"></i> Kembali ke Dashboard
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
