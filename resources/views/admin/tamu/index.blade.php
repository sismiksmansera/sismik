@extends('layouts.app')

@section('title', 'Daftar Tamu')

@push('styles')
<style>
/* PAGE STYLES */
.tamu-page {
    padding: 30px;
    background: #f8f9fa;
    min-height: calc(100vh - 60px);
}

/* HEADER */
.page-header {
    background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
}

.header-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}

.header-text h1 {
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 5px 0;
}

.header-text p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
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
    border: 1px solid #e5e7eb;
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: white;
}

.stat-icon.green { background: linear-gradient(135deg, #16a34a, #22c55e); }
.stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

.stat-info h3 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
}

.stat-info p {
    margin: 4px 0 0 0;
    font-size: 13px;
    color: #6b7280;
}

/* FILTER SECTION */
.filter-section {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
    align-items: end;
}

.filter-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

.filter-group input,
.filter-group select {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.3s;
}

.filter-group input:focus,
.filter-group select:focus {
    outline: none;
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
}

.btn-filter {
    padding: 10px 20px;
    background: linear-gradient(135deg, #16a34a, #22c55e);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-filter:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
}

.btn-reset {
    padding: 10px 16px;
    background: #f3f4f6;
    color: #374151;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-reset:hover {
    background: #e5e7eb;
}

.btn-print {
    padding: 10px 16px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-print:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}

/* TABLE */
.table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 14px 16px;
    text-align: left;
    font-size: 14px;
}

.data-table th {
    background: #f8fafc;
    color: #374151;
    font-weight: 600;
    border-bottom: 2px solid #e5e7eb;
}

.data-table td {
    border-bottom: 1px solid #f3f4f6;
    color: #1f2937;
}

.data-table tr:hover {
    background: #f9fafb;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-wali { background: #dbeafe; color: #1d4ed8; }
.badge-jurnalis { background: #fef3c7; color: #b45309; }
.badge-pt { background: #dcfce7; color: #16a34a; }
.badge-khusus { background: #f3e8ff; color: #7c3aed; }
.badge-umum { background: #f3f4f6; color: #374151; }

.badge-doc {
    background: #ecfdf5;
    color: #059669;
    font-size: 10px;
    margin-left: 4px;
}

.btn-action {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.btn-view {
    background: #dbeafe;
    color: #1d4ed8;
}

.btn-view:hover {
    background: #bfdbfe;
}

.btn-delete {
    background: #fee2e2;
    color: #dc2626;
    border: none;
    cursor: pointer;
}

.btn-delete:hover {
    background: #fecaca;
}

/* EMPTY STATE */
.empty-state {
    padding: 60px 30px;
    text-align: center;
    color: #6b7280;
}

.empty-state i {
    font-size: 50px;
    margin-bottom: 15px;
    color: #d1d5db;
}

.empty-state h3 {
    color: #1f2937;
    margin-bottom: 8px;
}

/* PAGINATION */
.pagination-wrapper {
    padding: 15px 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: center;
}

/* SUCCESS MESSAGE */
.alert-success {
    background: #dcfce7;
    color: #166534;
    padding: 14px 18px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    border-left: 4px solid #22c55e;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .tamu-page { padding: 20px 15px; }
    .page-header { flex-direction: column; text-align: center; }
    .header-info { flex-direction: column; }
    .stats-grid { grid-template-columns: 1fr; }
    .filter-grid { grid-template-columns: 1fr; }
    
    .table-container { overflow-x: auto; }
    .data-table { min-width: 800px; }
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')
    
    <div class="main-content tamu-page">
        <!-- Header -->
        <div class="page-header">
            <div class="header-info">
                <div class="header-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="header-text">
                    <h1>Daftar Tamu</h1>
                    <p>Rekap kunjungan tamu sekolah</p>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalHariIni }}</h3>
                    <p>Tamu Hari Ini</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalBulanIni }}</h3>
                    <p>Tamu Bulan Ini</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalSemua }}</h3>
                    <p>Total Semua Tamu</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter -->
        <div class="filter-section">
            <form method="GET" action="{{ route('admin.tamu.index') }}">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label>Tanggal Dari</label>
                        <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
                    </div>
                    <div class="filter-group">
                        <label>Tanggal Sampai</label>
                        <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
                    </div>
                    <div class="filter-group">
                        <label>Kategori</label>
                        <select name="kategori">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriOptions as $option)
                                <option value="{{ $option }}" {{ request('kategori') == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, bertemu, keperluan...">
                    </div>
                    <div class="filter-group" style="display: flex; gap: 8px;">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('admin.tamu.index') }}" class="btn-reset">
                            <i class="fas fa-times"></i> Reset
                        </a>
                        <a href="{{ route('admin.tamu.print', request()->query()) }}" class="btn-print" target="_blank">
                            <i class="fas fa-print"></i> Cetak
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-container">
            @if($tamuList->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Bertemu</th>
                            <th>Keperluan</th>
                            <th>Dokumen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tamuList as $index => $tamu)
                            <tr>
                                <td>{{ $tamuList->firstItem() + $index }}</td>
                                <td>
                                    <div style="font-weight: 600;">{{ $tamu->created_at->format('d/m/Y') }}</div>
                                    <div style="font-size: 12px; color: #6b7280;">{{ $tamu->created_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">{{ $tamu->nama }}</div>
                                    <div style="font-size: 12px; color: #6b7280;">{{ $tamu->no_hp }}</div>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($tamu->datang_sebagai) {
                                            'Wali Murid' => 'badge-wali',
                                            'Jurnalis' => 'badge-jurnalis',
                                            'Perguruan Tinggi' => 'badge-pt',
                                            'Tamu Khusus' => 'badge-khusus',
                                            default => 'badge-umum'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $tamu->datang_sebagai }}</span>
                                </td>
                                <td>{{ Str::limit($tamu->bertemu_dengan, 20) }}</td>
                                <td>{{ Str::limit($tamu->keperluan, 30) }}</td>
                                <td>
                                    @if($tamu->memberikan_dokumen)
                                        <span class="badge badge-doc"><i class="fas fa-arrow-up"></i> Beri</span>
                                    @endif
                                    @if($tamu->meminta_dokumen)
                                        <span class="badge badge-doc"><i class="fas fa-arrow-down"></i> Minta</span>
                                    @endif
                                    @if(!$tamu->memberikan_dokumen && !$tamu->meminta_dokumen)
                                        <span style="color: #9ca3af;">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('tamu.print', $tamu->id) }}" class="btn-action btn-view" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.tamu.destroy', $tamu->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus data tamu ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($tamuList->hasPages())
                    <div class="pagination-wrapper">
                        {{ $tamuList->withQueryString()->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>Belum Ada Data Tamu</h3>
                    <p>Data kunjungan tamu akan muncul di sini</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
