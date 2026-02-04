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

/* RESPONSIVE */
@media (max-width: 768px) {
    .content-header { padding: 20px 15px; }
    .header-content { flex-direction: column; align-items: flex-start; }
    .header-icon { width: 50px; height: 50px; font-size: 20px; }
    .header-info h1 { font-size: 1.2rem; }
    .header-actions { width: 100%; }
    .btn-header { flex: 1; justify-content: center; }
    .presensi-grid { grid-template-columns: repeat(3, 1fr); }
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
                                        <span style="font-weight: 700; color: {{ $predikat['color'] }}; font-size: 1rem;">
                                            {{ $nilai['total_nilai'] > 0 ? number_format($nilai['rata_rata'], 1) : '-' }}
                                        </span>
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
                                    <td style="text-align: center; font-weight: 600; color: #15803d; font-size: 0.85rem;">{{ $pres['hadir'] }}</td>
                                    <td style="text-align: center; font-weight: 600; color: #1d4ed8; font-size: 0.85rem;">{{ $pres['dispen'] }}</td>
                                    <td style="text-align: center; font-weight: 600; color: #5b21b6; font-size: 0.85rem;">{{ $pres['izin'] }}</td>
                                    <td style="text-align: center; font-weight: 600; color: #92400e; font-size: 0.85rem;">{{ $pres['sakit'] }}</td>
                                    <td style="text-align: center; font-weight: 600; color: #dc2626; font-size: 0.85rem;">{{ $pres['alfa'] }}</td>
                                    <td style="text-align: center; font-weight: 600; color: #4b5563; font-size: 0.85rem;">{{ $pres['bolos'] }}</td>
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
            
        </div>
    </div>
</div>
@endsection
