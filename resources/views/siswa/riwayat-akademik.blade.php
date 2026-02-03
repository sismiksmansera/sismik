@extends('layouts.app')

@section('title', 'Riwayat Akademik | SISMIK')

@push('styles')
<style>
    /* Header Card */
    .riwayat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }
    .header-info-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #667eea;
        flex: 1;
    }
    .header-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }
    .header-details h3 { margin: 0; color: #1f2937; font-size: 18px; font-weight: 600; }
    .header-details p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }

    /* Stats Row */
    .stats-row {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }
    .stat-card-v {
        background: white;
        padding: 18px 22px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
        min-width: 150px;
    }
    .stat-icon-v {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
    }
    .stat-value-v { font-size: 1.5rem; font-weight: 700; color: #1f2937; }
    .stat-label-v { font-size: 0.8rem; color: #6b7280; }

    /* Section Card */
    .section-card {
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
    .section-header h2 {
        margin: 0;
        font-size: 1.1rem;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Table */
    .table-wrapper { overflow-x: auto; }
    .rekap-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }
    .rekap-table thead tr { background: linear-gradient(135deg, #667eea22 0%, #764ba222 100%); }
    .rekap-table th {
        padding: 12px 10px;
        text-align: center;
        font-weight: 600;
        color: #374151;
        font-size: 0.8rem;
        border-bottom: 2px solid #e5e7eb;
    }
    .rekap-table th.left { text-align: left; padding-left: 15px; }
    .rekap-table th.nilai-group { color: #667eea; }
    .rekap-table th.presensi-group { color: #10b981; }
    .rekap-table tbody tr { border-bottom: 1px solid #e5e7eb; }
    .rekap-table tbody tr:nth-child(even) { background: #fafafa; }
    .rekap-table td { padding: 12px 8px; text-align: center; font-size: 0.85rem; }
    .rekap-table td.left { text-align: left; padding-left: 15px; }
    .mapel-name { font-weight: 600; color: #1f2937; }
    .guru-name { font-size: 0.75rem; color: #6b7280; }
    .predikat-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.8rem;
    }

    /* Presensi Colors */
    .pres-h { color: #15803d; font-weight: 600; }
    .pres-d { color: #1d4ed8; font-weight: 600; }
    .pres-i { color: #5b21b6; font-weight: 600; }
    .pres-s { color: #92400e; font-weight: 600; }
    .pres-a { color: #dc2626; font-weight: 600; }
    .pres-b { color: #4b5563; font-weight: 600; }

    /* Footer Row */
    .rekap-table tfoot tr {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-top: 2px solid #10b981;
    }
    .rekap-table tfoot td {
        padding: 16px 15px;
        font-weight: 700;
        color: #15803d;
    }
    .grand-total-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 20px;
        color: white;
        border-radius: 25px;
        font-weight: 700;
        font-size: 1.1rem;
    }

    /* Legend */
    .legend { padding: 15px 25px; background: #f8fafc; border-top: 1px solid #e5e7eb; }
    .legend-items { display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; font-size: 0.75rem; }
    .legend-item { display: flex; align-items: center; gap: 5px; }
    .legend-color { width: 12px; height: 12px; border-radius: 3px; }

    /* Bottom Grid */
    .bottom-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    /* Presensi Row */
    .presensi-row { display: flex; gap: 6px; flex-wrap: nowrap; overflow-x: auto; padding: 12px 20px; }
    .presensi-item {
        flex: 1;
        min-width: 50px;
        text-align: center;
        padding: 10px 5px;
        border-radius: 10px;
    }
    .presensi-item .value { font-size: 1.1rem; font-weight: 700; }
    .presensi-item .label { font-size: 0.6rem; font-weight: 600; }

    /* List Items */
    .list-container { padding: 20px; display: flex; flex-direction: column; gap: 12px; }
    .list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 16px;
        background: #f8fafc;
        border-radius: 12px;
        border-left: 4px solid;
    }
    .list-item-info { flex: 1; }
    .list-item-title { font-weight: 600; color: #1f2937; }
    .list-item-subtitle { font-size: 0.8rem; color: #6b7280; }

    /* Empty State */
    .empty-inline { text-align: center; padding: 30px; color: #6b7280; }
    .empty-inline i { font-size: 32px; margin-bottom: 10px; color: #d1d5db; }

    /* Link */
    .section-link { font-size: 0.8rem; color: #667eea; text-decoration: none; font-weight: 600; }

    @media (max-width: 768px) {
        /* Stats row mobile - 4 in a row */
        .stats-row { 
            flex-direction: row !important; 
            flex-wrap: nowrap !important;
            gap: 6px;
        }
        .stat-card-v {
            flex-direction: column !important;
            padding: 10px 4px !important;
            gap: 4px !important;
            text-align: center;
            min-width: unset !important;
        }
        .stat-icon-v {
            width: 32px !important;
            height: 32px !important;
            font-size: 12px !important;
            margin: 0 auto;
        }
        .stat-value-v {
            font-size: 14px !important;
        }
        .stat-label-v {
            font-size: 8px !important;
        }
        .bottom-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-siswa')

    <div class="main-content">
        <!-- Header -->
        <div class="riwayat-header">
            <div class="header-info-card">
                <div class="header-icon"><i class="fas fa-graduation-cap"></i></div>
                <div class="header-details">
                    <h3>{{ $siswa->nama }}</h3>
                    <p>Riwayat Akademik</p>
                    <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                        Periode: {{ $tahunAktif }} - {{ $semesterAktif }} | Rombel: {{ $namaRombel ?? '-' }}
                    </p>
                </div>
            </div>
            <a href="{{ route('siswa.riwayat-akademik.print') }}" target="_blank" class="btn-print" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;">
                <i class="fas fa-print"></i> Cetak Riwayat
            </a>
        </div>

        <!-- Summary Stats -->
        <div class="stats-row">
            <div class="stat-card-v">
                <div class="stat-icon-v" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <div class="stat-value-v">{{ number_format($rataRataKeseluruhan, 1) }}</div>
                    <div class="stat-label-v">Rata-rata</div>
                </div>
            </div>
            <div class="stat-card-v">
                <div class="stat-icon-v" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <div class="stat-value-v">{{ $persentaseKehadiran }}%</div>
                    <div class="stat-label-v">Kehadiran</div>
                </div>
            </div>
            <div class="stat-card-v">
                <div class="stat-icon-v" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="fas fa-futbol"></i>
                </div>
                <div>
                    <div class="stat-value-v">{{ count($ekskulList) }}</div>
                    <div class="stat-label-v">Ekskul</div>
                </div>
            </div>
            <div class="stat-card-v">
                <div class="stat-icon-v" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
                    <i class="fas fa-trophy"></i>
                </div>
                <div>
                    <div class="stat-value-v">{{ $totalPrestasi }}</div>
                    <div class="stat-label-v">Prestasi</div>
                </div>
            </div>
        </div>

        <!-- Rekap Nilai Table -->
        <div class="section-card">
            <div class="section-header">
                <h2><i class="fas fa-book-open" style="color: #667eea;"></i> Rekap Nilai dan Keaktifan Per Mapel</h2>
                <span class="badge">{{ count($rekapNilai) }} Mapel</span>
            </div>

            @if(count($rekapNilai) > 0)
            <div class="table-wrapper">
                <table class="rekap-table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="left" style="vertical-align: middle;">Mata Pelajaran</th>
                            <th colspan="3" class="nilai-group">Nilai</th>
                            <th colspan="7" class="presensi-group">Kehadiran</th>
                        </tr>
                        <tr style="background: #f8fafc;">
                            <th>Rata-rata</th>
                            <th>Predikat</th>
                            <th>Sesi</th>
                            <th style="color: #15803d;" title="Hadir">H</th>
                            <th style="color: #1d4ed8;" title="Dispen">D</th>
                            <th style="color: #5b21b6;" title="Izin">I</th>
                            <th style="color: #92400e;" title="Sakit">S</th>
                            <th style="color: #dc2626;" title="Alfa">A</th>
                            <th style="color: #4b5563;" title="Bolos">B</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapNilai as $nilai)
                        @php
                            $persen_color = '#10b981';
                            if ($nilai['presensi']['persentase'] < 75) $persen_color = '#ef4444';
                            elseif ($nilai['presensi']['persentase'] < 90) $persen_color = '#f59e0b';
                        @endphp
                        <tr>
                            <td class="left">
                                <div class="mapel-name">{{ $nilai['nama_mapel'] }}</div>
                                <div class="guru-name">{{ $nilai['nama_guru'] }}</div>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: {{ $nilai['predikat']['color'] }}; font-size: 1rem;">
                                    {{ $nilai['total_nilai'] > 0 ? number_format($nilai['rata_rata'], 1) : '-' }}
                                </span>
                            </td>
                            <td>
                                @if($nilai['total_nilai'] > 0)
                                <span class="predikat-badge" style="background: {{ $nilai['predikat']['color'] }}22; color: {{ $nilai['predikat']['color'] }};">
                                    {{ $nilai['predikat']['predikat'] }}
                                </span>
                                @else
                                <span style="color: #9ca3af;">-</span>
                                @endif
                            </td>
                            <td style="color: #6b7280;">{{ $nilai['total_nilai'] }}</td>
                            <td class="pres-h">{{ $nilai['presensi']['hadir'] }}</td>
                            <td class="pres-d">{{ $nilai['presensi']['dispen'] }}</td>
                            <td class="pres-i">{{ $nilai['presensi']['izin'] }}</td>
                            <td class="pres-s">{{ $nilai['presensi']['sakit'] }}</td>
                            <td class="pres-a">{{ $nilai['presensi']['alfa'] }}</td>
                            <td class="pres-b">{{ $nilai['presensi']['bolos'] }}</td>
                            <td>
                                <span style="font-weight: 700; color: {{ $persen_color }};">
                                    {{ $nilai['presensi']['total'] > 0 ? $nilai['presensi']['persentase'] . '%' : '-' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $footer_color = '#10b981';
                            if ($grandPersentase < 75) $footer_color = '#ef4444';
                            elseif ($grandPersentase < 90) $footer_color = '#f59e0b';
                        @endphp
                        <tr>
                            <td colspan="6" style="text-align: right;">
                                <i class="fas fa-chart-pie" style="margin-right: 8px;"></i>
                                Total Persentase Kehadiran Seluruh Mata Pelajaran:
                            </td>
                            <td colspan="5" style="text-align: center;">
                                <span class="grand-total-badge" style="background: {{ $footer_color }}; box-shadow: 0 4px 12px {{ $footer_color }}44;">
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
                <div class="legend-items">
                    <span class="legend-item"><span class="legend-color" style="background: #dcfce7;"></span><strong style="color: #15803d;">H</strong> = Hadir</span>
                    <span class="legend-item"><span class="legend-color" style="background: #dbeafe;"></span><strong style="color: #1d4ed8;">D</strong> = Dispen</span>
                    <span class="legend-item"><span class="legend-color" style="background: #ede9fe;"></span><strong style="color: #5b21b6;">I</strong> = Izin</span>
                    <span class="legend-item"><span class="legend-color" style="background: #fef3c7;"></span><strong style="color: #92400e;">S</strong> = Sakit</span>
                    <span class="legend-item"><span class="legend-color" style="background: #fee2e2;"></span><strong style="color: #dc2626;">A</strong> = Alfa</span>
                    <span class="legend-item"><span class="legend-color" style="background: #f3f4f6;"></span><strong style="color: #4b5563;">B</strong> = Bolos</span>
                </div>
            </div>
            @else
            <div class="empty-inline">
                <i class="fas fa-book"></i>
                <p>Belum ada data nilai mata pelajaran.</p>
            </div>
            @endif
        </div>

        <!-- Bottom Grid: Presensi & Ekskul -->
        <div class="bottom-grid">
            <!-- Rekap Presensi -->
            <div class="section-card">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-check" style="color: #10b981;"></i> Rekap Presensi</h2>
                    <a href="{{ route('siswa.presensi') }}" class="section-link">Lihat Detail →</a>
                </div>
                <div class="presensi-row">
                    <div class="presensi-item" style="background: #dcfce7;">
                        <div class="value" style="color: #15803d;">{{ $totalPresensi['hadir'] }}</div>
                        <div class="label" style="color: #166534;">Hadir</div>
                    </div>
                    <div class="presensi-item" style="background: #dbeafe;">
                        <div class="value" style="color: #1d4ed8;">{{ $totalPresensi['dispen'] }}</div>
                        <div class="label" style="color: #1e40af;">Dispen</div>
                    </div>
                    <div class="presensi-item" style="background: #ede9fe;">
                        <div class="value" style="color: #5b21b6;">{{ $totalPresensi['izin'] }}</div>
                        <div class="label" style="color: #6d28d9;">Izin</div>
                    </div>
                    <div class="presensi-item" style="background: #fef3c7;">
                        <div class="value" style="color: #92400e;">{{ $totalPresensi['sakit'] }}</div>
                        <div class="label" style="color: #a16207;">Sakit</div>
                    </div>
                    <div class="presensi-item" style="background: #fee2e2;">
                        <div class="value" style="color: #dc2626;">{{ $totalPresensi['alfa'] }}</div>
                        <div class="label" style="color: #991b1b;">Alfa</div>
                    </div>
                    <div class="presensi-item" style="background: #f3f4f6;">
                        <div class="value" style="color: #4b5563;">{{ $totalPresensi['bolos'] }}</div>
                        <div class="label" style="color: #6b7280;">Bolos</div>
                    </div>
                </div>
            </div>

            <!-- Ekstrakurikuler -->
            <div class="section-card">
                <div class="section-header">
                    <h2><i class="fas fa-futbol" style="color: #f59e0b;"></i> Keaktifan Ekstrakurikuler</h2>
                    <a href="{{ route('siswa.ekstrakurikuler') }}" class="section-link">Lihat Detail →</a>
                </div>
                @if(count($ekskulList) > 0)
                <div class="list-container">
                    @foreach($ekskulList as $ekskul)
                    <div class="list-item" style="border-left-color: #f59e0b;">
                        <div class="list-item-info">
                            <div class="list-item-title">{{ $ekskul->nama_ekstrakurikuler }}</div>
                            <div class="list-item-subtitle">Pembina: {{ $ekskul->pembina_1 }}</div>
                        </div>
                        <span style="padding: 6px 14px; background: {{ $ekskul->nilai_info['bg'] }}; color: {{ $ekskul->nilai_info['text'] }}; border-radius: 20px; font-weight: 700; font-size: 0.9rem;">
                            {{ $ekskul->nilai ?: '-' }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-inline">
                    <i class="fas fa-futbol"></i>
                    <p>Belum mengikuti ekstrakurikuler.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Bottom Grid: BK & Prestasi -->
        <div class="bottom-grid">
            <!-- Catatan BK -->
            <div class="section-card">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-comments" style="color: #8b5cf6;"></i> Catatan BK
                        <span style="background: #8b5cf622; color: #7c3aed; padding: 2px 10px; border-radius: 12px; font-size: 0.75rem;">{{ $totalCatatanBk }}</span>
                    </h2>
                    <a href="{{ route('siswa.catatan-bk') }}" class="section-link">Lihat Semua →</a>
                </div>
                @if(count($catatanBkList) > 0)
                <div class="list-container">
                    @foreach($catatanBkList as $bk)
                    @php
                        $statusColor = $bk->status === 'Selesai' ? '#10b981' : ($bk->status === 'Dalam Proses' ? '#f59e0b' : '#ef4444');
                    @endphp
                    <div class="list-item" style="border-left-color: #8b5cf6;">
                        <div class="list-item-info">
                            <div class="list-item-title">{{ $bk->jenis_bimbingan }}</div>
                            <div class="list-item-subtitle">{{ \Carbon\Carbon::parse($bk->tanggal)->format('d M Y') }}</div>
                        </div>
                        <span style="padding: 4px 10px; background: {{ $statusColor }}22; color: {{ $statusColor }}; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                            {{ $bk->status }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-inline">
                    <i class="fas fa-comments"></i>
                    <p>Tidak ada catatan bimbingan.</p>
                </div>
                @endif
            </div>

            <!-- Prestasi -->
            <div class="section-card">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-trophy" style="color: #f59e0b;"></i> Prestasi
                        <span style="background: #f59e0b22; color: #d97706; padding: 2px 10px; border-radius: 12px; font-size: 0.75rem;">{{ $totalPrestasi }}</span>
                    </h2>
                    <a href="{{ route('siswa.prestasi') }}" class="section-link">Lihat Semua →</a>
                </div>
                @if(count($prestasiList) > 0)
                <div class="list-container">
                    @foreach($prestasiList as $prestasi)
                    <div class="list-item" style="border-left-color: {{ $prestasi->jenjang_color }};">
                        <div class="list-item-info">
                            <div class="list-item-title">{{ $prestasi->nama_kompetisi }}</div>
                            <div class="list-item-subtitle">
                                <span style="background: {{ $prestasi->jenjang_color }}22; color: {{ $prestasi->jenjang_color }}; padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 600;">{{ $prestasi->jenjang }}</span>
                                &nbsp;{{ \Carbon\Carbon::parse($prestasi->tanggal_pelaksanaan)->format('d M Y') }}
                            </div>
                        </div>
                        <span style="padding: 4px 10px; background: #fef3c7; color: #92400e; border-radius: 12px; font-size: 0.8rem; font-weight: 700;">
                            Juara {{ $prestasi->juara }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-inline">
                    <i class="fas fa-trophy"></i>
                    <p>Belum ada prestasi yang tercatat.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
