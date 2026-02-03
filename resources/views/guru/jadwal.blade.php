@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@push('styles')
<style>
/* ============================
   JADWAL PELAJARAN GURU STYLES
============================ */

/* HEADER SECTION - Green gradient like other guru pages */
.jadwal-header-section {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    color: white;
    text-align: center;
}

.jadwal-header-section .header-icon-large {
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

.jadwal-header-section .page-title {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 10px 0;
    color: white;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* TOGGLE BUTTON STYLE */
.header-actions {
    display: flex;
    justify-content: center;
    width: 100%;
    margin-top: 20px;
}

.view-toggle {
    display: flex;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    padding: 4px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    max-width: 400px;
    width: 100%;
}

.toggle-btn {
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    flex: 1;
    justify-content: center;
}

.toggle-btn:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    text-decoration: none;
}

.toggle-btn.active {
    background: rgba(255, 255, 255, 0.25);
    color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

.stat-icon.primary {
    background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
}

.stat-icon.success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-icon.warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-info {
    flex: 1;
}

.stat-info h3 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.stat-info p {
    margin: 6px 0 0 0;
    color: #6b7280;
    font-size: 13px;
    font-weight: 500;
}

/* FILTER BAR */
.filter-bar {
    background: white;
    padding: 20px 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.filter-row {
    display: flex;
    align-items: flex-end;
    gap: 20px;
    flex-wrap: wrap;
}

.filter-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
    min-width: 180px;
}

.filter-label {
    font-size: 12px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0;
}

.modern-select {
    padding: 10px 15px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    transition: all 0.3s ease;
}

.modern-select:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.filter-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 8px;
}

/* DAY SECTION */
.day-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: 25px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.day-header {
    background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
    padding: 20px 25px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.day-title {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.day-title i {
    font-size: 24px;
    opacity: 0.9;
}

.day-title h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: white;
}

.day-badge {
    background: rgba(255, 255, 255, 0.2);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

/* ROMBEL CARDS GRID */
.rombel-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 20px;
    padding: 25px;
}

/* ROMBEL CARD - Light Green Theme */
.rombel-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    position: relative;
}

.rombel-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.rombel-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    border-color: #10b981;
}

/* Light Green Header for Rombel Card */
.rombel-card-header {
    padding: 20px;
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-bottom: 1px solid #a7f3d0;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.rombel-card-header:hover {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
}

.rombel-info {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    flex: 1;
}

.rombel-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    flex-shrink: 0;
}

.rombel-details h3 {
    margin: 0 0 4px 0;
    font-size: 18px;
    font-weight: 700;
    color: #065f46;
    line-height: 1.2;
}

.rombel-jadwal-count {
    font-size: 12px;
    color: #6b7280;
    font-weight: 500;
}

/* STATISTICS ROW */
.rombel-statistics-row {
    display: flex;
    gap: 8px;
    padding: 15px 20px;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
}

.rombel-stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    flex: 1;
    min-width: 0;
}

.rombel-stat-item:hover {
    border-color: #10b981;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.1);
}

.rombel-stat-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: white;
    flex-shrink: 0;
}

.rombel-stat-item:nth-child(1) .rombel-stat-icon {
    background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
}

.rombel-stat-item:nth-child(2) .rombel-stat-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.rombel-stat-item:nth-child(3) .rombel-stat-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.rombel-stat-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
    flex: 1;
}

.rombel-stat-value {
    font-size: 14px;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.rombel-stat-label {
    font-size: 9px;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

/* CARD CONTENT */
.rombel-card-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
    opacity: 0;
}

.rombel-card.expanded .rombel-card-content {
    max-height: 2000px;
    opacity: 1;
}

.jadwal-list {
    display: flex;
    flex-direction: column;
}

.jadwal-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 20px;
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.2s ease;
}

.jadwal-item:hover {
    background: #ecfdf5;
}

.jadwal-item:last-child {
    border-bottom: none;
}

.jadwal-time {
    flex-shrink: 0;
    min-width: 80px;
}

.jam-ke {
    background: #d1fae5;
    color: #047857;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    display: inline-block;
    text-align: center;
    min-width: 70px;
}

.jadwal-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.mapel-name {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #064e3b;
    font-size: 14px;
}

.mapel-name i {
    color: #10b981;
    font-size: 12px;
    width: 14px;
}

.guru-info {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #6b7280;
}

.guru-info i {
    color: #9ca3af;
    font-size: 11px;
    width: 14px;
}

/* ROMBEL CARD FOOTER */
.rombel-card-footer {
    padding: 15px 20px;
    background: #f8fafc;
    border-top: 1px solid #e5e7eb;
}

.rombel-summary {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.summary-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #6b7280;
    font-weight: 500;
}

.summary-item i {
    color: #9ca3af;
    font-size: 11px;
}

/* EMPTY STATES */
.empty-day-state,
.empty-state {
    text-align: center;
    color: #6b7280;
}

.empty-day-state {
    padding: 60px 20px;
}

.empty-state {
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin: 25px 0;
}

.empty-icon {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 15px;
}

.empty-text h3,
.empty-state h3 {
    margin: 0 0 10px 0;
    color: #1f2937;
    font-size: 18px;
}

.empty-text p,
.empty-state p {
    margin: 0;
    font-size: 14px;
    line-height: 1.5;
}

/* RESPONSIVE DESIGN - MOBILE */
@media (max-width: 768px) {
    .jadwal-header-section {
        padding: 20px 15px;
        margin-bottom: 15px;
    }

    .jadwal-header-section .header-icon-large {
        width: 60px;
        height: 60px;
        font-size: 28px;
        margin-bottom: 15px;
    }

    .jadwal-header-section .page-title {
        font-size: 20px;
    }

    .view-toggle {
        max-width: 100%;
    }

    .toggle-btn {
        padding: 10px 16px;
        font-size: 12px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 15px;
    }

    .stat-card {
        padding: 12px 8px;
        flex-direction: column;
        text-align: center;
        gap: 10px;
        min-height: auto;
        justify-content: flex-start;
        align-items: center;
    }

    .stat-icon {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }

    .stat-info h3 {
        font-size: 14px;
    }

    .stat-info p {
        font-size: 9px;
    }

    .filter-bar {
        padding: 12px;
        margin-bottom: 15px;
    }

    .filter-row {
        flex-direction: column;
        gap: 12px;
    }

    .filter-item {
        width: 100%;
        flex-direction: row;
        align-items: center;
        gap: 12px;
    }

    .filter-label {
        font-size: 11px;
        min-width: 100px;
        margin-bottom: 0;
    }

    .modern-select {
        font-size: 11px;
        padding: 8px 12px;
    }

    .day-section {
        margin-bottom: 15px;
    }

    .day-header {
        padding: 15px;
    }

    .day-title h2 {
        font-size: 16px;
    }

    .day-title i {
        font-size: 18px;
    }

    .day-badge {
        font-size: 11px;
        padding: 5px 10px;
    }

    .rombel-cards-grid {
        grid-template-columns: 1fr;
        padding: 15px;
        gap: 15px;
    }

    .rombel-card-header {
        padding: 12px;
    }

    .rombel-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }

    .rombel-details h3 {
        font-size: 14px;
    }

    .rombel-jadwal-count {
        font-size: 10px;
    }

    .rombel-statistics-row {
        flex-direction: row;
        gap: 6px;
        padding: 10px 12px;
    }

    .rombel-stat-item {
        flex-direction: column;
        gap: 4px;
        padding: 6px 4px;
        text-align: center;
        min-height: 50px;
        justify-content: center;
    }

    .rombel-stat-icon {
        width: 18px;
        height: 18px;
        font-size: 9px;
    }

    .rombel-stat-value {
        font-size: 10px;
    }

    .rombel-stat-label {
        font-size: 7px;
    }

    .jadwal-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        padding: 10px 12px;
    }

    .jadwal-time,
    .jam-ke,
    .jadwal-details {
        width: 100%;
    }

    .jam-ke {
        text-align: center;
        font-size: 10px;
        padding: 5px 10px;
    }

    .mapel-name {
        font-size: 12px;
    }

    .mapel-name i {
        font-size: 10px;
    }

    .guru-info {
        font-size: 10px;
    }

    .guru-info i {
        font-size: 9px;
    }

    .rombel-card-footer {
        padding: 10px 12px;
    }

    .summary-item {
        font-size: 10px;
    }

    .summary-item i {
        font-size: 9px;
    }

    .empty-icon {
        font-size: 36px;
    }

    .empty-text h3,
    .empty-state h3 {
        font-size: 14px;
    }

    .empty-text p,
    .empty-state p {
        font-size: 10px;
    }
}

@media (max-width: 480px) {
    .jadwal-header-section .header-icon-large {
        width: 50px;
        height: 50px;
        font-size: 24px;
        margin-bottom: 10px;
    }

    .jadwal-header-section .page-title {
        font-size: 18px;
    }

    .rombel-stat-item {
        padding: 5px 3px;
        min-height: 45px;
    }

    .stat-info h3 {
        font-size: 12px;
    }

    .stat-info p {
        font-size: 8px;
    }
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')
    
    <div class="main-content">
        <div class="jadwal-pelajaran-page">
            <!-- HEADER SECTION - Green gradient -->
    <div class="jadwal-header-section">
        <div class="header-icon-large">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <h1 class="page-title">{{ $viewTitle }}</h1>
        
        <!-- TOGGLE BUTTON -->
        <div class="header-actions">
            <div class="view-toggle">
                <a href="{{ route('guru.jadwal') }}?{{ http_build_query(array_merge(request()->except('view'), ['view' => 'my_schedule'])) }}" 
                   class="toggle-btn {{ $toggleView === 'my_schedule' ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    Jadwal Saya
                </a>
                <a href="{{ route('guru.jadwal') }}?{{ http_build_query(array_merge(request()->except('view'), ['view' => 'all_schedule'])) }}" 
                   class="toggle-btn {{ $toggleView === 'all_schedule' ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    Semua Jadwal
                </a>
            </div>
        </div>
    </div>

    <!-- STATISTICS SECTION -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalJadwal }}</h3>
                <p>Total Jadwal</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalRombel }}</h3>
                <p>Kelas yang Diampu</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $hariAktif }}</h3>
                <p>Hari Aktif</p>
            </div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="filter-bar">
        <form method="GET" class="filter-row" id="autoFilterForm">
            <input type="hidden" name="view" value="{{ $toggleView }}">
            
            <!-- FILTER TAHUN PELAJARAN -->
            <div class="filter-item">
                <label class="filter-label">Tahun Pelajaran</label>
                <select name="tahun" class="modern-select" onchange="this.form.submit()">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $tahun)
                        <option value="{{ $tahun }}" {{ $tahunFilter == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- FILTER SEMESTER -->
            <div class="filter-item">
                <label class="filter-label">Semester</label>
                <select name="semester" class="modern-select" onchange="this.form.submit()">
                    <option value="">Semua Semester</option>
                    <option value="ganjil" {{ $semesterFilter == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="genap" {{ $semesterFilter == 'genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>

            <!-- FILTER HARI -->
            <div class="filter-item">
                <label class="filter-label">Hari</label>
                <select name="hari" class="modern-select" onchange="this.form.submit()">
                    <option value="">Semua Hari</option>
                    @foreach($hariList as $hari)
                        <option value="{{ $hari }}" {{ $filterHari == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                    @endforeach
                </select>
            </div>

            <!-- FILTER ACTIONS -->
            <div class="filter-actions">
                <a href="{{ route('guru.jadwal') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-refresh"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- SCHEDULE CONTENT -->
    @if(empty($jadwalPerHari))
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-calendar-times"></i>
            </div>
            <h3>Belum Ada Jadwal</h3>
            <p>Tidak ada jadwal pelajaran yang ditemukan dengan filter yang dipilih.</p>
        </div>
    @else
        @foreach($jadwalPerHari as $hariData)
            <div class="day-section">
                <div class="day-header">
                    <div class="day-title">
                        <i class="fas fa-calendar-day"></i>
                        <h2>{{ $hariData['hari'] }}</h2>
                    </div>
                    <div class="day-badge">
                        {{ $hariData['count'] }} Jadwal
                    </div>
                </div>

                <div class="rombel-cards-grid">
                    @foreach($hariData['rombel'] as $rombel)
                        <div class="rombel-card" data-rombel="{{ $rombel['rombel_id'] }}">
                            <!-- CARD HEADER -->
                            <div class="rombel-card-header" data-rombel="{{ $rombel['rombel_id'] }}">
                                <div class="rombel-info">
                                    <div class="rombel-icon">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                    <div class="rombel-details">
                                        <h3>{{ $rombel['nama_rombel'] }}</h3>
                                        <span class="rombel-jadwal-count">{{ $rombel['jadwal_count'] }} mata pelajaran</span>
                                    </div>
                                </div>
                            </div>

                            <!-- STATISTICS ROW -->
                            <div class="rombel-statistics-row">
                                <div class="rombel-stat-item">
                                    <div class="rombel-stat-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="rombel-stat-info">
                                        <div class="rombel-stat-value">{{ $rombel['jadwal_count'] }}</div>
                                        <div class="rombel-stat-label">Total Jam</div>
                                    </div>
                                </div>
                                <div class="rombel-stat-item">
                                    <div class="rombel-stat-icon">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="rombel-stat-info">
                                        <div class="rombel-stat-value">{{ $rombel['mapel_count'] }}</div>
                                        <div class="rombel-stat-label">Mapel</div>
                                    </div>
                                </div>
                                <div class="rombel-stat-item">
                                    <div class="rombel-stat-icon">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <div class="rombel-stat-info">
                                        <div class="rombel-stat-value">{{ $rombel['guru_count'] }}</div>
                                        <div class="rombel-stat-label">Guru</div>
                                    </div>
                                </div>
                            </div>

                            <!-- CARD BODY - COLLAPSIBLE -->
                            <div class="rombel-card-content">
                                <div class="jadwal-list">
                                    @foreach($rombel['jadwal'] as $jadwal)
                                        <div class="jadwal-item">
                                            <div class="jadwal-time">
                                                <span class="jam-ke">{{ $jadwal['jam_text'] }}</span>
                                            </div>
                                            <div class="jadwal-details">
                                                <div class="mapel-name">
                                                    <i class="fas fa-book"></i>
                                                    {{ $jadwal['nama_mapel'] }}
                                                </div>
                                                <div class="guru-info">
                                                    <i class="fas fa-user"></i>
                                                    {{ $jadwal['nama_guru'] }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="rombel-card-footer">
                                <div class="rombel-summary">
                                    <div class="summary-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>{{ $hariData['hari'] }}</span>
                                    </div>
                                    <div class="summary-item">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                        <span>{{ $rombel['nama_rombel'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Collapse Management untuk Kartu Rombel
    const storageKey = 'rombel_collapse_state';
    let collapseState = JSON.parse(localStorage.getItem(storageKey)) || {};
    
    // Fungsi untuk menyimpan state
    function saveState() {
        localStorage.setItem(storageKey, JSON.stringify(collapseState));
    }
    
    // Fungsi untuk toggle dengan animasi yang stabil
    function toggleRombelCard(rombelId) {
        const card = document.querySelector(`.rombel-card[data-rombel="${rombelId}"]`);
        const content = card.querySelector('.rombel-card-content');
        
        const isExpanded = collapseState[rombelId] || false;
        
        if (isExpanded) {
            // Collapse
            content.style.maxHeight = content.scrollHeight + 'px';
            content.offsetHeight; // reflow
            content.style.maxHeight = '0';
            content.style.opacity = '0';
            
            setTimeout(() => {
                card.classList.remove('expanded');
            }, 300);
            
            collapseState[rombelId] = false;
        } else {
            // Expand
            card.classList.add('expanded');
            content.style.maxHeight = '0';
            content.style.opacity = '0';
            content.offsetHeight; // reflow
            content.style.maxHeight = content.scrollHeight + 'px';
            content.style.opacity = '1';
            
            setTimeout(() => {
                if (card.classList.contains('expanded')) {
                    content.style.maxHeight = '';
                }
            }, 500);
            
            collapseState[rombelId] = true;
        }
        
        saveState();
    }
    
    // Setup event listeners
    document.querySelectorAll('.rombel-card-header').forEach(header => {
        header.addEventListener('click', function(e) {
            const rombelId = this.dataset.rombel;
            toggleRombelCard(rombelId);
            e.stopPropagation();
        });
    });
    
    // Apply saved state
    function applySavedState() {
        document.querySelectorAll('.rombel-card').forEach(card => {
            const rombelId = card.dataset.rombel;
            const content = card.querySelector('.rombel-card-content');
            
            if (!collapseState[rombelId]) {
                content.style.maxHeight = '0';
                content.style.opacity = '0';
                card.classList.remove('expanded');
            } else {
                card.classList.add('expanded');
                content.style.maxHeight = '';
                content.style.opacity = '1';
            }
        });
    }
    
    applySavedState();
    
    // Window Resize Handler
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            document.querySelectorAll('.rombel-card.expanded .rombel-card-content').forEach(content => {
                content.style.maxHeight = '';
                const contentHeight = content.scrollHeight;
                content.style.maxHeight = contentHeight + 'px';
                
                setTimeout(() => {
                    if (content.closest('.rombel-card').classList.contains('expanded')) {
                        content.style.maxHeight = '';
                    }
                }, 100);
            });
        }, 250);
    });
});
</script>
@endpush
