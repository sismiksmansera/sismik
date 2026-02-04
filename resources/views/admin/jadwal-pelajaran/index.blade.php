@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<style>
    /* FILTER BAR STYLES */
    .filter-bar {
        background: white;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 25px;
    }
    
    .filter-form-horizontal {
        width: 100%;
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
        color: #374151;
        margin-bottom: 0;
    }
    
    .filter-actions-horizontal {
        display: flex;
        gap: 10px;
        align-items: flex-end;
        margin-bottom: 8px;
    }
    
    /* MODERN SELECT STYLES */
    .modern-select {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        padding: 10px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
        width: 100%;
    }
    
    .modern-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }
    
    .modern-select:hover {
        border-color: #9ca3af;
    }
    
    /* CONTENT HEADER */
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
        flex: 1;
    }
    
    .header-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
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
        font-size: 28px;
        font-weight: 700;
    }
    
    .filter-info {
        margin-top: 5px;
    }
    
    .filter-info small {
        font-size: 12px;
        color: #6b7280;
    }
    
    /* CONTENT SECTION */
    .content-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
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
    
    .section-body {
        padding: 25px;
    }
    
    .section-body.p-0 {
        padding: 0;
    }
    
    /* ROMBEL SECTION */
    .rombel-section {
        margin-bottom: 0;
        border: 1px solid #e5e7eb;
        border-top: none;
    }
    
    .rombel-section:first-child {
        border-top: 1px solid #e5e7eb;
    }
    
    .rombel-header {
        background: #f8fafc;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .rombel-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .rombel-title h4 {
        margin: 0;
        color: #1f2937;
        font-size: 16px;
        font-weight: 600;
    }
    
    .rombel-title i {
        color: #6b7280;
        font-size: 16px;
    }
    
    .rombel-badge {
        background: #dbeafe;
        color: #1d4ed8;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    /* MODERN TABLE */
    .modern-table-container {
        overflow-x: auto;
    }
    
    .modern-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    
    .modern-table th {
        background: linear-gradient(0deg, #059669 0%, #047857 100%);
        color: white;
        padding: 15px 12px;
        font-weight: 600;
        text-align: left;
    }
    
    .modern-table td {
        padding: 12px;
        border-bottom: 1px solid #f3f4f6;
        color: #4b5563;
    }
    
    .modern-table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .modern-table tbody tr:hover {
        background: #f9fafb;
    }
    
    /* BADGES */
    .jam-badge {
        background: #fef3c7;
        color: #d97706;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        min-width: 40px;
        text-align: center;
    }
    
    .guru-badge {
        background: #dbeafe;
        color: #1d4ed8;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
    }
    
    /* MAPEL INFO */
    .mapel-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .mapel-info i {
        color: #6b7280;
        width: 16px;
    }
    
    .periode-info-small {
        margin-top: 2px;
    }
    
    .periode-info-small small {
        font-size: 10px;
        color: #6b7280;
    }
    
    /* EMPTY STATE */
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
    
    .empty-state p {
        margin: 0;
        font-size: 14px;
    }
    
    .text-center {
        text-align: center;
    }
    
    /* BUTTON STYLES */
    .btn-sm {
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
    }
    
    .btn-secondary {
        background: #6b7280;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #4b5563;
        transform: translateY(-1px);
        color: white;
        text-decoration: none;
    }
    
    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .filter-row {
            gap: 15px;
        }
        
        .filter-item {
            min-width: 160px;
            flex: 1;
        }
    }
    
    @media (max-width: 768px) {
        .filter-bar {
            padding: 15px 20px;
        }
        
        .filter-row {
            flex-direction: column;
            gap: 15px;
        }
        
        .filter-item {
            min-width: 100%;
            width: 100%;
        }
        
        .filter-actions-horizontal {
            width: 100%;
            justify-content: stretch;
        }
        
        .filter-actions-horizontal .btn-sm {
            flex: 1;
            justify-content: center;
        }
        
        .content-header {
            flex-direction: column;
            gap: 15px;
        }
        
        .rombel-header {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }
        
        .modern-table th,
        .modern-table td {
            padding: 10px 8px;
            font-size: 13px;
        }
    }
</style>

<!-- HEADER SECTION -->
<div class="content-header">
    <div class="header-content">
        <div class="header-icon">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="header-text">
            <h1>Jadwal Pelajaran</h1>
            @if(!empty($tahunFilter) || !empty($semesterFilter))
                <div class="filter-info">
                    <small class="text-muted">
                        @if(!empty($tahunFilter)) Tahun: <strong>{{ $tahunFilter }}</strong> @endif
                        @if(!empty($semesterFilter)) | Semester: <strong>{{ ucfirst($semesterFilter) }}</strong> @endif
                    </small>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- FILTER BAR -->
<div class="filter-bar">
    <form method="GET" class="filter-form-horizontal" id="autoFilterForm">
        <div class="filter-row">
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
            <div class="filter-actions-horizontal">
                <a href="{{ route('admin.jadwal-pelajaran.index') }}" class="btn-sm btn-secondary">
                    <i class="fas fa-refresh"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- JADWAL PER HARI -->
@foreach($loopHari as $hari)
    <div class="content-section">
        <div class="section-header">
            <h2><i class="fas fa-calendar-day"></i> {{ $hari }}</h2>
            <span class="badge">{{ $jadwalData[$hari]['count'] }} Jadwal</span>
        </div>
        
        <div class="section-body p-0">
            @if(count($jadwalData[$hari]['rombels']) > 0)
                @foreach($jadwalData[$hari]['rombels'] as $rombel)
                    <div class="rombel-section">
                        <div class="rombel-header">
                            <div class="rombel-title">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <h4>{{ $rombel['nama_rombel'] }}</h4>
                            </div>
                            <span class="rombel-badge">{{ $rombel['mapel_count'] }} Mapel</span>
                        </div>
                        
                        <div class="modern-table-container">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="100">Jam ke-</th>
                                        <th width="400">Mata Pelajaran</th>
                                        <th width="300">Guru Pengampu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rombel['jadwal'] as $row)
                                        <tr>
                                            <td class="text-center">
                                                <span class="jam-badge">{{ $row->jam_ke }}</span>
                                            </td>
                                            <td>
                                                <div class="mapel-info">
                                                    <i class="fas fa-book"></i>
                                                    <div>
                                                        {{ $row->nama_mapel }}
                                                        @if(!empty($tahunFilter) && !empty($semesterFilter))
                                                            <div class="periode-info-small">
                                                                <small class="text-muted">
                                                                    {{ $row->tahun_pelajaran }} - {{ ucfirst($row->semester) }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="guru-badge">{{ $row->nama_guru }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3>Tidak Ada Jadwal</h3>
                    <p>Belum ada jadwal pelajaran untuk hari {{ $hari }}</p>
                </div>
            @endif
        </div>
    </div>
@endforeach
@endsection
