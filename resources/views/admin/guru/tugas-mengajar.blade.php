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
    
    /* Table Action Buttons */
    .btn-action {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        transition: all 0.2s ease;
    }
    .btn-action.edit {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }
    .btn-action.edit:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(59,130,246,0.4);
    }
    .btn-action.delete {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    .btn-action.delete:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(239,68,68,0.4);
    }
    
    /* Delete Confirm Modal */
    .confirm-modal-content {
        text-align: center;
        padding: 20px 0;
    }
    .confirm-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 28px;
        color: #d97706;
    }
    .confirm-title {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 10px;
    }
    .confirm-message {
        color: #6b7280;
        font-size: 14px;
        line-height: 1.6;
    }
    .confirm-highlight {
        background: #fef3c7;
        padding: 12px 16px;
        border-radius: 8px;
        margin: 16px 0;
        font-weight: 600;
        color: #92400e;
    }
    
    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        z-index: 1050;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-content {
        background: white;
        border-radius: 16px;
        width: 95%;
        max-width: 900px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    }
    @keyframes slideIn {
        from { transform: translateY(-30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .modal-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 16px 16px 0 0;
    }
    .modal-header h3 { font-size: 18px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 10px; }
    .modal-close {
        width: 36px; height: 36px;
        border: none;
        background: rgba(255,255,255,0.2);
        border-radius: 8px;
        cursor: pointer;
        font-size: 18px;
        color: white;
        transition: all 0.2s ease;
    }
    .modal-close:hover { background: rgba(255,255,255,0.3); transform: rotate(90deg); }
    .modal-body { padding: 24px; background: #f8fafc; }
    .modal-footer {
        padding: 16px 24px;
        background: white;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        border-radius: 0 0 16px 16px;
    }
    
    /* Form in Modal */
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-weight: 600; margin-bottom: 8px; color: #374151; }
    .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        background: white;
        transition: border-color 0.2s;
    }
    .form-select:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.15); }
    
    /* Jadwal Table in Modal */
    .jadwal-table-wrapper {
        background: white;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .jadwal-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .jadwal-table th {
        background: linear-gradient(180deg, #3b82f6, #1d4ed8);
        color: white;
        padding: 10px 6px;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
    }
    .jadwal-table th:first-child { border-radius: 8px 0 0 0; }
    .jadwal-table th:last-child { border-radius: 0 8px 0 0; }
    .jadwal-table td {
        border: 1px solid #e5e7eb;
        padding: 8px 4px;
        text-align: center;
        background: white;
    }
    .jadwal-table .hari-cell {
        text-align: left;
        font-weight: 600;
        background: #f1f5f9 !important;
        color: #1d4ed8;
        padding-left: 12px;
        min-width: 80px;
    }
    .jadwal-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #10b981;
    }
    .jadwal-checkbox:disabled {
        cursor: not-allowed;
        opacity: 0.4;
    }
    .jadwal-legend {
        display: flex;
        gap: 16px;
        margin-top: 12px;
        padding: 10px 14px;
        background: #f8fafc;
        border-radius: 8px;
        font-size: 12px;
        color: #6b7280;
    }
    .jadwal-legend span { display: flex; align-items: center; gap: 6px; }
    .legend-dot { width: 12px; height: 12px; border-radius: 3px; }
    .legend-dot.green { background: #10b981; }
    .legend-dot.gray { background: #d1d5db; }
    
    /* Toast Notification */
    .toast-container {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .toast {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        animation: toastSlide 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        max-width: 400px;
        min-width: 300px;
    }
    .toast.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    .toast.error {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    .toast.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    .toast-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .toast-content {
        flex: 1;
    }
    .toast-title {
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 2px;
    }
    .toast-message {
        font-size: 13px;
        opacity: 0.9;
    }
    .toast-close {
        background: rgba(255,255,255,0.2);
        border: none;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        color: white;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.2s;
    }
    .toast-close:hover {
        background: rgba(255,255,255,0.3);
    }
    @keyframes toastSlide {
        from { transform: translateX(120%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .toast.hide {
        animation: toastSlideOut 0.3s ease forwards;
    }
    @keyframes toastSlideOut {
        to { transform: translateX(120%); opacity: 0; }
    }
    
    @media (max-width: 768px) {
        .content-header { flex-direction: column; }
        .teacher-info-card { min-width: auto; width: 100%; }
        .stats-grid { grid-template-columns: 1fr; }
        .filter-group { flex-direction: column; align-items: stretch; }
        .toast-container { left: 16px; right: 16px; top: 16px; }
        .toast { min-width: auto; max-width: none; }
    }
</style>
@endpush

@section('content')
<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

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
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span class="badge">{{ $totalMapel }} Mapel</span>
                    <button class="btn btn-primary" onclick="openModal('modalPenugasan')" style="padding: 8px 16px; font-size: 13px;">
                        <i class="fas fa-plus"></i> Tambah Penugasan
                    </button>
                </div>
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
                                <th class="text-center" width="130">Aksi</th>
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
                                    <td class="text-center">
                                        <div style="display:flex;gap:6px;justify-content:center;">
                                            <button class="btn-action edit" onclick="editPenugasan({{ $p['id_rombel'] }}, {{ $p['id_mapel'] }})" title="Edit Jadwal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-action delete" onclick="deletePenugasan({{ $p['id_rombel'] }}, {{ $p['id_mapel'] }}, '{{ $p['nama_rombel'] }}', '{{ $p['nama_mapel'] }}')" title="Hapus Penugasan">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-total">
                                <td colspan="3" style="text-align: right;"><strong>Total Jam Mengajar per Minggu</strong></td>
                                <td class="text-center"><strong>{{ $totalJamTable }} jam</strong></td>
                                <td class="text-center">-</td>
                                <td></td>
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

<!-- Modal Tambah Penugasan -->
<div class="modal-overlay" id="modalPenugasan">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> Tambah Penugasan Mengajar</h3>
            <button class="modal-close" onclick="closeModal('modalPenugasan')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label"><i class="fas fa-school" style="color: #3b82f6;"></i> Pilih Rombel</label>
                <select id="selectRombel" class="form-select" onchange="loadJadwalKonflik()">
                    <option value="">-- Pilih Rombel --</option>
                    @foreach($rombelList as $rombel)
                        <option value="{{ $rombel->id }}">{{ $rombel->nama_rombel }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label"><i class="fas fa-book" style="color: #3b82f6;"></i> Pilih Mata Pelajaran</label>
                <select id="selectMapel" class="form-select" onchange="loadJadwalKonflik()">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($mapelList as $mapel)
                        <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" id="jadwalSection" style="display: none;">
                <label class="form-label"><i class="fas fa-clock" style="color: #3b82f6;"></i> Jadwal Pelajaran</label>
                <div class="jadwal-table-wrapper">
                    <table class="jadwal-table">
                        <thead>
                            <tr>
                                <th rowspan="2">Hari</th>
                                <th colspan="11">Jam Pelajaran ke-</th>
                            </tr>
                            <tr>
                                @for($j = 1; $j <= 11; $j++)
                                    <th>{{ $j }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @php $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; @endphp
                            @foreach($hariList as $h)
                                <tr>
                                    <td class="hari-cell">{{ $h }}</td>
                                    @for($j = 1; $j <= 11; $j++)
                                        <td>
                                            <input type="checkbox" 
                                                name="jadwal[{{ $h }}][]" 
                                                value="{{ $j }}" 
                                                class="jadwal-checkbox"
                                                data-hari="{{ $h }}"
                                                data-jam="{{ $j }}">
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="jadwal-legend">
                    <span><div class="legend-dot green"></div> Tersedia untuk dipilih</span>
                    <span><div class="legend-dot gray"></div> Sudah terisi mapel lain (disabled)</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('modalPenugasan')">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="button" class="btn btn-primary" id="btnSavePenugasan" onclick="savePenugasan()">
                <i class="fas fa-save"></i> Simpan Penugasan
            </button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal-overlay" id="modalDelete">
    <div class="modal-content" style="max-width: 450px;">
        <div class="modal-header" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
            <h3><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</h3>
            <button class="modal-close" onclick="closeModal('modalDelete')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="confirm-modal-content">
                <div class="confirm-icon">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <div class="confirm-title">Hapus Penugasan?</div>
                <div class="confirm-message">
                    Anda akan menghapus penugasan berikut:
                </div>
                <div class="confirm-highlight" id="deleteInfo"></div>
                <div class="confirm-message">
                    Semua jadwal terkait penugasan ini akan dihapus. Tindakan ini tidak dapat dibatalkan.
                </div>
            </div>
        </div>
        <div class="modal-footer" style="justify-content: center;">
            <button type="button" class="btn btn-secondary" onclick="closeModal('modalDelete')">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="button" class="btn" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;" onclick="confirmDelete()">
                <i class="fas fa-trash"></i> Ya, Hapus
            </button>
        </div>
    </div>
</div>

<!-- Hidden inputs for delete -->
<input type="hidden" id="deleteIdRombel" value="">
<input type="hidden" id="deleteIdMapel" value="">

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
    
    // Modal functions
    function openModal(id) { document.getElementById(id).classList.add('active'); }
    function closeModal(id) { document.getElementById(id).classList.remove('active'); resetJadwalForm(); }
    
    // Reset jadwal form
    function resetJadwalForm() {
        document.getElementById('selectRombel').value = '';
        document.getElementById('selectMapel').value = '';
        document.getElementById('jadwalSection').style.display = 'none';
        document.querySelectorAll('.jadwal-checkbox').forEach(cb => {
            cb.checked = false;
            cb.disabled = false;
            cb.title = '';
        });
    }
    
    // Load jadwal konflik when rombel and mapel selected
    function loadJadwalKonflik() {
        const idRombel = document.getElementById('selectRombel').value;
        const idMapel = document.getElementById('selectMapel').value;
        
        if (!idRombel || !idMapel) {
            document.getElementById('jadwalSection').style.display = 'none';
            return;
        }
        
        // Reset checkboxes
        document.querySelectorAll('.jadwal-checkbox').forEach(cb => {
            cb.checked = false;
            cb.disabled = false;
            cb.title = '';
        });
        
        // Load konflik data
        fetch(`{{ route('admin.guru.penugasan.check-jadwal', ['id' => $guru->id]) }}?id_rombel=${idRombel}&id_mapel=${idMapel}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.jadwal_terisi) {
                    Object.entries(data.jadwal_terisi).forEach(([hari, jamArray]) => {
                        jamArray.forEach(jamInfo => {
                            const cb = document.querySelector(`input[name="jadwal[${hari}][]"][value="${jamInfo.jam}"]`);
                            if (cb) {
                                cb.disabled = true;
                                cb.title = `Sudah terisi: ${jamInfo.mapel} (${jamInfo.guru})`;
                            }
                        });
                    });
                }
                document.getElementById('jadwalSection').style.display = 'block';
            })
            .catch(err => {
                console.error('Error loading jadwal:', err);
                document.getElementById('jadwalSection').style.display = 'block';
            });
    }
    
    // Save penugasan
    function savePenugasan() {
        const idRombel = document.getElementById('selectRombel').value;
        const idMapel = document.getElementById('selectMapel').value;
        
        if (!idRombel || !idMapel) {
            showToast('warning', 'Perhatian', 'Silakan pilih Rombel dan Mata Pelajaran terlebih dahulu!');
            return;
        }
        
        // Collect checked jadwal
        const jadwal = {};
        document.querySelectorAll('.jadwal-checkbox:checked:not(:disabled)').forEach(cb => {
            const hari = cb.dataset.hari;
            const jam = cb.value;
            if (!jadwal[hari]) jadwal[hari] = [];
            jadwal[hari].push(jam);
        });
        
        if (Object.keys(jadwal).length === 0) {
            showToast('warning', 'Perhatian', 'Silakan pilih minimal satu jam pelajaran!');
            return;
        }
        
        // Save via AJAX
        fetch('{{ route('admin.guru.penugasan.save', ['id' => $guru->id]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                id_rombel: idRombel,
                id_mapel: idMapel,
                jadwal: jadwal
            })
        })
        .then(res => res.json())
        .then(result => {
            if (result.status === 'success') {
                showToast('success', 'Berhasil!', result.message);
                closeModal('modalPenugasan');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('error', 'Gagal!', result.message);
            }
        })
        .catch(err => showToast('error', 'Error!', err.message));
    }
    
    // Toast notification function
    function showToast(type, title, message) {
        // Create container if not exists
        var container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.style.cssText = 'position:fixed;top:24px;right:24px;z-index:99999;';
            document.body.appendChild(container);
        }
        
        var bgColors = {
            success: 'linear-gradient(135deg, #10b981, #059669)',
            error: 'linear-gradient(135deg, #ef4444, #dc2626)',
            warning: 'linear-gradient(135deg, #f59e0b, #d97706)'
        };
        var icons = { success: '✓', error: '✕', warning: '!' };
        
        var toast = document.createElement('div');
        toast.style.cssText = 'display:flex;align-items:center;gap:14px;padding:16px 20px;border-radius:12px;' +
            'box-shadow:0 10px 40px rgba(0,0,0,0.3);margin-bottom:12px;min-width:300px;max-width:400px;' +
            'background:' + bgColors[type] + ';color:white;font-family:inherit;animation:none;';
        
        toast.innerHTML = '<div style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.2);' +
            'display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:bold;">' + icons[type] + '</div>' +
            '<div style="flex:1;">' +
                '<div style="font-weight:700;font-size:15px;margin-bottom:2px;">' + title + '</div>' +
                '<div style="font-size:13px;opacity:0.9;">' + message + '</div>' +
            '</div>' +
            '<button onclick="this.parentElement.remove()" style="background:rgba(255,255,255,0.2);border:none;' +
            'width:28px;height:28px;border-radius:6px;color:white;cursor:pointer;font-size:18px;">×</button>';
        
        container.appendChild(toast);
        
        // Auto remove after 4 seconds
        setTimeout(function() {
            if (toast.parentElement) toast.remove();
        }, 4000);
    }
    
    // Close modal on outside click
    document.querySelectorAll('.modal-overlay').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
    });
    
    // Edit mode tracking
    var isEditMode = false;
    var editIdRombel = null;
    var editIdMapel = null;
    
    // Edit penugasan - open modal with existing data
    function editPenugasan(idRombel, idMapel) {
        isEditMode = true;
        editIdRombel = idRombel;
        editIdMapel = idMapel;
        
        // Set dropdown values
        document.getElementById('selectRombel').value = idRombel;
        document.getElementById('selectMapel').value = idMapel;
        
        // Change modal title and button
        document.querySelector('#modalPenugasan .modal-header h3').innerHTML = '<i class="fas fa-edit"></i> Edit Penugasan Mengajar';
        document.getElementById('btnSavePenugasan').innerHTML = '<i class="fas fa-save"></i> Update Penugasan';
        
        openModal('modalPenugasan');
        
        // Load jadwal and mark existing ones
        loadJadwalForEdit(idRombel, idMapel);
    }
    
    // Load jadwal for editing
    function loadJadwalForEdit(idRombel, idMapel) {
        // Reset all checkboxes
        document.querySelectorAll('.jadwal-checkbox').forEach(function(cb) {
            cb.checked = false;
            cb.disabled = false;
            cb.title = '';
        });
        
        // Load konflik data and existing jadwal
        fetch('{{ route("admin.guru.penugasan.check-jadwal", ["id" => $guru->id]) }}?id_rombel=' + idRombel + '&id_mapel=' + idMapel)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success && data.jadwal_terisi) {
                    Object.keys(data.jadwal_terisi).forEach(function(hari) {
                        data.jadwal_terisi[hari].forEach(function(jamInfo) {
                            var cb = document.querySelector('input[name="jadwal[' + hari + '][]"][value="' + jamInfo.jam + '"]');
                            if (cb) {
                                // If same mapel, check it; otherwise disable
                                if (parseInt(jamInfo.id_mapel) === parseInt(idMapel)) {
                                    cb.checked = true;
                                    cb.disabled = false;
                                } else {
                                    cb.disabled = true;
                                    cb.title = 'Sudah terisi: ' + jamInfo.mapel + ' (' + jamInfo.guru + ')';
                                }
                            }
                        });
                    });
                }
                document.getElementById('jadwalSection').style.display = 'block';
            })
            .catch(function(err) {
                console.error('Error loading jadwal:', err);
                document.getElementById('jadwalSection').style.display = 'block';
            });
    }
    
    // Delete penugasan - show confirmation modal
    function deletePenugasan(idRombel, idMapel, namaRombel, namaMapel) {
        document.getElementById('deleteIdRombel').value = idRombel;
        document.getElementById('deleteIdMapel').value = idMapel;
        document.getElementById('deleteInfo').innerHTML = '<strong>' + namaMapel + '</strong><br>Rombel: ' + namaRombel;
        openModal('modalDelete');
    }
    
    // Confirm delete
    function confirmDelete() {
        var idRombel = document.getElementById('deleteIdRombel').value;
        var idMapel = document.getElementById('deleteIdMapel').value;
        
        fetch('{{ route("admin.guru.penugasan.delete", ["id" => $guru->id]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                id_rombel: idRombel,
                id_mapel: idMapel
            })
        })
        .then(function(res) { return res.json(); })
        .then(function(result) {
            closeModal('modalDelete');
            if (result.status === 'success') {
                showToast('success', 'Berhasil!', result.message);
                setTimeout(function() { location.reload(); }, 1500);
            } else {
                showToast('error', 'Gagal!', result.message);
            }
        })
        .catch(function(err) {
            showToast('error', 'Error!', err.message);
        });
    }
    
    // Override closeModal to reset edit mode
    var originalCloseModal = closeModal;
    closeModal = function(id) {
        document.getElementById(id).classList.remove('active');
        if (id === 'modalPenugasan') {
            resetJadwalForm();
            isEditMode = false;
            editIdRombel = null;
            editIdMapel = null;
            document.querySelector('#modalPenugasan .modal-header h3').innerHTML = '<i class="fas fa-plus-circle"></i> Tambah Penugasan Mengajar';
            document.getElementById('btnSavePenugasan').innerHTML = '<i class="fas fa-save"></i> Simpan Penugasan';
        }
    };
</script>
@endpush
