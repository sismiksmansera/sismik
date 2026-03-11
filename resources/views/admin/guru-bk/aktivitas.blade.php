@extends('layouts.app')

@section('title', 'Aktivitas Guru BK - ' . $guruBK->nama . ' | SISMIK')

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
    .header-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
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
    .filter-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    .filter-form {
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
    .filter-control {
        padding: 10px 15px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
    }
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    .stat-card {
        background: white;
        padding: 18px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .stat-content {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: white;
    }
    .stat-info h3 { margin: 0; font-size: 20px; font-weight: 700; color: #1f2937; }
    .stat-info p { margin: 0; color: #6b7280; font-size: 11px; }
    
    /* Section Card */
    .section-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .section-header {
        padding: 15px 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-header h2 { margin: 0; font-size: 14px; font-weight: 600; color: #1f2937; }
    .section-badge {
        margin-left: auto;
        background: #ec4899;
        color: white;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 11px;
    }
    
    /* Progress Bar */
    .progress-item { margin-bottom: 12px; }
    .progress-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
    }
    .progress-label { font-size: 12px; color: #4b5563; }
    .progress-value { font-size: 12px; font-weight: 600; }
    .progress-bar {
        height: 6px;
        background: #f3f4f6;
        border-radius: 3px;
        overflow: hidden;
    }
    .progress-fill { height: 100%; border-radius: 3px; }
    
    /* Data Table */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .data-table th {
        padding: 10px 12px;
        text-align: left;
        font-weight: 600;
        color: #4b5563;
        background: #f9fafb;
    }
    .data-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #f3f4f6;
    }
    
    /* Badges */
    .badge-jenis {
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
    }
    .badge-status {
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
    }
    
    /* Grid Layout */
    .content-grid {
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .empty-state {
        padding: 40px;
        text-align: center;
        color: #9ca3af;
    }
    .empty-state i { font-size: 28px; margin-bottom: 10px; }
    
    @media (max-width: 1024px) {
        .filter-form { flex-direction: column; align-items: stretch; }
        .content-grid { grid-template-columns: 1fr; }
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
                <a href="{{ route('admin.guru-bk.index') }}" style="width: 40px; height: 40px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6b7280; text-decoration: none;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="header-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="header-text">
                    <h1>Aktivitas Guru BK</h1>
                    <p><strong>{{ $guruBK->nama }}</strong> • {{ $filterTahun }} ({{ $filterSemester }})</p>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="filter-card">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label>Tahun Pelajaran</label>
                    <select name="tahun" class="filter-control">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $y == $filterTahun ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>Semester</label>
                    <select name="semester" class="filter-control">
                        <option value="Ganjil" {{ $filterSemester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ $filterSemester == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" min="{{ $minDate }}" max="{{ $maxDate }}" class="filter-control">
                </div>
                <div class="filter-group">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" min="{{ $minDate }}" max="{{ $maxDate }}" class="filter-control">
                </div>
                <button type="submit" class="btn btn-primary" style="background: #ec4899;">
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
                <div class="stat-content">
                    <div class="stat-icon" style="background: {{ $warnaIndikator }}20;">
                        <i class="fas fa-chart-pie" style="color: {{ $warnaIndikator }};"></i>
                    </div>
                    <div class="stat-info">
                        <h3 style="color: {{ $warnaIndikator }};">{{ $persentaseSelesai }}%</h3>
                        <p>{{ $labelIndikator }}</p>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats->total_siswa ?? 0 }}</h3>
                        <p>Siswa Dibimbing</p>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats->total_catatan ?? 0 }}</h3>
                        <p>Total Catatan</p>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3 style="color: #ef4444;">{{ $stats->belum_ditangani ?? 0 }}</h3>
                        <p>Belum Ditangani</p>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-spinner"></i>
                    </div>
                    <div class="stat-info">
                        <h3 style="color: #f59e0b;">{{ $stats->dalam_proses ?? 0 }}</h3>
                        <p>Dalam Proses</p>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3 style="color: #10b981;">{{ $stats->selesai ?? 0 }}</h3>
                        <p>Selesai</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Per Jenis Bimbingan -->
            <div class="section-card">
                <div class="section-header">
                    <i class="fas fa-chart-bar" style="color: #8b5cf6;"></i>
                    <h2>Per Jenis Bimbingan</h2>
                </div>
                <div style="padding: 15px;">
                    @if(count($listJenis) > 0)
                        @php
                            $jenisColors = [
                                'Akademik' => '#3b82f6',
                                'Karir' => '#10b981',
                                'Pribadi' => '#8b5cf6',
                                'Sosial' => '#ec4899'
                            ];
                        @endphp
                        @foreach($listJenis as $j)
                            @php
                                $color = $jenisColors[$j->jenis_bimbingan] ?? '#6b7280';
                                $persen = $totalCatatan > 0 ? round(($j->total / $totalCatatan) * 100) : 0;
                            @endphp
                            <div class="progress-item">
                                <div class="progress-header">
                                    <span class="progress-label">{{ $j->jenis_bimbingan }}</span>
                                    <span class="progress-value" style="color: {{ $color }};">{{ $j->total }}</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $persen }}%; background: {{ $color }};"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p style="margin: 0; font-size: 12px;">Tidak ada data</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Riwayat Bimbingan -->
            <div class="section-card">
                <div class="section-header">
                    <i class="fas fa-history" style="color: #ec4899;"></i>
                    <h2>Riwayat Bimbingan</h2>
                    <span class="section-badge">{{ count($listRiwayat) }} record</span>
                </div>
                <div style="overflow-x: auto;">
                    @if(count($listRiwayat) > 0)
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Jenis</th>
                                    <th>Masalah</th>
                                    <th style="text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $statusColors = [
                                        'Selesai' => ['bg' => '#d1fae5', 'color' => '#10b981'],
                                        'Dalam Proses' => ['bg' => '#fef3c7', 'color' => '#f59e0b'],
                                        'Belum Ditangani' => ['bg' => '#fee2e2', 'color' => '#ef4444']
                                    ];
                                @endphp
                                @foreach($listRiwayat as $r)
                                    @php
                                        $sc = $statusColors[$r->status] ?? $statusColors['Belum Ditangani'];
                                        $jc = $jenisColors[$r->jenis_bimbingan] ?? '#6b7280';
                                    @endphp
                                    <tr>
                                        <td style="font-weight: 500; white-space: nowrap;">{{ date('d M Y', strtotime($r->tanggal)) }}</td>
                                        <td>{{ $r->nama_siswa ?? $r->nisn }}</td>
                                        <td>
                                            <span class="badge-jenis" style="background: {{ $jc }}15; color: {{ $jc }};">
                                                {{ $r->jenis_bimbingan }}
                                            </span>
                                        </td>
                                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #6b7280;">
                                            {{ Str::limit($r->masalah, 50) }}
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge-status" style="background: {{ $sc['bg'] }}; color: {{ $sc['color'] }};">
                                                {{ $r->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>Belum ada riwayat bimbingan untuk periode ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
