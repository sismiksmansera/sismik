@extends('layouts.app')

@section('title', 'Penugasan Mengajar')

@push('styles')
<style>
/* ============================
   TUGAS MENGAJAR STYLES
============================ */

/* HEADER SECTION - Green gradient like other guru pages */
.tugas-header-section {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
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

/* FILTER SECTION */
.filter-section {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.filter-controls {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    align-items: flex-end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
    min-width: 180px;
}

.filter-label {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 6px;
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

/* ASSIGNMENT CARDS GRID */
.assignment-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

/* ASSIGNMENT CARD */
.assignment-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e5e7eb;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.assignment-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    border-color: #10b981;
}

/* CARD HEADER - Green gradient */
.assignment-card .card-header {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 20px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.card-rombel {
    background: rgba(255, 255, 255, 0.25);
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 18px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    align-self: flex-start;
}

.card-rombel i {
    font-size: 16px;
    opacity: 0.9;
}

.card-title {
    display: flex;
    flex-direction: column;
    gap: 6px;
    align-items: flex-start;
    padding-left: 0;
    margin-left: 0;
    width: 100%;
}

.card-title h3 {
    margin: 0;
    padding-left: 0;
    font-size: 16px;
    font-weight: 600;
    line-height: 1.3;
    display: flex;
    align-items: center;
    gap: 8px;
    color: white;
    text-align: left;
}

.card-title h3 i {
    font-size: 14px;
    opacity: 0.9;
}

.periode-badge {
    background: rgba(255, 255, 255, 0.2);
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
    backdrop-filter: blur(10px);
    margin-left: 0;
}

/* CARD BODY */
.assignment-card .card-body {
    padding: 20px;
}

.info-section {
    margin-bottom: 18px;
}

.info-section:last-child {
    margin-bottom: 0;
}

.info-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-label i {
    width: 14px;
    text-align: center;
}

.info-value {
    font-size: 14px;
    color: #1f2937;
    font-weight: 500;
}

/* JADWAL GRID */
.jadwal-grid {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.jadwal-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 3px solid #10b981;
    transition: all 0.2s ease;
}

.jadwal-item:hover {
    background: #ecfdf5;
    transform: translateX(2px);
}

.jadwal-day {
    font-weight: 700;
    color: #059669;
    font-size: 13px;
}

.jadwal-time {
    color: #6b7280;
    font-size: 12px;
}

.no-schedule {
    padding: 12px;
    background: #fef3c7;
    border-radius: 8px;
    color: #92400e;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.jam-count {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
    display: inline-block;
}

/* CARD FOOTER */
.assignment-card .card-footer {
    padding: 15px 20px;
    background: #f8fafc;
    border-top: 1px solid #e5e7eb;
}

.action-buttons-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.btn-action {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    width: 100%;
}

.btn-action.btn-info {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.btn-action.btn-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.btn-action.btn-primary {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
}

.btn-action.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-action.btn-secondary {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-action.btn-disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-action.btn-disabled:hover {
    transform: none;
    box-shadow: none;
}

/* TOTAL SUMMARY CARD */
.total-summary-card {
    background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
    border-radius: 12px;
    padding: 20px 25px;
    margin-top: 20px;
}

.summary-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

.summary-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: white;
}

.summary-text {
    flex: 1;
    color: white;
}

.summary-text h4 {
    margin: 0 0 5px 0;
    font-size: 16px;
    font-weight: 600;
}

.summary-text p {
    margin: 0;
    font-size: 14px;
    opacity: 0.9;
}

.summary-badge {
    background: rgba(255,255,255,0.2);
    padding: 10px 20px;
    border-radius: 10px;
    color: white;
    font-weight: 700;
    font-size: 14px;
}

/* EMPTY STATE */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.empty-icon {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 15px;
}

.empty-state h3 {
    margin: 0 0 10px 0;
    color: #1f2937;
    font-size: 18px;
}

.empty-state p {
    margin: 0;
    color: #6b7280;
    font-size: 14px;
}

/* RESPONSIVE DESIGN - MOBILE */
@media (max-width: 768px) {
    .tugas-header-section {
        padding: 20px 15px;
        margin-bottom: 15px;
    }

    .tugas-header-section .header-icon-large {
        width: 60px;
        height: 60px;
        font-size: 28px;
        margin-bottom: 15px;
    }

    .tugas-header-section .page-title {
        font-size: 20px;
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

    .filter-section {
        padding: 15px;
        margin-bottom: 15px;
    }

    .filter-controls {
        flex-direction: column;
        gap: 12px;
    }

    .filter-group {
        width: 100%;
        flex-direction: row;
        align-items: center;
        gap: 12px;
    }

    .filter-label {
        min-width: 100px;
        font-size: 11px;
    }

    .modern-select {
        font-size: 11px;
        padding: 8px 12px;
    }

    .assignment-cards-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .assignment-card .card-header {
        padding: 15px;
    }

    .card-rombel {
        font-size: 14px;
        padding: 8px 12px;
    }

    .card-title h3 {
        font-size: 13px;
    }

    .periode-badge {
        font-size: 10px;
    }

    .assignment-card .card-body {
        padding: 15px;
    }

    .info-label {
        font-size: 10px;
    }

    .info-value {
        font-size: 12px;
    }

    .jadwal-item {
        padding: 6px 10px;
    }

    .jadwal-day {
        font-size: 11px;
    }

    .jadwal-time {
        font-size: 10px;
    }

    .jam-count {
        font-size: 12px;
        padding: 6px 12px;
    }

    .assignment-card .card-footer {
        padding: 12px 15px;
    }

    .action-buttons-group {
        gap: 6px;
    }

    .btn-action {
        font-size: 10px;
        padding: 6px 10px;
    }

    .total-summary-card {
        padding: 15px 20px;
    }

    .summary-content {
        flex-wrap: wrap;
        gap: 15px;
    }

    .summary-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }

    .summary-text h4 {
        font-size: 14px;
    }

    .summary-text p {
        font-size: 12px;
    }

    .summary-badge {
        font-size: 12px;
        padding: 8px 16px;
    }

    .empty-icon {
        font-size: 36px;
    }

    .empty-state h3 {
        font-size: 14px;
    }

    .empty-state p {
        font-size: 10px;
    }
}

@media (max-width: 480px) {
    .tugas-header-section .header-icon-large {
        width: 50px;
        height: 50px;
        font-size: 24px;
        margin-bottom: 10px;
    }

    .tugas-header-section .page-title {
        font-size: 18px;
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
        <div class="tugas-mengajar-page">
            <!-- HEADER SECTION - Green gradient -->
            <div class="tugas-header-section">
                <div class="header-icon-large">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h1 class="page-title">Penugasan Mengajar</h1>
            </div>

            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $totalRombel }}</h3>
                        <p>Rombel Diajar</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $totalMapel }}</h3>
                        <p>Mata Pelajaran</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $totalJam }}</h3>
                        <p>Total Jam/Minggu</p>
                    </div>
                </div>
            </div>

            <!-- FILTER SECTION -->
            <div class="filter-section">
                <form method="GET" class="filter-controls">
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-calendar-alt"></i> Tahun Pelajaran
                        </label>
                        <select name="tahun" class="modern-select" onchange="this.form.submit()">
                            <option value="">Semua Tahun</option>
                            @foreach($tahunList as $tahun)
                                <option value="{{ $tahun }}" {{ $tahunFilter == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-sync-alt"></i> Semester
                        </label>
                        <select name="semester" class="modern-select" onchange="this.form.submit()">
                            <option value="">Semua Semester</option>
                            <option value="ganjil" {{ $semesterFilter == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="genap" {{ $semesterFilter == 'genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- CONTENT SECTION -->
            @if(count($assignmentData) > 0)
                <div class="assignment-cards-grid">
                    @foreach($assignmentData as $assignment)
                        <div class="assignment-card">
                            <!-- CARD HEADER -->
                            <div class="card-header">
                                <div class="card-rombel">
                                    <i class="fas fa-school"></i> {{ $assignment['nama_rombel'] }}
                                </div>
                                <div class="card-title">
                                    <h3><i class="fas fa-book"></i> {{ $assignment['nama_mapel'] }}</h3>
                                    <span class="periode-badge">
                                        {{ $assignment['tahun_pelajaran'] }} - {{ ucfirst($assignment['semester']) }}
                                    </span>
                                </div>
                            </div>

                            <!-- CARD BODY -->
                            <div class="card-body">
                                <!-- JADWAL INFO -->
                                <div class="info-section">
                                    <div class="info-label">
                                        <i class="fas fa-clock"></i>
                                        Jadwal Mengajar
                                    </div>
                                    <div class="info-value">
                                        @if(!empty($assignment['jadwal']))
                                            <div class="jadwal-grid">
                                                @foreach($assignment['jadwal'] as $jadwal)
                                                    <div class="jadwal-item">
                                                        <span class="jadwal-day">{{ $jadwal['hari'] }}</span>
                                                        <span class="jadwal-time">{{ $jadwal['jam_text'] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="no-schedule">
                                                <i class="fas fa-clock"></i> Jadwal belum diatur
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- JAM INFO -->
                                <div class="info-section">
                                    <div class="info-label">
                                        <i class="fas fa-chart-bar"></i>
                                        Beban Mengajar
                                    </div>
                                    <div class="info-value">
                                        <span class="jam-count">{{ $assignment['jam_count'] }} jam/minggu</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- TOTAL SUMMARY -->
                <div class="total-summary-card">
                    <div class="summary-content">
                        <div class="summary-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="summary-text">
                            <h4>Total Beban Mengajar</h4>
                            <p>Per Minggu: <strong>{{ $totalJamTable }} jam</strong></p>
                        </div>
                        <div class="summary-badge">
                            {{ $totalMapel }} Mapel
                        </div>
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>Belum Ada Penugasan</h3>
                    <p>Anda belum memiliki jadwal mengajar untuk periode yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
