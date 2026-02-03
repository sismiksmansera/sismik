@extends('layouts.app')

@section('title', 'Aktivitas Guru - ' . $guru->nama . ' | SISMIK')

@push('styles')
<style>
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .back-btn {
        width: 40px;
        height: 40px;
        background: #f3f4f6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        text-decoration: none;
        transition: all 0.2s;
    }
    .back-btn:hover { background: #e5e7eb; color: #374151; }
    
    .header-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    .header-text h1 { margin: 0; color: #1e3a8a; font-size: 22px; font-weight: 700; }
    .header-text p { margin: 3px 0 0 0; color: #6b7280; font-size: 13px; }
    
    /* Filter Form */
    .filter-form {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    .filter-row {
        display: flex;
        gap: 15px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .filter-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 5px;
    }
    .filter-group select, .filter-group input {
        padding: 10px 15px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        min-width: 150px;
    }
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
    }
    .stat-info h3 { margin: 0; font-size: 22px; font-weight: 700; color: #1f2937; }
    .stat-info p { margin: 0; color: #6b7280; font-size: 12px; }
    
    /* Section Card */
    .section-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 20px;
    }
    .section-header {
        padding: 15px 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-header h2 { margin: 0; font-size: 16px; font-weight: 600; color: #1f2937; }
    .section-badge {
        margin-left: auto;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        color: white;
    }
    
    /* Table */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .data-table th {
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: #4b5563;
        background: #f9fafb;
    }
    .data-table th.text-center { text-align: center; }
    .data-table td {
        padding: 10px 15px;
        border-bottom: 1px solid #f3f4f6;
        color: #4b5563;
    }
    .data-table td.text-center { text-align: center; }
    .data-table tbody tr:hover { background: #fafafa; }
    
    /* Badges */
    .badge-kelas {
        background: #dbeafe;
        color: #1d4ed8;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
    }
    .badge-jam {
        background: #fef3c7;
        color: #92400e;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-success {
        background: #d1fae5;
        color: #059669;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
    }
    
    /* Empty State */
    .empty-state {
        padding: 40px;
        text-align: center;
        color: #9ca3af;
    }
    .empty-state i { font-size: 32px; margin-bottom: 10px; }
    
    @media print {
        .no-print { display: none !important; }
        .main-content { padding: 0 !important; }
    }
    @media (max-width: 768px) {
        .filter-row { flex-direction: column; }
        .filter-group { width: 100%; }
        .filter-group select, .filter-group input { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <div class="header-left">
                <a href="{{ route('admin.guru.index') }}" class="back-btn no-print">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="header-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="header-text">
                    <h1>Aktivitas Guru</h1>
                    <p><strong>{{ $guru->nama }}</strong> • {{ $filterTahun }} ({{ $filterSemester }})</p>
                </div>
            </div>
            <button onclick="window.print()" class="btn btn-primary no-print" style="background: #8b5cf6;">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>

        <!-- Filter Form -->
        <div class="filter-form no-print">
            <form method="GET" class="filter-row">
                <div class="filter-group">
                    <label>Tahun Pelajaran</label>
                    <select name="tahun">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $y == $filterTahun ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>Semester</label>
                    <select name="semester">
                        <option value="Ganjil" {{ $filterSemester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ $filterSemester == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" min="{{ $minDate }}" max="{{ $maxDate }}">
                </div>
                <div class="filter-group">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" min="{{ $minDate }}" max="{{ $maxDate }}">
                </div>
                <button type="submit" class="btn btn-primary" style="background: #8b5cf6; height: 42px;">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </form>
            <p style="margin: 10px 0 0 0; font-size: 12px; color: #9ca3af;">
                Rentang periode: <strong>{{ date('d M Y', strtotime($minDate)) }}</strong> s/d <strong>{{ date('d M Y', strtotime($maxDate)) }}</strong>
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card" style="border-left: 4px solid {{ $warnaIndikator }};">
                <div class="stat-icon" style="background: {{ $warnaIndikator }}20;">
                    <i class="fas fa-heartbeat" style="color: {{ $warnaIndikator }};"></i>
                </div>
                <div class="stat-info">
                    <h3 style="color: {{ $warnaIndikator }};">{{ $persentaseKeaktifan }}%</h3>
                    <p>{{ $labelIndikator }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $statPresensi->total_hari ?? 0 }}</h3>
                    <p>Hari Presensi</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $statPenilaian->total_record ?? 0 }}</h3>
                    <p>Penilaian Diinput</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalJam }}</h3>
                    <p>Jam/Minggu</p>
                </div>
            </div>
        </div>

        <!-- Riwayat Presensi -->
        <div class="section-card">
            <div class="section-header">
                <i class="fas fa-clipboard-list" style="color: #3b82f6;"></i>
                <h2>Riwayat Presensi</h2>
                <span class="section-badge" style="background: #3b82f6;">{{ count($listPresensi) }} record</span>
            </div>
            <div style="overflow-x: auto;">
                @if(count($listPresensi) > 0)
                    <table class="data-table" style="font-size: 11px;">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Hari</th>
                                <th>Mapel</th>
                                <th>Kelas</th>
                                <th class="text-center">Siswa</th>
                                <th class="text-center" style="color: #10b981;">H</th>
                                <th class="text-center" style="color: #f59e0b;">S</th>
                                <th class="text-center" style="color: #3b82f6;">I</th>
                                <th class="text-center" style="color: #ef4444;">A</th>
                                <th class="text-center" style="color: #f59e0b;">Jam Jadwal</th>
                                <th class="text-center" style="color: #f59e0b;">Waktu Jadwal</th>
                                <th class="text-center" style="color: #8b5cf6;">Jam Presensi</th>
                                <th class="text-center" style="color: #8b5cf6;">Waktu Presensi</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listPresensi as $p)
                                <tr>
                                    <td style="font-weight: 500; white-space: nowrap;">{{ date('d M Y', strtotime($p['tanggal_presensi'])) }}</td>
                                    <td style="font-size: 10px; color: #6b7280;">{{ $p['hari'] }}</td>
                                    <td style="font-size: 10px;">{{ $p['mata_pelajaran'] }}</td>
                                    <td><span class="badge-kelas" style="font-size: 9px;">{{ $p['nama_rombel'] ?? '-' }}</span></td>
                                    <td class="text-center" style="font-weight: 600;">{{ $p['jumlah_siswa'] }}</td>
                                    <td class="text-center" style="color: #10b981; font-weight: 600;">{{ $p['hadir'] }}</td>
                                    <td class="text-center" style="color: #f59e0b; font-weight: 600;">{{ $p['sakit'] }}</td>
                                    <td class="text-center" style="color: #3b82f6; font-weight: 600;">{{ $p['izin'] }}</td>
                                    <td class="text-center" style="color: #ef4444; font-weight: 600;">{{ $p['alfa'] }}</td>
                                    <td class="text-center">
                                        <span style="background: #fef3c7; color: #92400e; padding: 1px 4px; border-radius: 3px; font-size: 10px; font-weight: 600;">
                                            {{ $p['jam_jadwal_str'] }}
                                        </span>
                                    </td>
                                    <td class="text-center" style="font-size: 10px; color: #92400e;">{{ $p['waktu_jadwal'] }}</td>
                                    <td class="text-center">
                                        <span style="background: #ede9fe; color: #7c3aed; padding: 1px 4px; border-radius: 3px; font-size: 10px; font-weight: 600;">
                                            {{ $p['jam_presensi_str'] }}
                                        </span>
                                    </td>
                                    <td class="text-center" style="font-size: 10px; color: #7c3aed;">{{ $p['waktu_presensi'] }}</td>
                                    <td style="font-size: 9px; line-height: 1.4;">
                                        @if($p['jadwal_match']['status'] == 'match')
                                            <span style="color: #10b981;">✓ {{ $p['jadwal_match']['label'] }}</span>
                                        @elseif($p['jadwal_match']['status'] == 'partial')
                                            <span style="color: #f59e0b;">⚠ {{ $p['jadwal_match']['label'] }}</span>
                                        @elseif($p['jadwal_match']['status'] == 'mismatch')
                                            <span style="color: #ef4444;">✗ {{ $p['jadwal_match']['label'] }}</span>
                                        @else
                                            <span style="color: #9ca3af;">- {{ $p['jadwal_match']['label'] }}</span>
                                        @endif
                                        @if(!empty($p['time_match']['text']))
                                            <br>
                                            @if($p['time_match']['status'] == 'match')
                                                <span style="color: #10b981;">✓ {{ $p['time_match']['text'] }}</span>
                                            @elseif($p['time_match']['status'] == 'mismatch')
                                                <span style="color: #ef4444;">✗ {{ $p['time_match']['text'] }}</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada data presensi untuk periode ini</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Riwayat Penilaian -->
        <div class="section-card">
            <div class="section-header">
                <i class="fas fa-star" style="color: #10b981;"></i>
                <h2>Riwayat Penilaian</h2>
                <span class="section-badge" style="background: #10b981;">{{ count($listPenilaian) }} record</span>
            </div>
            <div style="overflow-x: auto;">
                @if(count($listPenilaian) > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Materi</th>
                                <th class="text-center">Siswa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listPenilaian as $n)
                                <tr>
                                    <td style="font-weight: 500;">{{ date('d M Y', strtotime($n->tanggal_penilaian)) }}</td>
                                    <td>{{ $n->mapel }}</td>
                                    <td><span class="badge-success">{{ $n->nama_rombel }}</span></td>
                                    <td style="color: #6b7280; font-size: 12px;">{{ $n->materi ?? '-' }}</td>
                                    <td class="text-center" style="font-weight: 600;">{{ $n->jumlah_siswa }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada data penilaian untuk periode ini</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Jadwal Mengajar -->
        <div class="section-card">
            <div class="section-header">
                <i class="fas fa-calendar-alt" style="color: #f59e0b;"></i>
                <h2>Jadwal Mengajar</h2>
                <span class="section-badge" style="background: #f59e0b;">{{ $totalJam }} jam/minggu</span>
            </div>
            <div style="overflow-x: auto;">
                @if(count($listJadwal) > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th class="text-center">Jam Ke</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listJadwal as $j)
                                <tr>
                                    <td style="font-weight: 500;">{{ $j->hari }}</td>
                                    <td class="text-center"><span class="badge-jam">{{ $j->jam_ke }}</span></td>
                                    <td>{{ $j->nama_mapel }}</td>
                                    <td><span class="badge-kelas">{{ $j->nama_rombel }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>Tidak ada jadwal mengajar untuk periode ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
