@extends('layouts.app')

@section('title', 'Rekap Presensi')

@push('styles')
<style>
/* HEADER SECTION - Green gradient */
.rekap-header-section {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    color: white;
    text-align: center;
}

.rekap-header-section .header-icon-large {
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

.rekap-header-section .page-title {
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
    border-color: #10b981;
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
    font-size: 16px;
    font-weight: 700;
    color: #1f2937;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.stat-info p { margin: 4px 0 0 0; color: #6b7280; font-size: 12px; font-weight: 500; }

/* FILTER SECTION */
.filter-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: flex-end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
    min-width: 150px;
}

.filter-label {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
}

.modern-input {
    padding: 10px 15px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.modern-input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.btn-filter {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-back {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-back:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
    color: white;
}

/* REKAP CARDS */
.rekap-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
}

.rekap-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.rekap-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

.rekap-card.success { border-left: 4px solid #10b981; }
.rekap-card.warning { border-left: 4px solid #f59e0b; }
.rekap-card.danger { border-left: 4px solid #ef4444; }

.rekap-card-header {
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}

.student-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 18px;
    flex-shrink: 0;
    overflow: hidden;
}

.student-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.student-info { flex: 1; min-width: 0; }
.student-name {
    font-weight: 700;
    font-size: 15px;
    color: #1f2937;
    margin: 0 0 4px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.student-nisn {
    font-size: 12px;
    color: #6b7280;
    margin: 0;
}

.persentase-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 12px;
    flex-shrink: 0;
}

.rekap-card.success .persentase-circle { background: #d1fae5; color: #059669; }
.rekap-card.warning .persentase-circle { background: #fef3c7; color: #d97706; }
.rekap-card.danger .persentase-circle { background: #fee2e2; color: #dc2626; }

.rekap-card-body {
    padding: 15px;
}

.rekap-stats-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 8px;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 8px 4px;
    border-radius: 8px;
    text-align: center;
}

.stat-item i { font-size: 14px; margin-bottom: 4px; }
.stat-item strong { font-size: 14px; font-weight: 700; }
.stat-item small { font-size: 9px; color: #6b7280; }

.stat-item.hadir { background: #d1fae5; color: #059669; }
.stat-item.dispen { background: #dbeafe; color: #1d4ed8; }
.stat-item.izin { background: #fef3c7; color: #d97706; }
.stat-item.sakit { background: #e0e7ff; color: #4338ca; }
.stat-item.alfa { background: #fee2e2; color: #dc2626; }
.stat-item.bolos { background: #fce7f3; color: #be185d; }

/* EMPTY STATE */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.empty-icon { font-size: 48px; color: #d1d5db; margin-bottom: 15px; }
.empty-state h3 { margin: 0 0 10px 0; color: #1f2937; font-size: 18px; }
.empty-state p { margin: 0; color: #6b7280; font-size: 14px; }

/* RESPONSIVE */
@media (max-width: 768px) {
    .rekap-header-section { padding: 20px 15px; }
    .rekap-header-section .header-icon-large {
        width: 60px; height: 60px; font-size: 28px; margin-bottom: 15px;
    }
    .rekap-header-section .page-title { font-size: 20px; }
    
    .stats-grid { grid-template-columns: repeat(3, 1fr); gap: 8px; }
    .stat-card {
        flex-direction: column; text-align: center; padding: 10px; gap: 8px;
    }
    .stat-icon { width: 35px; height: 35px; font-size: 14px; }
    .stat-info h3 { font-size: 11px; white-space: normal; }
    .stat-info p { font-size: 9px; }
    
    .filter-row { flex-direction: column; }
    .filter-group { width: 100%; flex-direction: row; align-items: center; gap: 12px; }
    .filter-label { min-width: 100px; }
    
    .rekap-cards-grid { grid-template-columns: 1fr; gap: 15px; }
    
    .rekap-stats-grid { grid-template-columns: repeat(3, 1fr); }
    
    .student-avatar { width: 40px; height: 40px; font-size: 14px; }
    .student-name { font-size: 13px; }
    .student-nisn { font-size: 10px; }
    .persentase-circle { width: 40px; height: 40px; font-size: 10px; }
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')
    
    <div class="main-content">
        <div class="rekap-presensi-page">
            <!-- HEADER SECTION -->
            <div class="rekap-header-section">
                <div class="header-icon-large">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h1 class="page-title">Rekap Presensi</h1>
            </div>

            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $namaRombel }}</h3>
                        <p>Rombel</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $mapel }}</h3>
                        <p>Mata Pelajaran</p>
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

            <!-- ACTION BUTTONS & FILTER -->
            <div class="filter-card">
                <form method="GET" class="filter-row">
                    <input type="hidden" name="id_rombel" value="{{ $idRombel }}">
                    <input type="hidden" name="mapel" value="{{ $mapel }}">
                    
                    <a href="{{ route('guru.tugas-mengajar') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    
                    <div class="filter-group">
                        <label class="filter-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="modern-input" 
                               value="{{ $tanggalMulai }}" min="{{ $minDate }}" max="{{ $maxDate }}">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="modern-input" 
                               value="{{ $tanggalSelesai }}" min="{{ $minDate }}" max="{{ $maxDate }}">
                    </div>
                    
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i> Filter Data
                    </button>
                </form>
            </div>

            <!-- REKAP CARDS -->
            @if(count($rekapData) > 0)
                <div class="rekap-cards-grid">
                    @foreach($rekapData as $siswa)
                        @php
                            $totalKehadiran = $siswa->hadir + $siswa->dispen;
                            $persentase = $siswa->total_presensi > 0 
                                ? round(($totalKehadiran / $siswa->total_presensi) * 100, 1) 
                                : 0;
                            
                            $warnaKartu = $persentase >= 90 ? 'success' : ($persentase >= 75 ? 'warning' : 'danger');
                            
                            $initials = collect(explode(' ', $siswa->nama))
                                ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                                ->take(2)
                                ->join('');
                            
                            $hasFoto = $siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto);
                        @endphp
                        
                        <div class="rekap-card {{ $warnaKartu }}">
                            <div class="rekap-card-header">
                                <div class="student-avatar">
                                    @if($hasFoto)
                                        <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}">
                                    @else
                                        {{ $initials ?: 'S' }}
                                    @endif
                                </div>
                                <div class="student-info">
                                    <h4 class="student-name">{{ $siswa->nama }}</h4>
                                    <p class="student-nisn">NISN: {{ $siswa->nisn }}</p>
                                </div>
                                <div class="persentase-circle">
                                    {{ $persentase }}%
                                </div>
                            </div>
                            
                            <div class="rekap-card-body">
                                <div class="rekap-stats-grid">
                                    <div class="stat-item hadir">
                                        <i class="fas fa-check-circle"></i>
                                        <strong>{{ $siswa->hadir }}</strong>
                                        <small>Hadir</small>
                                    </div>
                                    <div class="stat-item dispen">
                                        <i class="fas fa-user-tie"></i>
                                        <strong>{{ $siswa->dispen }}</strong>
                                        <small>Dispen</small>
                                    </div>
                                    <div class="stat-item izin">
                                        <i class="fas fa-clock"></i>
                                        <strong>{{ $siswa->izin }}</strong>
                                        <small>Izin</small>
                                    </div>
                                    <div class="stat-item sakit">
                                        <i class="fas fa-first-aid"></i>
                                        <strong>{{ $siswa->sakit }}</strong>
                                        <small>Sakit</small>
                                    </div>
                                    <div class="stat-item alfa">
                                        <i class="fas fa-times-circle"></i>
                                        <strong>{{ $siswa->alfa }}</strong>
                                        <small>Alfa</small>
                                    </div>
                                    <div class="stat-item bolos">
                                        <i class="fas fa-running"></i>
                                        <strong>{{ $siswa->bolos }}</strong>
                                        <small>Bolos</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Tidak Ada Data Rekap</h3>
                    <p>Tidak ada data presensi ditemukan untuk periode yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
