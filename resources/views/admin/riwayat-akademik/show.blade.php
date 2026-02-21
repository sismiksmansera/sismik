@extends('layouts.app')

@section('title', 'Riwayat Akademik - ' . $siswa->nama)

@push('styles')
<style>
/* HEADER SECTION */
.content-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 25px 30px;
    margin-bottom: 25px;
    color: white;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.4);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.header-icon {
    width: 70px;
    height: 70px;
    background: rgba(255,255,255,0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    font-size: 28px;
}

.header-info h1 {
    margin: 0 0 8px 0;
    font-size: 1.6rem;
    font-weight: 700;
}

.header-meta {
    display: flex;
    gap: 15px;
    font-size: 0.9rem;
    opacity: 0.9;
    flex-wrap: wrap;
}

.header-periode {
    margin-top: 8px;
    font-size: 0.85rem;
    opacity: 0.8;
}

.header-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background: rgba(255,255,255,0.2);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.btn-header:hover {
    background: rgba(255,255,255,0.3);
    color: white;
}

/* SUMMARY CARDS */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
}

.summary-card {
    background: white;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.summary-card-content {
    display: flex;
    align-items: center;
    gap: 15px;
}

.summary-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.summary-icon.nilai { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.summary-icon.kehadiran { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.summary-icon.ekstra { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.summary-icon.prestasi { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }

.summary-value {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1f2937;
}

.summary-label {
    font-size: 0.8rem;
    color: #6b7280;
}

/* CONTENT SECTION */
.content-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 25px;
    overflow: hidden;
}

.section-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.section-title {
    margin: 0;
    font-size: 1.1rem;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

/* TABLE */
.rekap-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.rekap-table th {
    padding: 12px 15px;
    text-align: center;
    font-weight: 600;
    color: #374151;
    font-size: 0.8rem;
    border-bottom: 2px solid #e5e7eb;
}

.rekap-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #e5e7eb;
}

.rekap-table tbody tr:nth-child(even) {
    background: #fafafa;
}

/* PREDIKAT BADGE */
.predikat-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.8rem;
}

/* PRESENSI GRID */
.presensi-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.presensi-item {
    text-align: center;
    padding: 15px;
    border-radius: 12px;
}

.presensi-value {
    font-size: 1.5rem;
    font-weight: 700;
}

.presensi-label {
    font-size: 0.75rem;
    font-weight: 600;
}

/* TWO COLUMN GRID */
.two-column-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

/* EKSTRA CARD */
.ekstra-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 16px;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid #f59e0b;
    margin-bottom: 12px;
}

/* BK CARD */
.bk-card {
    padding: 16px;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid #3b82f6;
    margin-bottom: 12px;
}

/* PRESTASI CARD */
.prestasi-card {
    padding: 16px;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid #ec4899;
    margin-bottom: 12px;
}

/* PELANGGARAN CARD */
.pelanggaran-card {
    padding: 16px;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid #ef4444;
    margin-bottom: 12px;
}

/* PANGGILAN ORTU CARD */
.panggilan-card {
    padding: 16px;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid #6366f1;
    margin-bottom: 12px;
}

/* LEGEND */
.legend {
    padding: 15px 25px;
    background: #f8fafc;
    border-top: 1px solid #e5e7eb;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
    font-size: 0.75rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 3px;
}

/* MODAL DETAIL NILAI */
.nilai-clickable {
    cursor: pointer;
    transition: all 0.2s;
    border-radius: 8px;
    padding: 4px 8px;
}
.nilai-clickable:hover {
    background: #667eea22;
    transform: scale(1.1);
}
.modal-nilai-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(4px);
}
.modal-nilai-overlay.active {
    display: flex;
}
.modal-nilai-box {
    background: white;
    border-radius: 20px;
    width: 95%;
    max-width: 750px;
    max-height: 85vh;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: modalSlideIn 0.3s ease;
}
@keyframes modalSlideIn {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.modal-nilai-header {
    padding: 20px 25px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-nilai-header h3 {
    margin: 0;
    font-size: 1.1rem;
}
.modal-nilai-close {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 36px; height: 36px;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.2s;
}
.modal-nilai-close:hover {
    background: rgba(255,255,255,0.4);
}
.modal-nilai-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    padding: 15px 25px;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}
.stat-box {
    text-align: center;
    padding: 10px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.stat-box .stat-value {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1f2937;
}
.stat-box .stat-label {
    font-size: 0.7rem;
    color: #6b7280;
    font-weight: 600;
}
.modal-nilai-body {
    padding: 20px 25px;
    max-height: 50vh;
    overflow-y: auto;
}
.detail-nilai-table {
    width: 100%;
    border-collapse: collapse;
}
.detail-nilai-table th {
    padding: 10px 12px;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    border-bottom: 2px solid #e5e7eb;
    text-transform: uppercase;
}
.detail-nilai-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.85rem;
    color: #374151;
}
.detail-nilai-table tbody tr:hover {
    background: #f8fafc;
}
.detail-nilai-table .nilai-cell {
    font-weight: 700;
    text-align: center;
}
.presensi-clickable {
    cursor: pointer;
    transition: all 0.2s;
    border-radius: 6px;
    padding: 2px 6px;
    display: inline-block;
}
.presensi-clickable:hover {
    transform: scale(1.15);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.modal-presensi-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(4px);
}
.modal-presensi-overlay.active {
    display: flex;
}
.modal-presensi-box {
    background: white;
    border-radius: 20px;
    width: 95%;
    max-width: 500px;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: modalSlideIn 0.3s ease;
}
.modal-presensi-header {
    padding: 20px 25px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-presensi-header h3 {
    margin: 0;
    font-size: 1.05rem;
}
.modal-presensi-body {
    padding: 20px 25px;
    max-height: 55vh;
    overflow-y: auto;
}
.presensi-date-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    background: #f8fafc;
    border-radius: 10px;
    margin-bottom: 8px;
    border-left: 3px solid;
}
.presensi-date-item .date-info {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.9rem;
}
.presensi-date-item .day-info {
    font-size: 0.8rem;
    color: #6b7280;
}
.presensi-date-item .guru-info {
    font-size: 0.75rem;
    color: #9ca3af;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .content-header { padding: 20px 15px; }
    .header-content { flex-direction: column; align-items: flex-start; }
    .header-icon { width: 50px; height: 50px; font-size: 20px; }
    .header-info h1 { font-size: 1.2rem; }
    .header-actions { width: 100%; }
    .btn-header { flex: 1; justify-content: center; }
    .presensi-grid { grid-template-columns: repeat(3, 1fr); }
    .modal-nilai-stats { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

@section('content')
<div class="layout">
    @if(request()->routeIs('guru.*'))
        @include('layouts.partials.sidebar-guru')
    @else
        @include('layouts.partials.sidebar-admin')
    @endif
    
    <div class="main-content">
        <div class="content" style="padding: 20px;">
            
            <!-- HEADER SECTION -->
            <div class="content-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="header-info">
                            <h1>Riwayat Akademik Siswa</h1>
                            <div class="header-meta">
                                <span><i class="fas fa-user"></i> {{ $siswa->nama }}</span>
                                <span><i class="fas fa-id-card"></i> NISN: {{ $siswa->nisn }}</span>
                                <span><i class="fas fa-chalkboard"></i> {{ $namaRombel ?: '-' }}</span>
                            </div>
                            <div class="header-periode">
                                <i class="fas fa-calendar-alt"></i> Periode: {{ $periodeAktif->tahun_pelajaran }} - {{ $periodeAktif->semester }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="header-actions">
                        @php
                            $printRoute = request()->routeIs('guru.*') 
                                ? route('guru.riwayat-akademik.print', ['nisn' => $siswa->nisn])
                                : route('admin.riwayat-akademik.print', ['nisn' => $siswa->nisn]);
                        @endphp
                        <a href="{{ $printRoute }}" class="btn-header" target="_blank">
                            <i class="fas fa-print"></i> Cetak
                        </a>
                        <a href="javascript:history.back()" class="btn-header">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- SUMMARY CARDS -->
            <div class="summary-grid">
                <div class="summary-card" style="border-left: 4px solid #667eea;">
                    <div class="summary-card-content">
                        <div class="summary-icon nilai">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <div class="summary-value">{{ number_format($rataRataKeseluruhan, 1) }}</div>
                            <div class="summary-label">Rata-rata Nilai</div>
                        </div>
                    </div>
                </div>
                
                <div class="summary-card" style="border-left: 4px solid #10b981;">
                    <div class="summary-card-content">
                        <div class="summary-icon kehadiran">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <div class="summary-value">{{ $persentaseKehadiran }}%</div>
                            <div class="summary-label">Kehadiran</div>
                        </div>
                    </div>
                </div>
                
                <div class="summary-card" style="border-left: 4px solid #f59e0b;">
                    <div class="summary-card-content">
                        <div class="summary-icon ekstra">
                            <i class="fas fa-futbol"></i>
                        </div>
                        <div>
                            <div class="summary-value">{{ count($ekstraList) }}</div>
                            <div class="summary-label">Ekstrakurikuler</div>
                        </div>
                    </div>
                </div>
                
                <div class="summary-card" style="border-left: 4px solid #ec4899;">
                    <div class="summary-card-content">
                        <div class="summary-icon prestasi">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div>
                            <div class="summary-value">{{ $totalPrestasi }}</div>
                            <div class="summary-label">Prestasi</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- REKAP NILAI PER MAPEL -->
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-book-open" style="color: #667eea;"></i>
                        Rekap Nilai Mata Pelajaran dan Keaktifan Proses Belajar
                    </h2>
                    <span class="section-badge">{{ count($rekapNilai) }} Mapel</span>
                </div>
                
                @if(count($rekapNilai) > 0)
                <div style="overflow-x: auto;">
                    <table class="rekap-table">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #667eea22 0%, #764ba222 100%);">
                                <th rowspan="2" style="text-align: left; vertical-align: middle;">Mata Pelajaran</th>
                                <th colspan="3" style="color: #667eea; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid #e5e7eb;">Nilai</th>
                                <th colspan="7" style="color: #10b981; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid #e5e7eb;">Kehadiran</th>
                            </tr>
                            <tr style="background: #f8fafc;">
                                <th style="font-size: 0.75rem;">Rata-rata</th>
                                <th style="font-size: 0.75rem;">Predikat</th>
                                <th style="font-size: 0.75rem;">Sesi</th>
                                <th style="font-size: 0.7rem; color: #15803d;" title="Hadir">H</th>
                                <th style="font-size: 0.7rem; color: #1d4ed8;" title="Dispen">D</th>
                                <th style="font-size: 0.7rem; color: #5b21b6;" title="Izin">I</th>
                                <th style="font-size: 0.7rem; color: #92400e;" title="Sakit">S</th>
                                <th style="font-size: 0.7rem; color: #dc2626;" title="Alfa">A</th>
                                <th style="font-size: 0.7rem; color: #4b5563;" title="Bolos">B</th>
                                <th style="font-size: 0.75rem;">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $grandTotalPresensi = 0;
                                $grandTotalHadir = 0;
                            @endphp
                            @foreach($rekapNilai as $nilai)
                                @php
                                    $predikat = App\Http\Controllers\Admin\RiwayatAkademikController::getPredikat($nilai['rata_rata']);
                                    $pres = $nilai['presensi'];
                                    $grandTotalPresensi += $pres['total'];
                                    $grandTotalHadir += $pres['hadir'];
                                    $persenColor = '#10b981';
                                    if ($pres['persentase'] < 75) $persenColor = '#ef4444';
                                    elseif ($pres['persentase'] < 90) $persenColor = '#f59e0b';
                                @endphp
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: #1f2937; font-size: 0.9rem;">{{ $nilai['nama_mapel'] }}</div>
                                        <div style="font-size: 0.75rem; color: #6b7280;">{{ $nilai['nama_guru'] }}</div>
                                    </td>
                                    <td style="text-align: center;">
                                        @if($nilai['total_nilai'] > 0)
                                        <span class="nilai-clickable" onclick="showDetailNilai('{{ $siswa->nisn }}', '{{ addslashes($nilai['nama_mapel']) }}')" title="Klik untuk lihat detail nilai">
                                            <span style="font-weight: 700; color: {{ $predikat['color'] }}; font-size: 1rem;">
                                                {{ number_format($nilai['rata_rata'], 1) }}
                                            </span>
                                            <i class="fas fa-search-plus" style="font-size: 0.6rem; color: #9ca3af; margin-left: 3px;"></i>
                                        </span>
                                        @else
                                        <span style="font-weight: 700; color: #9ca3af; font-size: 1rem;">-</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if($nilai['total_nilai'] > 0)
                                            <span class="predikat-badge" style="background: {{ $predikat['color'] }}22; color: {{ $predikat['color'] }};">
                                                {{ $predikat['predikat'] }}
                                            </span>
                                        @else
                                            <span style="color: #9ca3af;">-</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; color: #6b7280; font-size: 0.85rem;">{{ $nilai['total_nilai'] }}</td>
                                    <td style="text-align: center; font-weight: 600; color: #15803d; font-size: 0.85rem;">
                                        @if($pres['hadir'] > 0)
                                        <span class="presensi-clickable" style="background: #dcfce722;" onclick="showDetailPresensi('{{ $siswa->nisn }}', '{{ addslashes($nilai['nama_mapel']) }}', 'H', '{{ $periodeAktif->tahun_pelajaran }}', '{{ $periodeAktif->semester }}')">{{ $pres['hadir'] }}</span>
                                        @else 0 @endif
                                    </td>
                                    <td style="text-align: center; font-weight: 600; color: #1d4ed8; font-size: 0.85rem;">
                                        @if($pres['dispen'] > 0)
                                        <span class="presensi-clickable" style="background: #dbeafe22;" onclick="showDetailPresensi('{{ $siswa->nisn }}', '{{ addslashes($nilai['nama_mapel']) }}', 'D', '{{ $periodeAktif->tahun_pelajaran }}', '{{ $periodeAktif->semester }}')">{{ $pres['dispen'] }}</span>
                                        @else 0 @endif
                                    </td>
                                    <td style="text-align: center; font-weight: 600; color: #5b21b6; font-size: 0.85rem;">
                                        @if($pres['izin'] > 0)
                                        <span class="presensi-clickable" style="background: #ede9fe22;" onclick="showDetailPresensi('{{ $siswa->nisn }}', '{{ addslashes($nilai['nama_mapel']) }}', 'I', '{{ $periodeAktif->tahun_pelajaran }}', '{{ $periodeAktif->semester }}')">{{ $pres['izin'] }}</span>
                                        @else 0 @endif
                                    </td>
                                    <td style="text-align: center; font-weight: 600; color: #92400e; font-size: 0.85rem;">
                                        @if($pres['sakit'] > 0)
                                        <span class="presensi-clickable" style="background: #fef3c722;" onclick="showDetailPresensi('{{ $siswa->nisn }}', '{{ addslashes($nilai['nama_mapel']) }}', 'S', '{{ $periodeAktif->tahun_pelajaran }}', '{{ $periodeAktif->semester }}')">{{ $pres['sakit'] }}</span>
                                        @else 0 @endif
                                    </td>
                                    <td style="text-align: center; font-weight: 600; color: #dc2626; font-size: 0.85rem;">
                                        @if($pres['alfa'] > 0)
                                        <span class="presensi-clickable" style="background: #fee2e222;" onclick="showDetailPresensi('{{ $siswa->nisn }}', '{{ addslashes($nilai['nama_mapel']) }}', 'A', '{{ $periodeAktif->tahun_pelajaran }}', '{{ $periodeAktif->semester }}')">{{ $pres['alfa'] }}</span>
                                        @else 0 @endif
                                    </td>
                                    <td style="text-align: center; font-weight: 600; color: #4b5563; font-size: 0.85rem;">
                                        @if($pres['bolos'] > 0)
                                        <span class="presensi-clickable" style="background: #f3f4f622;" onclick="showDetailPresensi('{{ $siswa->nisn }}', '{{ addslashes($nilai['nama_mapel']) }}', 'B', '{{ $periodeAktif->tahun_pelajaran }}', '{{ $periodeAktif->semester }}')">{{ $pres['bolos'] }}</span>
                                        @else 0 @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <span style="font-weight: 700; color: {{ $persenColor }}; font-size: 0.9rem;">
                                            {{ $pres['total'] > 0 ? $pres['persentase'] . '%' : '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @php
                                $grandPersentase = $grandTotalPresensi > 0 ? round(($grandTotalHadir / $grandTotalPresensi) * 100, 1) : 0;
                                $footerPersenColor = '#10b981';
                                if ($grandPersentase < 75) $footerPersenColor = '#ef4444';
                                elseif ($grandPersentase < 90) $footerPersenColor = '#f59e0b';
                            @endphp
                            <tr style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-top: 2px solid #10b981;">
                                <td colspan="6" style="text-align: right; font-weight: 700; color: #15803d; font-size: 0.95rem; padding: 16px 15px;">
                                    <i class="fas fa-chart-pie" style="margin-right: 8px;"></i>
                                    Total Persentase Kehadiran Seluruh Mata Pelajaran:
                                </td>
                                <td colspan="5" style="text-align: center; padding: 16px 15px;">
                                    <span style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px; background: {{ $footerPersenColor }}; color: white; border-radius: 25px; font-weight: 700; font-size: 1.1rem; box-shadow: 0 4px 12px {{ $footerPersenColor }}44;">
                                        <i class="fas fa-percentage"></i>
                                        {{ $grandPersentase }}%
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Legend -->
                <div class="legend">
                    <span class="legend-item"><span class="legend-color" style="background: #dcfce7;"></span><strong style="color: #15803d;">H</strong> = Hadir</span>
                    <span class="legend-item"><span class="legend-color" style="background: #dbeafe;"></span><strong style="color: #1d4ed8;">D</strong> = Dispen</span>
                    <span class="legend-item"><span class="legend-color" style="background: #ede9fe;"></span><strong style="color: #5b21b6;">I</strong> = Izin</span>
                    <span class="legend-item"><span class="legend-color" style="background: #fef3c7;"></span><strong style="color: #92400e;">S</strong> = Sakit</span>
                    <span class="legend-item"><span class="legend-color" style="background: #fee2e2;"></span><strong style="color: #dc2626;">A</strong> = Alfa</span>
                    <span class="legend-item"><span class="legend-color" style="background: #f3f4f6;"></span><strong style="color: #4b5563;">B</strong> = Bolos</span>
                </div>
                @else
                <div style="padding: 40px; text-align: center; color: #6b7280;">
                    <i class="fas fa-book" style="font-size: 40px; margin-bottom: 15px; color: #d1d5db;"></i>
                    <p>Belum ada data nilai mata pelajaran.</p>
                </div>
                @endif
            </div>
            
            <!-- TWO COLUMN GRID: PRESENSI & EKSTRAKURIKULER -->
            <div class="two-column-grid">
                <!-- REKAP PRESENSI -->
                <div class="content-section" style="margin-bottom: 0;">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-calendar-check" style="color: #10b981;"></i>
                            Rekap Presensi
                        </h2>
                    </div>
                    <div style="padding: 20px;">
                        <div class="presensi-grid">
                            <div class="presensi-item" style="background: #dcfce7;">
                                <div class="presensi-value" style="color: #15803d;">{{ $presensiTotal->hadir ?? 0 }}</div>
                                <div class="presensi-label" style="color: #166534;">Hadir</div>
                            </div>
                            <div class="presensi-item" style="background: #dbeafe;">
                                <div class="presensi-value" style="color: #1d4ed8;">{{ $presensiTotal->dispen ?? 0 }}</div>
                                <div class="presensi-label" style="color: #1e40af;">Dispen</div>
                            </div>
                            <div class="presensi-item" style="background: #ede9fe;">
                                <div class="presensi-value" style="color: #5b21b6;">{{ $presensiTotal->izin ?? 0 }}</div>
                                <div class="presensi-label" style="color: #6d28d9;">Izin</div>
                            </div>
                            <div class="presensi-item" style="background: #fef3c7;">
                                <div class="presensi-value" style="color: #92400e;">{{ $presensiTotal->sakit ?? 0 }}</div>
                                <div class="presensi-label" style="color: #a16207;">Sakit</div>
                            </div>
                            <div class="presensi-item" style="background: #fee2e2;">
                                <div class="presensi-value" style="color: #dc2626;">{{ $presensiTotal->alfa ?? 0 }}</div>
                                <div class="presensi-label" style="color: #991b1b;">Alfa</div>
                            </div>
                            <div class="presensi-item" style="background: #f3f4f6;">
                                <div class="presensi-value" style="color: #4b5563;">{{ $presensiTotal->bolos ?? 0 }}</div>
                                <div class="presensi-label" style="color: #6b7280;">Bolos</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- EKSTRAKURIKULER -->
                <div class="content-section" style="margin-bottom: 0;">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-futbol" style="color: #f59e0b;"></i>
                            Keaktifan Ekstrakurikuler
                        </h2>
                    </div>
                    <div style="padding: 20px;">
                        @if(count($ekstraList) > 0)
                            @foreach($ekstraList as $ekstra)
                                @php
                                    $nilaiInfo = App\Http\Controllers\Admin\RiwayatAkademikController::getNilaiEkstraColor($ekstra['nilai']);
                                @endphp
                                <div class="ekstra-card">
                                    <div>
                                        <div style="font-weight: 600; color: #1f2937;">{{ $ekstra['nama_ekstrakurikuler'] }}</div>
                                        <div style="font-size: 0.8rem; color: #6b7280;">Pembina: {{ $ekstra['pembina'] }}</div>
                                    </div>
                                    <span style="padding: 4px 12px; background: {{ $nilaiInfo['bg'] }}; color: {{ $nilaiInfo['text'] }}; border-radius: 20px; font-weight: 700; font-size: 0.85rem;">
                                        {{ $ekstra['nilai'] ?: '-' }}
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <div style="text-align: center; color: #6b7280; padding: 30px;">
                                <i class="fas fa-futbol" style="font-size: 30px; margin-bottom: 10px; color: #d1d5db;"></i>
                                <p>Belum mengikuti kegiatan ekstrakurikuler.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- TWO COLUMN GRID: CATATAN BK & PRESTASI -->
            <div class="two-column-grid">
                <!-- CATATAN BK -->
                <div class="content-section" style="margin-bottom: 0;">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-clipboard-list" style="color: #3b82f6;"></i>
                            Catatan Bimbingan Konseling
                        </h2>
                        @if($totalCatatanBk > 5)
                            <span style="font-size: 0.8rem; color: #6b7280;">Menampilkan 5 dari {{ $totalCatatanBk }}</span>
                        @endif
                    </div>
                    <div style="padding: 20px;">
                        @if(count($catatanBkList) > 0)
                            @foreach($catatanBkList as $catatan)
                                <div class="bk-card">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #1f2937;">{{ $catatan->jenis_bimbingan }}</span>
                                        <span style="font-size: 0.75rem; color: #6b7280;">{{ \Carbon\Carbon::parse($catatan->tanggal)->format('d M Y') }}</span>
                                    </div>
                                    <p style="font-size: 0.85rem; color: #4b5563; margin: 0 0 8px 0;">{{ Str::limit($catatan->masalah, 100) }}</p>
                                    <span style="display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 600;
                                        {{ $catatan->status == 'Selesai' ? 'background: #dcfce7; color: #15803d;' : 'background: #fef3c7; color: #a16207;' }}">
                                        {{ $catatan->status }}
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <div style="text-align: center; color: #6b7280; padding: 30px;">
                                <i class="fas fa-clipboard-check" style="font-size: 30px; margin-bottom: 10px; color: #d1d5db;"></i>
                                <p>Tidak ada catatan bimbingan konseling.</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- PRESTASI -->
                <div class="content-section" style="margin-bottom: 0;">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-trophy" style="color: #ec4899;"></i>
                            Prestasi
                        </h2>
                        @if($totalPrestasi > 5)
                            <span style="font-size: 0.8rem; color: #6b7280;">Menampilkan 5 dari {{ $totalPrestasi }}</span>
                        @endif
                    </div>
                    <div style="padding: 20px;">
                        @if(count($prestasiList) > 0)
                            @foreach($prestasiList as $prestasi)
                                <div class="prestasi-card">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #1f2937;">{{ $prestasi->nama_kompetisi }}</span>
                                        <span style="padding: 2px 8px; background: #fef3c7; color: #92400e; border-radius: 10px; font-size: 0.75rem; font-weight: 600;">
                                            {{ $prestasi->juara }}
                                        </span>
                                    </div>
                                    <div style="font-size: 0.8rem; color: #6b7280;">
                                        <span><i class="fas fa-layer-group"></i> {{ $prestasi->jenjang }}</span>
                                        <span style="margin-left: 15px;"><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($prestasi->tanggal_pelaksanaan)->format('d M Y') }}</span>
                                    </div>
                                    <div style="font-size: 0.8rem; color: #6b7280; margin-top: 5px;">
                                        <i class="fas fa-building"></i> {{ $prestasi->penyelenggara }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div style="text-align: center; color: #6b7280; padding: 30px;">
                                <i class="fas fa-trophy" style="font-size: 30px; margin-bottom: 10px; color: #d1d5db;"></i>
                                <p>Belum ada data prestasi.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- TWO COLUMN GRID: PELANGGARAN & PANGGILAN ORTU -->
            <div class="two-column-grid">
                <!-- RIWAYAT PELANGGARAN -->
                <div class="content-section" style="margin-bottom: 0;">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                            Riwayat Pelanggaran
                        </h2>
                        @if($totalPelanggaran > 5)
                            <span style="font-size: 0.8rem; color: #6b7280;">Menampilkan 5 dari {{ $totalPelanggaran }}</span>
                        @elseif($totalPelanggaran > 0)
                            <span class="section-badge" style="background: linear-gradient(135deg, #ef4444, #dc2626);">{{ $totalPelanggaran }} Pelanggaran</span>
                        @endif
                    </div>
                    <div style="padding: 20px;">
                        @if(count($pelanggaranList) > 0)
                            @foreach($pelanggaranList as $pelanggaran)
                                <div class="pelanggaran-card">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #1f2937;">{{ $pelanggaran->jenis_pelanggaran == 'Lainnya' ? ($pelanggaran->jenis_lainnya ?: 'Lainnya') : $pelanggaran->jenis_pelanggaran }}</span>
                                        <span style="font-size: 0.75rem; color: #6b7280;">{{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d M Y') }}</span>
                                    </div>
                                    @if($pelanggaran->deskripsi)
                                        <p style="font-size: 0.85rem; color: #4b5563; margin: 0 0 8px 0;">{{ Str::limit($pelanggaran->deskripsi, 100) }}</p>
                                    @endif
                                    @if($pelanggaran->sanksi)
                                        <span style="display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 600; background: #fef3c7; color: #92400e;">
                                            <i class="fas fa-gavel"></i> {{ Str::limit($pelanggaran->sanksi, 50) }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div style="text-align: center; color: #6b7280; padding: 30px;">
                                <i class="fas fa-check-circle" style="font-size: 30px; margin-bottom: 10px; color: #10b981;"></i>
                                <p>Tidak ada riwayat pelanggaran. 👍</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- RIWAYAT PANGGILAN ORANG TUA -->
                <div class="content-section" style="margin-bottom: 0;">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-phone-alt" style="color: #6366f1;"></i>
                            Panggilan Orang Tua
                        </h2>
                        @if($totalPanggilanOrtu > 5)
                            <span style="font-size: 0.8rem; color: #6b7280;">Menampilkan 5 dari {{ $totalPanggilanOrtu }}</span>
                        @elseif($totalPanggilanOrtu > 0)
                            <span class="section-badge" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">{{ $totalPanggilanOrtu }} Panggilan</span>
                        @endif
                    </div>
                    <div style="padding: 20px;">
                        @if(count($panggilanOrtuList) > 0)
                            @foreach($panggilanOrtuList as $panggilan)
                                <div class="panggilan-card">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #1f2937;">{{ $panggilan->perihal }}</span>
                                        <span style="display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 600;
                                            {{ $panggilan->status == 'Hadir' ? 'background: #dcfce7; color: #15803d;' : ($panggilan->status == 'Tidak Hadir' ? 'background: #fee2e2; color: #dc2626;' : 'background: #fef3c7; color: #a16207;') }}">
                                            {{ $panggilan->status ?? 'Menunggu' }}
                                        </span>
                                    </div>
                                    <div style="font-size: 0.8rem; color: #6b7280; margin-bottom: 6px;">
                                        <span><i class="fas fa-calendar"></i> {{ $panggilan->tanggal_panggilan ? \Carbon\Carbon::parse($panggilan->tanggal_panggilan)->format('d M Y') : '-' }}</span>
                                        @if($panggilan->jam_panggilan)
                                            <span style="margin-left: 10px;"><i class="fas fa-clock"></i> {{ $panggilan->jam_panggilan }}</span>
                                        @endif
                                    </div>
                                    @if($panggilan->alasan)
                                        <p style="font-size: 0.8rem; color: #4b5563; margin: 0;">{{ Str::limit($panggilan->alasan, 100) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div style="text-align: center; color: #6b7280; padding: 30px;">
                                <i class="fas fa-phone-slash" style="font-size: 30px; margin-bottom: 10px; color: #d1d5db;"></i>
                                <p>Tidak ada riwayat panggilan orang tua.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Modal Detail Nilai -->
<div class="modal-nilai-overlay" id="modalDetailNilai">
    <div class="modal-nilai-box">
        <div class="modal-nilai-header">
            <h3><i class="fas fa-chart-bar"></i> Detail Nilai: <span id="modalMapelName">-</span></h3>
            <button class="modal-nilai-close" onclick="closeDetailNilai()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-nilai-stats" id="modalNilaiStats">
            <div class="stat-box">
                <div class="stat-value" id="statTotal">0</div>
                <div class="stat-label">Total Penilaian</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" id="statRata" style="color: #667eea;">0</div>
                <div class="stat-label">Rata-rata</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" id="statTertinggi" style="color: #10b981;">0</div>
                <div class="stat-label">Tertinggi</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" id="statTerendah" style="color: #ef4444;">0</div>
                <div class="stat-label">Terendah</div>
            </div>
        </div>
        <div class="modal-nilai-body">
            <table class="detail-nilai-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jam Ke</th>
                        <th>Materi</th>
                        <th style="text-align: center;">Nilai</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="modalNilaiBody">
                    <tr><td colspan="6" style="text-align: center; padding: 30px; color: #6b7280;">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Presensi -->
<div class="modal-presensi-overlay" id="modalDetailPresensi">
    <div class="modal-presensi-box">
        <div class="modal-presensi-header" id="modalPresensiHeader">
            <div>
                <h3><i class="fas fa-calendar-check"></i> <span id="modalPresensiStatus">-</span></h3>
                <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 4px;">
                    <span id="modalPresensiMapel">-</span> · <span id="modalPresensiCount">0 kali</span>
                </div>
            </div>
            <button class="modal-nilai-close" onclick="closeDetailPresensi()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-presensi-body" id="modalPresensiBody">
            <div style="text-align: center; padding: 30px; color: #6b7280;">Memuat data...</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const presensiColors = {
    'H': { bg: 'linear-gradient(135deg, #10b981, #059669)', border: '#10b981' },
    'D': { bg: 'linear-gradient(135deg, #3b82f6, #2563eb)', border: '#3b82f6' },
    'I': { bg: 'linear-gradient(135deg, #8b5cf6, #7c3aed)', border: '#8b5cf6' },
    'S': { bg: 'linear-gradient(135deg, #f59e0b, #d97706)', border: '#f59e0b' },
    'A': { bg: 'linear-gradient(135deg, #ef4444, #dc2626)', border: '#ef4444' },
    'B': { bg: 'linear-gradient(135deg, #6b7280, #4b5563)', border: '#6b7280' }
};

function showDetailNilai(nisn, mapel) {
    const modal = document.getElementById('modalDetailNilai');
    const tbody = document.getElementById('modalNilaiBody');
    document.getElementById('modalMapelName').textContent = mapel;
    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 30px; color: #6b7280;"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>';
    modal.classList.add('active');
    
    const prefix = window.location.pathname.includes('/guru/') ? '/guru' : '/admin';
    const url = prefix + '/riwayat-akademik/detail-nilai?nisn=' + encodeURIComponent(nisn) + '&mapel=' + encodeURIComponent(mapel);
    
    fetch(url)
        .then(r => r.json())
        .then(data => {
            document.getElementById('statTotal').textContent = data.stats.total;
            document.getElementById('statRata').textContent = data.stats.rata_rata;
            document.getElementById('statTertinggi').textContent = data.stats.tertinggi;
            document.getElementById('statTerendah').textContent = data.stats.terendah;
            
            if (data.nilai.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 30px; color: #6b7280;"><i class="fas fa-inbox"></i> Belum ada data nilai.</td></tr>';
                return;
            }
            
            let html = '';
            data.nilai.forEach((n, i) => {
                const nilaiColor = n.nilai >= 85 ? '#10b981' : (n.nilai >= 75 ? '#3b82f6' : (n.nilai >= 65 ? '#f59e0b' : '#ef4444'));
                html += `<tr>
                    <td>${i + 1}</td>
                    <td>${n.tanggal}</td>
                    <td>${n.jam_ke}</td>
                    <td>${n.materi}</td>
                    <td class="nilai-cell" style="color: ${nilaiColor};">${n.nilai}</td>
                    <td>${n.keterangan}</td>
                </tr>`;
            });
            tbody.innerHTML = html;
        })
        .catch(err => {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 30px; color: #ef4444;"><i class="fas fa-exclamation-circle"></i> Gagal memuat data.</td></tr>';
        });
}

function closeDetailNilai() {
    document.getElementById('modalDetailNilai').classList.remove('active');
}

document.getElementById('modalDetailNilai').addEventListener('click', function(e) {
    if (e.target === this) closeDetailNilai();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetailNilai();
        closeDetailPresensi();
    }
});

function showDetailPresensi(nisn, mapel, status, tahun, semester) {
    const modal = document.getElementById('modalDetailPresensi');
    const body = document.getElementById('modalPresensiBody');
    const header = document.getElementById('modalPresensiHeader');
    const colors = presensiColors[status] || presensiColors['H'];
    
    header.style.background = colors.bg;
    document.getElementById('modalPresensiMapel').textContent = mapel;
    document.getElementById('modalPresensiStatus').textContent = 
        {H:'Hadir',D:'Dispen',I:'Izin',S:'Sakit',A:'Alfa',B:'Bolos'}[status] || status;
    
    body.innerHTML = '<div style="text-align:center;padding:30px;color:#6b7280;"><i class="fas fa-spinner fa-spin"></i> Memuat data...</div>';
    modal.classList.add('active');
    
    const prefix = window.location.pathname.includes('/guru/') ? '/guru' : '/admin';
    const url = `${prefix}/riwayat-akademik/detail-presensi?nisn=${encodeURIComponent(nisn)}&mapel=${encodeURIComponent(mapel)}&status=${status}&tahun=${encodeURIComponent(tahun)}&semester=${encodeURIComponent(semester)}`;
    
    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.records.length === 0) {
                body.innerHTML = '<div style="text-align:center;padding:30px;color:#6b7280;"><i class="fas fa-inbox"></i> Tidak ada data.</div>';
                return;
            }
            
            document.getElementById('modalPresensiCount').textContent = data.total + ' kali';
            
            let html = '';
            data.records.forEach((r, i) => {
                html += `<div class="presensi-date-item" style="border-color: ${colors.border};">
                    <div>
                        <div class="date-info">${r.hari}, ${r.tanggal}</div>
                        <div class="guru-info"><i class="fas fa-user-tie"></i> ${r.guru}</div>
                    </div>
                    <span style="font-size:0.75rem;font-weight:600;padding:3px 10px;border-radius:15px;background:${colors.border}22;color:${colors.border};">#${i+1}</span>
                </div>`;
            });
            body.innerHTML = html;
        })
        .catch(err => {
            body.innerHTML = '<div style="text-align:center;padding:30px;color:#ef4444;"><i class="fas fa-exclamation-circle"></i> Gagal memuat data.</div>';
        });
}

function closeDetailPresensi() {
    document.getElementById('modalDetailPresensi').classList.remove('active');
}

document.getElementById('modalDetailPresensi').addEventListener('click', function(e) {
    if (e.target === this) closeDetailPresensi();
});
</script>
@endpush
@endsection
