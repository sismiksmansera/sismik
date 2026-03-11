@extends('layouts.app')

@section('title', 'Mata Pelajaran - ' . $rombel->nama_rombel . ' | SISMIK')

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
        width: 65px;
        height: 65px;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.35);
    }
    .header-text h1 {
        margin: 0;
        color: #1e3a8a;
        font-size: 28px;
        font-weight: 700;
    }
    .header-text p {
        margin: 5px 0 0 0;
        color: #6b7280;
        font-size: 14px;
    }
    .rombel-info-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px 24px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border-left: 5px solid var(--primary);
        min-width: 280px;
    }
    .rombel-icon {
        width: 55px;
        height: 55px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 22px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    .rombel-details h3 {
        margin: 0;
        color: #1f2937;
        font-size: 20px;
        font-weight: 700;
    }
    .rombel-details p {
        margin: 5px 0 0 0;
        color: #6b7280;
        font-size: 14px;
    }
    
    /* Stats Grid - Enhanced */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 24px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        padding: 28px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 18px;
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.04);
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    }
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }
    .stat-icon.primary { 
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); 
        box-shadow: 0 6px 15px rgba(59, 130, 246, 0.35);
    }
    .stat-icon.success { 
        background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
        box-shadow: 0 6px 15px rgba(16, 185, 129, 0.35);
    }
    .stat-icon.warning { 
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); 
        box-shadow: 0 6px 15px rgba(245, 158, 11, 0.35);
    }
    .stat-info h3 {
        margin: 0;
        font-size: 30px;
        font-weight: 800;
        color: #1f2937;
    }
    .stat-info p {
        margin: 4px 0 0 0;
        color: #6b7280;
        font-size: 14px;
        font-weight: 500;
    }
    
    /* Content Section - Enhanced */
    .content-section {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 25px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 25px;
        border: 1px solid rgba(0,0,0,0.04);
    }
    .section-header {
        padding: 22px 28px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .section-header h2 {
        margin: 0;
        color: #1f2937;
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .section-header h2 i {
        color: var(--primary);
        font-size: 20px;
    }
    
    /* Modern Table - Enhanced */
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 14px;
    }
    .modern-table thead th {
        background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 16px 18px;
        font-weight: 600;
        text-align: left;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        font-size: 12px;
    }
    .modern-table thead th:first-child {
        border-radius: 12px 0 0 0;
    }
    .modern-table thead th:last-child {
        border-radius: 0 12px 0 0;
    }
    .modern-table td {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
        color: #4b5563;
        background: white;
        transition: all 0.2s ease;
    }
    .modern-table tbody tr {
        transition: all 0.2s ease;
    }
    .modern-table tbody tr:hover td {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    }
    .modern-table tbody tr:hover {
        transform: scale(1.005);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.08);
    }
    .modern-table tbody tr:last-child td:first-child {
        border-radius: 0 0 0 12px;
    }
    .modern-table tbody tr:last-child td:last-child {
        border-radius: 0 0 12px 0;
    }
    
    /* Modal - Enhanced */
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
        border-radius: 24px;
        width: 90%;
        max-width: 950px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 25px 80px rgba(0,0,0,0.25);
    }
    @keyframes slideIn {
        from { transform: translateY(-40px) scale(0.95); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }
    .modal-header {
        padding: 24px 28px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 24px 24px 0 0;
    }
    .modal-header h3 { 
        font-size: 20px; 
        font-weight: 700; 
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .modal-header h3 i {
        font-size: 22px;
    }
    .modal-close {
        width: 38px; height: 38px;
        border: none;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        cursor: pointer;
        font-size: 20px;
        color: white;
        transition: all 0.2s ease;
    }
    .modal-close:hover {
        background: rgba(255,255,255,0.35);
        transform: rotate(90deg);
    }
    .modal-body { 
        padding: 28px; 
        background: #f8fafc;
    }
    .modal-footer {
        padding: 20px 28px;
        background: white;
        border-top: 2px solid #e5e7eb;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        border-radius: 0 0 24px 24px;
    }
    
    /* Jadwal Table - Enhanced */
    .jadwal-table-wrapper {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-top: 16px;
    }
    .jadwal-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .jadwal-table th {
        background: linear-gradient(180deg, #1e40af 0%, #1d4ed8 100%);
        color: white;
        padding: 12px 8px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .jadwal-table th:first-child {
        border-radius: 10px 0 0 0;
    }
    .jadwal-table th:last-child {
        border-radius: 0 10px 0 0;
    }
    .jadwal-table td {
        border: 1px solid #e5e7eb;
        padding: 10px 6px;
        text-align: center;
        background: white;
        transition: all 0.15s ease;
    }
    .jadwal-table tbody tr:hover td {
        background: #f0f9ff;
    }
    .jadwal-table tbody tr:last-child td:first-child {
        border-radius: 0 0 0 10px;
    }
    .jadwal-table tbody tr:last-child td:last-child {
        border-radius: 0 0 10px 0;
    }
    .jadwal-table .hari-cell {
        text-align: left;
        font-weight: 700;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
        color: #1e40af;
        padding-left: 14px;
        min-width: 100px;
    }
    
    /* Checkbox - Enhanced */
    .jadwal-checkbox {
        width: 22px;
        height: 22px;
        cursor: pointer;
        accent-color: #10b981;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    .jadwal-checkbox:hover:not(:disabled) {
        transform: scale(1.15);
    }
    .jadwal-checkbox:disabled {
        cursor: not-allowed;
        opacity: 0.4;
    }
    .jadwal-checkbox.current-mapel {
        accent-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }
    
    /* Buttons - Enhanced */
    .btn-action {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 15px;
        color: white;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }
    .btn-action:hover { 
        transform: translateY(-3px) scale(1.05); 
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
    }
    
    /* Badges - Enhanced */
    .badge-jam { 
        padding: 6px 14px; 
        border-radius: 25px; 
        font-size: 12px; 
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-jam.success { 
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.2) 100%); 
        color: #047857;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .badge-jam.danger { 
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.2) 100%); 
        color: #b91c1c;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .badge-jam.warning { 
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(217, 119, 6, 0.2) 100%); 
        color: #b45309;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }
    
    .badge-status {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .badge-status.success { 
        background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
        color: white;
        box-shadow: 0 3px 10px rgba(16, 185, 129, 0.35);
    }
    .badge-status.danger { 
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); 
        color: white;
        box-shadow: 0 3px 10px rgba(239, 68, 68, 0.35);
    }
    
    .badge-guru {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 6px 14px;
        border-radius: 25px;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
        display: inline-block;
    }
    
    .action-footer {
        padding: 20px 28px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-top: 2px solid #e5e7eb;
    }
    
    /* Alert in Modal */
    .alert-jadwal {
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border: 1px solid #f59e0b;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
    }
    .alert-jadwal i {
        font-size: 20px;
        color: #d97706;
    }
    
    /* Form Select Enhanced */
    .form-select-enhanced {
        padding: 14px 18px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 15px;
        background: white;
        transition: all 0.2s ease;
        width: 100%;
    }
    .form-select-enhanced:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
        outline: none;
    }
    
    /* Legend */
    .jadwal-legend {
        display: flex;
        gap: 20px;
        margin-top: 16px;
        padding: 12px 16px;
        background: #f8fafc;
        border-radius: 10px;
        font-size: 13px;
        color: #6b7280;
    }
    .jadwal-legend span {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .legend-dot {
        width: 14px;
        height: 14px;
        border-radius: 4px;
    }
    .legend-dot.green { background: #10b981; }
    .legend-dot.gray { background: #d1d5db; }
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
                    <i class="fas fa-book"></i>
                </div>
                <div class="header-text">
                    <h1>Mata Pelajaran</h1>
                    <p style="font-size: 12px;">
                        Tahun: <strong>{{ $tahunPelajaran }}</strong> | 
                        Semester: <strong>{{ ucfirst($semester) }}</strong>
                    </p>
                </div>
            </div>
            
            <div class="rombel-info-card">
                <div class="rombel-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>      
                <div class="rombel-details">
                    <h3>{{ $rombel->nama_rombel }}</h3>
                    <p style="font-size: 14px;">
                        Wali Kelas: <strong>{{ $rombel->wali_kelas ?: '-' }}</strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $mapelList->count() }}</h3>
                    <p>Total Mapel</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $mapelAktif }}</h3>
                    <p>Mapel Aktif</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalJam }} Jam</h3>
                    <p>Total Jam</p>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-list-alt"></i> Daftar Mata Pelajaran</h2>
            </div>
            
            <div style="overflow-x: auto;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">No</th>
                            <th width="200">Nama Mapel</th>
                            <th width="300">Guru Pengampu</th>
                            <th width="150" class="text-center">Jumlah Jam</th>
                            <th width="100" class="text-center">Status</th>
                            <th width="100" class="text-center">Setting</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mapelList as $index => $mapel)
                            @php
                                $info = $mapelInfo[$mapel->id] ?? ['guru' => '-', 'jam' => 0, 'aktif' => false];
                                $isAgamaNonIslam = str_contains($mapel->nama_mapel, 'Pendidikan Agama') && !str_contains($mapel->nama_mapel, 'Islam');
                            @endphp
                            <tr>
                                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                <td>{{ $mapel->nama_mapel }}</td>
                                <td>
                                    @if($info['guru'] != '-')
                                        <span class="badge-guru">{{ $info['guru'] }}</span>
                                    @else
                                        <span style="color: #d1d5db; font-style: italic;">Belum ada</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($info['jam'] > 0)
                                        @if($isAgamaNonIslam)
                                            <span class="badge-jam warning">
                                                <i class="fas fa-info-circle"></i> {{ $info['jam'] }} Jam
                                            </span>
                                        @else
                                            <span class="badge-jam success">{{ $info['jam'] }} Jam</span>
                                        @endif
                                    @else
                                        <span class="badge-jam danger">0 Jam</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($info['aktif'])
                                        @if($isAgamaNonIslam)
                                            <span class="badge-status" style="background: #f59e0b;">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                        @else
                                            <span class="badge-status success">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge-status danger">
                                            <i class="fas fa-times-circle"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn-action" onclick="openJadwalModal({{ $mapel->id }}, '{{ addslashes($mapel->nama_mapel) }}')" title="Setting Jadwal">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center" style="padding: 40px; color: #9ca3af;">
                                    <i class="fas fa-inbox" style="font-size: 40px; margin-bottom: 10px;"></i>
                                    <p>Belum ada data mata pelajaran</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="action-footer">
                <a href="{{ route('admin.rombel.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Rombel
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Setting Jadwal -->
<div class="modal-overlay" id="modalJadwal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="jadwalModalLabel"><i class="fas fa-calendar-alt"></i> Setting Jadwal</h3>
            <button class="modal-close" onclick="closeModal('modalJadwal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="alert-jadwal">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Perhatian:</strong> Jam yang sudah terisi oleh mata pelajaran lain tidak dapat dipilih.
                </div>
            </div>
            
            <form id="formJadwal">
                <input type="hidden" name="id_mapel" id="id_mapel">
                <input type="hidden" name="id_rombel" value="{{ $rombel->id }}">
                <input type="hidden" name="tahun_pelajaran" value="{{ $tahunPelajaran }}">
                <input type="hidden" name="semester" value="{{ $semester }}">
                
                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label" style="font-weight: 700; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; color: #1f2937;">
                        <i class="fas fa-user-tie" style="color: var(--primary);"></i> Nama Guru Pengampu
                    </label>
                    <select name="nama_guru" id="nama_guru" class="form-select-enhanced" required>
                        <option value="">-- Pilih Guru Pengampu --</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->nama }}">{{ $guru->nama }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" style="font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; color: #1f2937;">
                        <i class="fas fa-clock" style="color: var(--primary);"></i> Jadwal Pelajaran
                    </label>
                    <div class="jadwal-table-wrapper">
                        <table class="jadwal-table">
                            <thead>
                                <tr>
                                    <th rowspan="2">Hari</th>
                                    <th colspan="11">Jam ke-</th>
                                </tr>
                                <tr>
                                    @for($j = 1; $j <= 11; $j++)
                                        <th>{{ $j }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @php $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; @endphp
                                @foreach($hari as $h)
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
                        <span><div class="legend-dot green"></div> Jadwal mapel ini</span>
                        <span><div class="legend-dot gray"></div> Sudah terisi mapel lain (disabled)</span>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('modalJadwal')">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="button" class="btn btn-primary" onclick="saveJadwal()">
                <i class="fas fa-save"></i> Simpan Jadwal
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    
    function openModal(id) { document.getElementById(id).classList.add('active'); }
    function closeModal(id) { document.getElementById(id).classList.remove('active'); }
    
    // Open jadwal modal
    function openJadwalModal(id, namaMapel) {
        document.getElementById('id_mapel').value = id;
        document.getElementById('jadwalModalLabel').innerHTML = '<i class="fas fa-calendar-alt"></i> Setting Jadwal - ' + namaMapel;
        
        // Reset all checkboxes
        document.querySelectorAll('#formJadwal input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
            cb.disabled = false;
            cb.classList.remove('current-mapel');
            cb.title = '';
        });
        document.getElementById('nama_guru').value = '';
        
        // Check if agama non-Islam
        const isAgamaNonIslam = namaMapel.includes('Pendidikan Agama') && !namaMapel.includes('Islam');
        
        if (isAgamaNonIslam) {
            // For non-Islam agama, load jadwal from Agama Islam (read-only)
            loadAgamaIslamJadwal(id, namaMapel);
        } else {
            // Normal load
            loadJadwal(id);
        }
        
        openModal('modalJadwal');
    }
    
    // Load normal jadwal
    function loadJadwal(idMapel) {
        const idRombel = document.querySelector('input[name="id_rombel"]').value;
        const tahun = document.querySelector('input[name="tahun_pelajaran"]').value;
        const semester = document.querySelector('input[name="semester"]').value;
        
        fetch(`{{ route('admin.mapel.jadwal.get') }}?id_mapel=${idMapel}&id_rombel=${idRombel}&tahun_pelajaran=${tahun}&semester=${semester}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('nama_guru').value = data.nama_guru || '';
                    
                    // Check current mapel jadwal
                    if (data.jadwal) {
                        Object.entries(data.jadwal).forEach(([hari, jamArray]) => {
                            jamArray.forEach(jam => {
                                const cb = document.querySelector(`input[name="jadwal[${hari}][]"][value="${jam}"]`);
                                if (cb) {
                                    cb.checked = true;
                                    cb.classList.add('current-mapel');
                                }
                            });
                        });
                    }
                    
                    // Disable jam already used by other mapel
                    if (data.jadwal_terisi) {
                        Object.entries(data.jadwal_terisi).forEach(([hari, jamArray]) => {
                            jamArray.forEach(jamInfo => {
                                const cb = document.querySelector(`input[name="jadwal[${hari}][]"][value="${jamInfo.jam}"]`);
                                if (cb && !cb.checked) {
                                    cb.disabled = true;
                                    cb.title = `Sudah diisi: ${jamInfo.mapel} (${jamInfo.guru})`;
                                }
                            });
                        });
                    }
                }
            });
    }
    
    // Load agama Islam jadwal for non-Islam agama subjects
    function loadAgamaIslamJadwal(idMapel, namaMapel) {
        // First get Agama Islam ID
        fetch('{{ route('admin.mapel.agama-islam-id') }}')
            .then(res => res.json())
            .then(data => {
                if (data.id_agama_islam) {
                    // Disable all checkboxes
                    document.querySelectorAll('#formJadwal input[type="checkbox"]').forEach(cb => {
                        cb.disabled = true;
                        cb.title = 'Jadwal mengikuti Pendidikan Agama Islam';
                    });
                    
                    // Load Agama Islam jadwal
                    const idRombel = document.querySelector('input[name="id_rombel"]').value;
                    const tahun = document.querySelector('input[name="tahun_pelajaran"]').value;
                    const semester = document.querySelector('input[name="semester"]').value;
                    
                    fetch(`{{ route('admin.mapel.jadwal.get') }}?id_mapel=${data.id_agama_islam}&id_rombel=${idRombel}&tahun_pelajaran=${tahun}&semester=${semester}`)
                        .then(res => res.json())
                        .then(islamData => {
                            if (islamData.success && islamData.jadwal) {
                                Object.entries(islamData.jadwal).forEach(([hari, jamArray]) => {
                                    jamArray.forEach(jam => {
                                        const cb = document.querySelector(`input[name="jadwal[${hari}][]"][value="${jam}"]`);
                                        if (cb) {
                                            cb.checked = true;
                                            cb.classList.add('current-mapel');
                                        }
                                    });
                                });
                            }
                        });
                    
                    // Also load current mapel guru
                    fetch(`{{ route('admin.mapel.jadwal.get') }}?id_mapel=${idMapel}&id_rombel=${idRombel}&tahun_pelajaran=${tahun}&semester=${semester}`)
                        .then(res => res.json())
                        .then(currentData => {
                            if (currentData.success) {
                                document.getElementById('nama_guru').value = currentData.nama_guru || '';
                            }
                        });
                }
            });
    }
    
    // Save jadwal
    function saveJadwal() {
        const form = document.getElementById('formJadwal');
        const formData = new FormData(form);
        
        // Convert to JSON
        const data = {
            id_mapel: formData.get('id_mapel'),
            id_rombel: formData.get('id_rombel'),
            tahun_pelajaran: formData.get('tahun_pelajaran'),
            semester: formData.get('semester'),
            nama_guru: formData.get('nama_guru'),
            jadwal: {}
        };
        
        // Collect checked jadwal
        document.querySelectorAll('.jadwal-checkbox:checked:not(:disabled)').forEach(cb => {
            const hari = cb.dataset.hari;
            const jam = cb.value;
            if (!data.jadwal[hari]) data.jadwal[hari] = [];
            data.jadwal[hari].push(jam);
        });
        
        fetch('{{ route('admin.mapel.jadwal.save') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            if (result.status === 'success') {
                alert(result.message);
                closeModal('modalJadwal');
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(err => alert('Error: ' + err.message));
    }
    
    // Close modal on outside click
    document.querySelectorAll('.modal-overlay').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
    });
</script>
@endpush
