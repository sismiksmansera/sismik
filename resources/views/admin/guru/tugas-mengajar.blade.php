@extends('layouts.app')

@section('title', 'Penugasan Mengajar - ' . $guru->nama . ' | SISMIK')

@push('styles')
<style>
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }
    .header-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .header-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    .header-text h1 {
        margin: 0;
        color: #1e3a8a;
        font-size: 24px;
        font-weight: 700;
    }
    .header-text p {
        margin: 5px 0 0 0;
        color: #6b7280;
        font-size: 14px;
    }
    .teacher-info-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border-left: 4px solid #3b82f6;
        min-width: 250px;
    }
    .teacher-avatar {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }
    .teacher-details h3 {
        margin: 0;
        color: #1f2937;
        font-size: 18px;
        font-weight: 600;
    }
    .teacher-details p {
        margin: 5px 0 0 0;
        color: #6b7280;
        font-size: 14px;
    }
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: transform 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
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
    }
    .stat-icon.primary { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
    .stat-icon.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-icon.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stat-info h3 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
    }
    .stat-info p {
        margin: 5px 0 0 0;
        color: #6b7280;
        font-size: 14px;
    }
    
    /* Content Section */
    .content-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 25px;
    }
    .section-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .section-header h2 {
        margin: 0;
        color: #1f2937;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .badge {
        background: #3b82f6;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    /* Table Controls */
    .table-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        flex-wrap: wrap;
        gap: 15px;
    }
    .filter-group {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }
    .filter-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-item label {
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        white-space: nowrap;
    }
    .filter-item select {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: white;
        min-width: 140px;
    }
    .filter-item select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Modern Table */
    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }
    .modern-table th {
        background: linear-gradient(0deg, #3b82f6, #1d4ed8);
        color: white;
        padding: 15px 20px;
        font-weight: 600;
        text-align: left;
        font-size: 14px;
    }
    .modern-table th.text-center { text-align: center; }
    .modern-table td {
        padding: 15px 20px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: top;
    }
    .modern-table tbody tr:hover {
        background: #f9fafb;
    }
    
    /* Rombel & Mapel Info */
    .rombel-name {
        font-weight: 700;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-block;
        font-size: 14px;
    }
    .periode-info {
        margin-top: 6px;
        font-size: 12px;
        color: #6b7280;
    }
    .mapel-info {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
        border-left: 3px solid #1f2937;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 8px;
    }
    
    /* Jadwal Styling */
    .jadwal-list {
        margin-left: 8px;
    }
    .jadwal-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
        font-size: 13px;
    }
    .jadwal-hari {
        color: #2563eb;
        font-weight: 500;
        min-width: 60px;
    }
    .jadwal-jam {
        color: #4b5563;
    }
    .jadwal-kosong {
        font-size: 13px;
        color: #9ca3af;
        font-style: italic;
    }
    
    /* Badges */
    .jam-badge {
        background: #dbeafe;
        color: #1d4ed8;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-badge.active {
        background: #dcfce7;
        color: #166534;
    }
    
    /* Table Footer */
    .table-total {
        background: #f0f9ff;
        font-weight: 600;
    }
    .table-total td {
        padding: 15px 20px;
        color: #1e40af;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }
    .empty-icon {
        font-size: 48px;
        color: #d1d5db;
        margin-bottom: 15px;
    }
    .empty-state h3 {
        margin: 0 0 10px 0;
        color: #374151;
        font-size: 18px;
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        padding: 0 25px 25px;
    }
    
    @media (max-width: 768px) {
        .content-header { flex-direction: column; }
        .teacher-info-card { min-width: auto; width: 100%; }
        .stats-grid { grid-template-columns: 1fr; }
        .filter-group { flex-direction: column; align-items: stretch; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header Section -->
        <div class="content-header">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="header-text">
                    <h1>Penugasan Mengajar</h1>
                    <p>Detail penugasan mengajar untuk guru</p>
                </div>
            </div>
            <div class="teacher-info-card">
                <div class="teacher-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="teacher-details">
                    <h3>{{ $guru->nama }}</h3>
                    <p>{{ $guru->nip ?? 'NIP tidak tersedia' }}</p>
                    @if(!empty($tahunFilter) || !empty($semesterFilter))
                        <p style="font-size: 12px; margin-top: 4px;">
                            @if(!empty($tahunFilter))Tahun: <strong>{{ $tahunFilter }}</strong>@endif
                            @if(!empty($semesterFilter)) | Semester: <strong>{{ ucfirst($semesterFilter) }}</strong>@endif
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
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

        <!-- Table Section -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-list-alt"></i> Daftar Penugasan</h2>
                <span class="badge">{{ $totalMapel }} Mapel</span>
            </div>

            <!-- Table Controls -->
            <div class="table-controls">
                <div class="filter-group">
                    <div class="filter-item">
                        <label>Tahun Pelajaran</label>
                        <select id="filterTahun">
                            <option value="">Semua Tahun</option>
                            @foreach($tahunList as $tahun)
                                <option value="{{ $tahun }}" {{ $tahunFilter == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>Semester</label>
                        <select id="filterSemester">
                            <option value="">Semua</option>
                            <option value="ganjil" {{ $semesterFilter == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="genap" {{ $semesterFilter == 'genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                </div>
                <button id="btnResetFilter" class="btn btn-secondary" style="padding: 8px 16px;">
                    <i class="fas fa-undo"></i> Reset Filter
                </button>
            </div>

            @if(count($penugasanWithJadwal) > 0)
                <div style="overflow-x: auto;">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th class="text-center" width="60">No</th>
                                <th width="200">Rombongan Belajar</th>
                                <th>Mata Pelajaran & Jadwal</th>
                                <th class="text-center" width="120">Jam/Minggu</th>
                                <th class="text-center" width="100">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalJamTable = 0; @endphp
                            @foreach($penugasanWithJadwal as $index => $p)
                                @php $totalJamTable += $p['jam_count']; @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="rombel-name">{{ $p['nama_rombel'] }}</span>
                                        <div class="periode-info">
                                            {{ $p['tahun_pelajaran'] }} - {{ ucfirst($p['semester']) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="mapel-info">
                                            <i class="fas fa-book-open"></i>
                                            {{ $p['nama_mapel'] }}
                                        </div>
                                        @if(!empty($p['jadwal']))
                                            <div class="jadwal-list">
                                                @foreach($p['jadwal'] as $hari => $jamRange)
                                                    <div class="jadwal-item">
                                                        <span class="jadwal-hari">{{ $hari }}</span>
                                                        <span class="jadwal-jam">Jam ke {{ $jamRange }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="jadwal-kosong">Jadwal belum diatur</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="jam-badge">{{ $p['jam_count'] }} jam</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge active">Aktif</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-total">
                                <td colspan="3" style="text-align: right;"><strong>Total Jam Mengajar per Minggu</strong></td>
                                <td class="text-center"><strong>{{ $totalJamTable }} jam</strong></td>
                                <td class="text-center">-</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>Belum Ada Penugasan</h3>
                    <p>Guru ini belum memiliki jadwal mengajar untuk periode ini.</p>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Guru
            </a>
            <button onclick="window.print()" class="btn btn-secondary" style="background: white; color: #374151; border: 1px solid #d1d5db;">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Filter functionality
    document.getElementById('filterTahun').addEventListener('change', applyFilters);
    document.getElementById('filterSemester').addEventListener('change', applyFilters);
    
    function applyFilters() {
        const tahun = document.getElementById('filterTahun').value;
        const semester = document.getElementById('filterSemester').value;
        const url = new URL(window.location.href);
        
        if (tahun) url.searchParams.set('tahun', tahun);
        else url.searchParams.delete('tahun');
        
        if (semester) url.searchParams.set('semester', semester);
        else url.searchParams.delete('semester');
        
        window.location.href = url.toString();
    }
    
    document.getElementById('btnResetFilter').addEventListener('click', function() {
        const url = new URL(window.location.href);
        url.searchParams.delete('tahun');
        url.searchParams.delete('semester');
        window.location.href = url.toString();
    });
</script>
@endpush
