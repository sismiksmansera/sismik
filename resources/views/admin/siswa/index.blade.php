@extends('layouts.app')

@section('title', 'Manajemen Siswa | SISMIK')

@push('styles')
<style>
    .content-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .header-content { display: flex; align-items: center; gap: 20px; }
    .header-icon {
        width: 70px;
        height: 70px;
        background: rgba(255,255,255,0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
    }
    .header-text h1 { font-size: 28px; font-weight: 700; margin-bottom: 5px; }
    .header-text p { opacity: 0.9; }
    
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
        padding: 15px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .content-section {
        background: var(--white);
        border-radius: 16px;
        padding: 0;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .table-controls {
        padding: 16px 24px;
        background: #f8fafc;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }
    .control-left { display: flex; align-items: center; gap: 10px; }
    .control-right { display: flex; align-items: center; gap: 16px; }
    .search-box { position: relative; }
    .search-box input {
        padding: 10px 16px 10px 40px;
        border: 2px solid var(--gray-200);
        border-radius: 10px;
        width: 280px;
        font-size: 14px;
    }
    .search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }
    .rows-select {
        padding: 8px 12px;
        border: 2px solid var(--gray-200);
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-700);
        background: white;
        cursor: pointer;
    }
    .rows-select:focus {
        outline: none;
        border-color: var(--primary);
    }
    
    .modern-table { width: 100%; border-collapse: collapse; }
    .modern-table th {
        background: var(--gray-100);
        padding: 14px 16px;
        text-align: center;
        font-weight: 600;
        font-size: 12px;
        color: var(--gray-600);
        text-transform: uppercase;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .btn-info-outline {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }
    .modern-table td {
        padding: 10px 14px;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }
    .modern-table tbody tr:hover { background: var(--gray-50); }
    
    .student-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--info));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 13px;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .student-avatar:hover {
        transform: scale(1.1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .student-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .student-info { display: flex; align-items: center; gap: 10px; }
    .student-name { font-weight: 600; font-size: 13px; }
    .student-id { font-size: 11px; color: var(--gray-500); }
    
    .gender-badge {
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .gender-l { background: #dbeafe; color: #1e40af; }
    .gender-p { background: #fce7f3; color: #be185d; }
    
    /* Clickable Display Cells */
    .editable-cell {
        cursor: pointer;
        padding: 8px 12px;
        border-radius: 8px;
        transition: all 0.2s;
        min-height: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    .editable-cell:hover {
        background: rgba(79, 70, 229, 0.1);
    }
    .editable-cell .main-value {
        font-size: 12px;
        font-weight: 600;
        color: var(--gray-700);
    }
    .editable-cell .sub-info {
        font-size: 10px;
        color: var(--gray-400);
        margin-top: 2px;
    }
    .editable-cell .empty-value {
        color: #9ca3af;
        font-style: italic;
        font-size: 11px;
    }
    
    .action-buttons-small { display: flex; gap: 6px; justify-content: center; }
    .btn-action {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 11px;
        text-decoration: none;
        color: white;
    }
    .btn-action:hover { transform: translateY(-2px); }
    .btn-warning { background: linear-gradient(135deg, #F59E0B, #D97706); }
    .btn-danger { background: linear-gradient(135deg, #EF4444, #DC2626); }
    
    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: var(--gray-500);
    }
    .empty-state i { font-size: 48px; margin-bottom: 16px; color: var(--gray-300); }
    
    .pagination-wrapper {
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid var(--gray-200);
        background: #f8fafc;
        flex-wrap: wrap;
        gap: 12px;
    }
    .pagination-wrapper .pagination-info {
        font-size: 13px;
        color: var(--gray-600);
    }
    
    /* Laravel Pagination Styling */
    .pagination-wrapper nav {
        display: flex;
        align-items: center;
    }
    .pagination-wrapper nav > div:first-child {
        display: none; /* Hide mobile text */
    }
    .pagination-wrapper nav > div:last-child {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pagination-wrapper nav p {
        font-size: 13px;
        color: var(--gray-600);
        margin: 0;
    }
    .pagination-wrapper nav span[aria-current="page"] span,
    .pagination-wrapper nav a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .pagination-wrapper nav span[aria-current="page"] span {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }
    .pagination-wrapper nav a {
        background: white;
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
    }
    .pagination-wrapper nav a:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }
    .pagination-wrapper nav span[aria-disabled="true"] span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        font-size: 13px;
        background: var(--gray-100);
        color: var(--gray-400);
        border-radius: 8px;
        cursor: not-allowed;
    }
    /* Fix for SVG icons */
    .pagination-wrapper nav svg {
        width: 16px;
        height: 16px;
    }
    
    /* Modal Styles */
    .modal { 
        display: none; 
        position: fixed; 
        top: 0; left: 0; 
        width: 100%; height: 100%; 
        background: rgba(0,0,0,0.5); 
        z-index: 1050; 
        align-items: center; 
        justify-content: center; 
        padding: 20px;
    }
    .modal.show { display: flex; }
    .modal-dialog {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow: hidden;
        animation: slideIn 0.3s ease;
    }
    @keyframes slideIn {
        from { transform: translateY(-30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .modal-header {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header h3 { font-size: 16px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 10px; }
    .modal-close { background: none; border: none; color: white; font-size: 20px; cursor: pointer; }
    .modal-body { padding: 20px; max-height: 60vh; overflow-y: auto; }
    .modal-footer { padding: 16px 20px; border-top: 1px solid var(--gray-200); display: flex; gap: 10px; justify-content: flex-end; }
    
    /* Student Info Card in Modal */
    .student-info-card {
        background: #f8fafc;
        border: 1px solid var(--gray-200);
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .student-info-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 8px;
    }
    .student-info-row:last-child { margin-bottom: 0; }
    .info-item { }
    .info-label { display: block; font-size: 11px; color: var(--gray-500); margin-bottom: 2px; }
    .info-value { font-size: 13px; font-weight: 600; color: var(--gray-700); }
    
    /* Angkatan Grid */
    .angkatan-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }
    .angkatan-option {
        padding: 14px 10px;
        border: 2px solid var(--gray-200);
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .angkatan-option:hover {
        border-color: var(--primary);
        background: rgba(79, 70, 229, 0.05);
    }
    .angkatan-option.selected {
        border-color: var(--primary);
        background: var(--primary);
        color: white;
    }
    .angkatan-option .year { font-size: 18px; font-weight: 700; }
    .angkatan-option .label { font-size: 10px; opacity: 0.7; }
    .angkatan-option.delete-option { border-color: #fecaca; }
    .angkatan-option.delete-option:hover { border-color: #ef4444; background: #fef2f2; }
    .angkatan-option.delete-option .year { color: #dc2626; }
    
    /* Semester Forms Grid */
    .semester-forms-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    .semester-form-item {
        background: #f8fafc;
        border: 1px solid var(--gray-200);
        border-radius: 10px;
        padding: 12px;
    }
    .semester-form-item.active {
        background: #dbeafe;
        border-color: #3b82f6;
    }
    .semester-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .semester-label { font-size: 12px; font-weight: 600; }
    .semester-badge {
        font-size: 9px;
        padding: 2px 6px;
        border-radius: 10px;
        background: #3b82f6;
        color: white;
    }
    .semester-select {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        font-size: 12px;
    }
    
    /* Warning Notice */
    .warning-notice {
        background: #fef3c7;
        border: 1px solid #f59e0b;
        border-radius: 8px;
        padding: 12px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin-top: 16px;
    }
    .warning-notice i { color: #f59e0b; margin-top: 2px; }
    .warning-notice .text { font-size: 12px; color: #92400e; }
    
    /* Toast */
    .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 16px 24px;
        border-radius: 10px;
        background: #10b981;
        color: white;
        font-weight: 600;
        font-size: 14px;
        z-index: 2000;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s;
    }
    .toast.show { opacity: 1; transform: translateY(0); }
    .toast.error { background: #ef4444; }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="header-text">
                    <h1>Manajemen Data Siswa</h1>
                    @if($tahunAktif && $semesterAktif)
                        <p style="color: #86efac; font-weight: 600;">
                            <i class="fas fa-calendar-check"></i> 
                            Semester Aktif: {{ $tahunAktif }} - {{ ucfirst($semesterAktif) }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button id="deleteSelected" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus Terpilih
            </button>
            <a href="{{ route('admin.siswa.create') }}" class="btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">
                <i class="fas fa-plus"></i> Tambah Siswa
            </a>
            <a href="{{ route('admin.siswa.import') }}" class="btn" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;">
                <i class="fas fa-file-import"></i> Import Data
            </a>
        </div>

        <!-- Content Section -->
        <div class="content-section">
            <div class="table-controls">
                <div class="control-left">
                    <span style="font-weight: 600; color: var(--gray-600);">
                        <i class="fas fa-list"></i> Total: {{ $siswaList->total() }} Siswa
                    </span>
                    <span style="margin-left: 20px; display: flex; align-items: center; gap: 8px;">
                        <label style="font-size: 13px; color: var(--gray-500);">Tampilkan:</label>
                        <select id="rowsPerPage" class="rows-select" onchange="changePerPage(this.value)">
                            <option value="10" {{ request('per_page', 25) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span style="font-size: 13px; color: var(--gray-500);">baris</span>
                    </span>
                </div>
                <div class="control-right">
                    <form method="GET" action="{{ route('admin.siswa.index') }}" id="searchForm">
                        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Cari nama/NIS/NISN..." onchange="this.form.submit()">
                        </div>
                    </form>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="modern-table" id="siswaTable">
                    <thead>
                        <tr>
                            <th class="text-center" width="40">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th class="text-center" width="70">Aksi</th>
                            <th class="text-center" width="40">No</th>
                            <th>Nama Siswa</th>
                            <th class="text-center">NIS</th>
                            <th class="text-center">NISN</th>
                            <th class="text-center">JK</th>
                            <th class="text-center" style="background: linear-gradient(0deg, #f59e0b, #d97706); color: white;">Angkatan</th>
                            <th class="text-center" style="background: linear-gradient(0deg, #3b82f6, #1d4ed8); color: white;">Rombel Aktif</th>
                            <th class="text-center" style="background: linear-gradient(0deg, #10b981, #059669); color: white;">Guru BK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaList as $index => $siswa)
                            @php
                                // Calculate semester aktif
                                $semesterAktifSiswa = 1;
                                if($siswa->angkatan_masuk && $tahunAktif && $semesterAktif) {
                                    $tahunParts = explode('/', $tahunAktif);
                                    $tahunMulai = intval($tahunParts[0] ?? 0);
                                    $angkatanInt = intval($siswa->angkatan_masuk);
                                    $selisihTahun = $tahunMulai - $angkatanInt;
                                    
                                    if ($selisihTahun == 0) {
                                        $semesterAktifSiswa = (strtolower($semesterAktif) == 'ganjil') ? 1 : 2;
                                    } elseif ($selisihTahun == 1) {
                                        $semesterAktifSiswa = (strtolower($semesterAktif) == 'ganjil') ? 3 : 4;
                                    } elseif ($selisihTahun == 2) {
                                        $semesterAktifSiswa = (strtolower($semesterAktif) == 'ganjil') ? 5 : 6;
                                    }
                                }
                                
                                $rombelField = "rombel_semester_{$semesterAktifSiswa}";
                                $rombelAktif = $siswa->$rombelField ?? '';
                                $bkField = "bk_semester_{$semesterAktifSiswa}";
                                $bkAktif = $siswa->$bkField ?? '';
                                
                                // Get all semester data for modals
                                $rombelData = [];
                                $bkData = [];
                                for ($i = 1; $i <= 6; $i++) {
                                    $rf = "rombel_semester_{$i}";
                                    $bf = "bk_semester_{$i}";
                                    $rombelData[$i] = $siswa->$rf ?? '';
                                    $bkData[$i] = $siswa->$bf ?? '';
                                }
                            @endphp
                            <tr data-id="{{ $siswa->id }}" 
                                data-nisn="{{ $siswa->nisn }}" 
                                data-nis="{{ $siswa->nis }}"
                                data-nama="{{ $siswa->nama }}"
                                data-angkatan="{{ $siswa->angkatan_masuk }}" 
                                data-semester-aktif="{{ $semesterAktifSiswa }}"
                                data-rombel='@json($rombelData)'
                                data-bk='@json($bkData)'>
                                <td class="text-center">
                                    <input type="checkbox" class="row-check" value="{{ $siswa->id }}">
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons-small">
                                        <a href="{{ route('admin.siswa.edit', $siswa->id) }}" class="btn-action btn-warning" title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn-action btn-info-outline" title="Riwayat Akademik">
                                            <i class="fas fa-book-open"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-center">{{ $siswaList->firstItem() + $index }}</td>
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar" onclick="openFotoModal('{{ $siswa->nama }}', '{{ $siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto) ? asset('storage/siswa/' . $siswa->foto) : '' }}', {{ $siswa->id }})">
                                            @if($siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto))
                                                <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}">
                                            @else
                                                {{ strtoupper(substr($siswa->nama, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="student-name">{{ $siswa->nama }}</div>
                                            <div class="student-id">{{ $siswa->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ $siswa->nis }}</td>
                                <td class="text-center">{{ $siswa->nisn }}</td>
                                <td class="text-center">
                                    @if($siswa->jk)
                                        <span class="gender-badge {{ $siswa->jk == 'Laki-laki' ? 'gender-l' : 'gender-p' }}">
                                            {{ $siswa->jk == 'Laki-laki' ? 'L' : 'P' }}
                                        </span>
                                    @else - @endif
                                </td>
                                <!-- Angkatan (Clickable) -->
                                <td class="text-center">
                                    <div class="editable-cell angkatan-cell" onclick="openAngkatanModal(this.closest('tr'))">
                                        @if($siswa->angkatan_masuk)
                                            <span class="main-value">{{ $siswa->angkatan_masuk }}</span>
                                        @else
                                            <span class="empty-value">Belum diatur</span>
                                        @endif
                                    </div>
                                </td>
                                <!-- Rombel Aktif (Clickable) -->
                                <td class="text-center">
                                    <div class="editable-cell rombel-cell" onclick="openRombelModal(this.closest('tr'))">
                                        @if($rombelAktif)
                                            <span class="main-value">{{ $rombelAktif }}</span>
                                            <span class="sub-info">Smt {{ $semesterAktifSiswa }}</span>
                                        @else
                                            <span class="empty-value">Belum diatur</span>
                                            <span class="sub-info">Smt {{ $semesterAktifSiswa }}</span>
                                        @endif
                                    </div>
                                </td>
                                <!-- Guru BK (Clickable) -->
                                <td class="text-center">
                                    <div class="editable-cell bk-cell" onclick="openBKModal(this.closest('tr'))">
                                        @if($bkAktif)
                                            <span class="main-value">{{ $bkAktif }}</span>
                                            <span class="sub-info">Smt {{ $semesterAktifSiswa }}</span>
                                        @else
                                            <span class="empty-value">Belum diatur</span>
                                            <span class="sub-info">Smt {{ $semesterAktifSiswa }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <i class="fas fa-user-graduate"></i>
                                        <h3>Tidak Ada Data Siswa</h3>
                                        <p>Belum ada data siswa terdaftar</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($siswaList->hasPages())
                <div class="pagination-wrapper">
                    <span class="pagination-info">Menampilkan {{ $siswaList->firstItem() }}-{{ $siswaList->lastItem() }} dari {{ $siswaList->total() }} data</span>
                    {{ $siswaList->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div class="modal" id="modalDelete">
    <div class="modal-dialog" style="max-width: 400px;">
        <div class="modal-header" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
            <h3><i class="fas fa-trash"></i> Hapus Siswa</h3>
            <button class="modal-close" onclick="closeModal('modalDelete')">&times;</button>
        </div>
        <div class="modal-body text-center" style="padding: 30px;">
            <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #f59e0b; margin-bottom: 16px;"></i>
            <p>Apakah Anda yakin ingin menghapus data siswa ini?</p>
        </div>
        <div class="modal-footer" style="justify-content: center;">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalDelete')">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Angkatan -->
<div class="modal" id="modalAngkatan">
    <div class="modal-dialog">
        <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <h3><i class="fas fa-calendar-alt"></i> Edit Angkatan Masuk</h3>
            <button class="modal-close" onclick="closeModal('modalAngkatan')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="student-info-card">
                <div class="student-info-row">
                    <div class="info-item">
                        <span class="info-label">Siswa</span>
                        <span class="info-value" id="angkatanSiswaName">-</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Angkatan Saat Ini</span>
                        <span class="info-value" id="angkatanCurrent">-</span>
                    </div>
                </div>
                <div class="student-info-row">
                    <div class="info-item">
                        <span class="info-label">NIS</span>
                        <span class="info-value" id="angkatanNIS">-</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">NISN</span>
                        <span class="info-value" id="angkatanNISN">-</span>
                    </div>
                </div>
            </div>
            
            <div style="margin-bottom: 12px; font-weight: 600; color: var(--gray-600);">
                <i class="fas fa-graduation-cap"></i> Pilih Tahun Angkatan
            </div>
            <div class="angkatan-grid" id="angkatanGrid">
                @foreach(range(date('Y') - 3, date('Y') + 1) as $tahun)
                    <div class="angkatan-option" data-year="{{ $tahun }}">
                        <div class="year">{{ $tahun }}</div>
                        <div class="label">Angkatan</div>
                    </div>
                @endforeach
                <div class="angkatan-option delete-option" data-year="">
                    <div class="year"><i class="fas fa-times-circle"></i></div>
                    <div class="label">Hapus</div>
                </div>
            </div>
            
            <div class="warning-notice">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="text">
                    <strong>Perhatian:</strong> Mengubah angkatan akan mempengaruhi perhitungan semester aktif.
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalAngkatan')">Batal</button>
            <button class="btn btn-warning" id="saveAngkatan" style="color: white;">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </div>
</div>

<!-- Modal Foto Profil Siswa -->
<div class="modal" id="modalFoto" style="background: rgba(0,0,0,0.9);">
    <div style="position: relative; max-width: 90%; max-height: 90%;">
        <button class="modal-close" onclick="closeModal('modalFoto')" style="position: absolute; top: -40px; right: 0; background: none; border: none; font-size: 28px; cursor: pointer; color: white;">&times;</button>
        <div style="text-align: center;">
            <div id="fotoContainer" style="background: white; padding: 8px; border-radius: 12px; display: inline-block;">
                <img id="fotoFullImg" src="" alt="Foto Siswa" style="max-width: 400px; max-height: 60vh; border-radius: 8px;">
            </div>
            <div id="fotoNoPhoto" style="display: none; background: linear-gradient(135deg, #4f46e5, #7c3aed); width: 200px; height: 200px; border-radius: 50%; margin: 0 auto; display: none; align-items: center; justify-content: center;">
                <span id="fotoInitials" style="font-size: 80px; color: white; font-weight: bold;"></span>
            </div>
            <div style="margin-top: 20px; color: white; text-align: center;">
                <h4 id="fotoNama" style="margin: 0; font-size: 1.4rem;"></h4>
                <p style="opacity: 0.8; margin: 8px 0;">Foto Profil Siswa</p>
                <a id="fotoEditLink" href="#" style="display: inline-block; padding: 10px 20px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; margin-top: 10px;">
                    <i class="fas fa-edit"></i> Edit Data Siswa
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Rombel All Semesters -->
<div class="modal" id="modalRombel">
    <div class="modal-dialog" style="max-width: 550px;">
        <div class="modal-header">
            <h3><i class="fas fa-users-class"></i> Edit Rombel Semua Semester</h3>
            <button class="modal-close" onclick="closeModal('modalRombel')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="student-info-card">
                <div class="student-info-row">
                    <div class="info-item">
                        <span class="info-label">Siswa</span>
                        <span class="info-value" id="rombelSiswaName">-</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Angkatan</span>
                        <span class="info-value" id="rombelAngkatan">-</span>
                    </div>
                </div>
            </div>
            
            <div style="margin-bottom: 12px; font-weight: 600; color: var(--gray-600);">
                <i class="fas fa-layer-group"></i> Rombel per Semester
            </div>
            <div class="semester-forms-grid" id="rombelFormsGrid">
                <!-- Generated by JS -->
            </div>
            
            <div class="warning-notice" style="background: #dbeafe; border-color: #3b82f6;">
                <i class="fas fa-info-circle" style="color: #3b82f6;"></i>
                <div class="text" style="color: #1e40af;">
                    <strong>Semester aktif:</strong> {{ $tahunAktif }} - {{ ucfirst($semesterAktif) }}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalRombel')">Batal</button>
            <button class="btn btn-primary" id="saveRombel">
                <i class="fas fa-save"></i> Simpan Semua
            </button>
        </div>
    </div>
</div>

<!-- Modal Edit Guru BK All Semesters -->
<div class="modal" id="modalBK">
    <div class="modal-dialog" style="max-width: 550px;">
        <div class="modal-header" style="background: linear-gradient(135deg, #10b981, #059669);">
            <h3><i class="fas fa-user-tie"></i> Edit Guru BK Semua Semester</h3>
            <button class="modal-close" onclick="closeModal('modalBK')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="student-info-card">
                <div class="student-info-row">
                    <div class="info-item">
                        <span class="info-label">Siswa</span>
                        <span class="info-value" id="bkSiswaName">-</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Angkatan</span>
                        <span class="info-value" id="bkAngkatan">-</span>
                    </div>
                </div>
            </div>
            
            <div style="margin-bottom: 12px; font-weight: 600; color: var(--gray-600);">
                <i class="fas fa-user-graduate"></i> Guru BK per Semester
            </div>
            <div class="semester-forms-grid" id="bkFormsGrid">
                <!-- Generated by JS -->
            </div>
            
            <div class="warning-notice" style="background: #d1fae5; border-color: #10b981;">
                <i class="fas fa-info-circle" style="color: #10b981;"></i>
                <div class="text" style="color: #065f46;">
                    <strong>Semester aktif:</strong> {{ $tahunAktif }} - {{ ucfirst($semesterAktif) }}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalBK')">Batal</button>
            <button class="btn btn-success" id="saveBK">
                <i class="fas fa-save"></i> Simpan Semua
            </button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>
@endsection

@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';
const rombelOptions = @json($rombelList);
const bkOptions = @json($guruBKList->pluck('nama'));

let currentRow = null;

// Modal Functions
function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); currentRow = null; }

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast show ' + type;
    setTimeout(() => { toast.classList.remove('show'); }, 3000);
}

// Change rows per page
function changePerPage(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    url.searchParams.set('page', 1); // Reset to page 1
    window.location.href = url.toString();
}

// Delete Confirmation
function confirmDelete(id) {
    document.getElementById('deleteForm').action = '/admin/siswa/' + id;
    openModal('modalDelete');
}

// Select All
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
});

// Delete Selected
document.getElementById('deleteSelected')?.addEventListener('click', function() {
    const selected = Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert('Pilih minimal satu siswa untuk dihapus');
        return;
    }
    if (!confirm(`Hapus ${selected.length} siswa?`)) return;

    fetch('{{ route("admin.siswa.delete-multiple") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ ids: selected })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) { showToast(data.message); location.reload(); }
        else { alert('Gagal: ' + data.message); }
    });
});

// Open Angkatan Modal
function openAngkatanModal(row) {
    currentRow = row;
    const data = row.dataset;
    
    document.getElementById('angkatanSiswaName').textContent = data.nama;
    document.getElementById('angkatanCurrent').textContent = data.angkatan || 'Belum diatur';
    document.getElementById('angkatanNIS').textContent = data.nis || '-';
    document.getElementById('angkatanNISN').textContent = data.nisn || '-';
    
    // Reset selection
    document.querySelectorAll('#angkatanGrid .angkatan-option').forEach(opt => {
        opt.classList.remove('selected');
        if (opt.dataset.year === data.angkatan) opt.classList.add('selected');
    });
    
    openModal('modalAngkatan');
}

// Angkatan Grid Click
document.querySelectorAll('#angkatanGrid .angkatan-option').forEach(opt => {
    opt.addEventListener('click', function() {
        document.querySelectorAll('#angkatanGrid .angkatan-option').forEach(o => o.classList.remove('selected'));
        this.classList.add('selected');
    });
});

// Save Angkatan
document.getElementById('saveAngkatan')?.addEventListener('click', function() {
    if (!currentRow) return;
    const selected = document.querySelector('#angkatanGrid .angkatan-option.selected');
    const angkatan = selected ? selected.dataset.year : '';
    
    fetch('{{ route("admin.siswa.update-angkatan") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ nisn: currentRow.dataset.nisn, angkatan })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Angkatan berhasil diperbarui');
            closeModal('modalAngkatan');
            location.reload(); // Reload to recalculate semester
        } else {
            alert('Gagal: ' + (data.msg || ''));
        }
    });
});

// Open Rombel Modal
function openRombelModal(row) {
    currentRow = row;
    const data = row.dataset;
    const rombelData = JSON.parse(data.rombel || '{}');
    const semesterAktif = parseInt(data.semesterAktif) || 1;
    
    document.getElementById('rombelSiswaName').textContent = data.nama;
    document.getElementById('rombelAngkatan').textContent = data.angkatan || '-';
    
    // Generate forms
    let html = '';
    for (let i = 1; i <= 6; i++) {
        const isActive = i === semesterAktif;
        html += `
            <div class="semester-form-item ${isActive ? 'active' : ''}">
                <div class="semester-header">
                    <span class="semester-label">Semester ${i}</span>
                    ${isActive ? '<span class="semester-badge">Aktif</span>' : ''}
                </div>
                <select class="semester-select" data-semester="${i}">
                    <option value="">-- Pilih Rombel --</option>
                    ${rombelOptions.map(r => `<option value="${r}" ${rombelData[i] === r ? 'selected' : ''}>${r}</option>`).join('')}
                </select>
            </div>
        `;
    }
    document.getElementById('rombelFormsGrid').innerHTML = html;
    
    openModal('modalRombel');
}

// Save Rombel All
document.getElementById('saveRombel')?.addEventListener('click', async function() {
    if (!currentRow) return;
    const nisn = currentRow.dataset.nisn;
    const selects = document.querySelectorAll('#rombelFormsGrid .semester-select');
    
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    this.disabled = true;
    
    for (const sel of selects) {
        await fetch('{{ route("admin.siswa.update-rombel") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ nisn, semester: sel.dataset.semester, nama_rombel: sel.value })
        });
    }
    
    this.innerHTML = '<i class="fas fa-save"></i> Simpan Semua';
    this.disabled = false;
    showToast('Rombel berhasil diperbarui');
    closeModal('modalRombel');
    location.reload();
});

// Open BK Modal
function openBKModal(row) {
    currentRow = row;
    const data = row.dataset;
    const bkData = JSON.parse(data.bk || '{}');
    const semesterAktif = parseInt(data.semesterAktif) || 1;
    
    document.getElementById('bkSiswaName').textContent = data.nama;
    document.getElementById('bkAngkatan').textContent = data.angkatan || '-';
    
    let html = '';
    for (let i = 1; i <= 6; i++) {
        const isActive = i === semesterAktif;
        html += `
            <div class="semester-form-item ${isActive ? 'active' : ''}" style="${isActive ? 'background: #d1fae5; border-color: #10b981;' : ''}">
                <div class="semester-header">
                    <span class="semester-label">Semester ${i}</span>
                    ${isActive ? '<span class="semester-badge" style="background: #10b981;">Aktif</span>' : ''}
                </div>
                <select class="semester-select" data-semester="${i}">
                    <option value="">-- Pilih Guru BK --</option>
                    ${bkOptions.map(b => `<option value="${b}" ${bkData[i] === b ? 'selected' : ''}>${b}</option>`).join('')}
                </select>
            </div>
        `;
    }
    document.getElementById('bkFormsGrid').innerHTML = html;
    
    openModal('modalBK');
}

// Save BK All
document.getElementById('saveBK')?.addEventListener('click', async function() {
    if (!currentRow) return;
    const nisn = currentRow.dataset.nisn;
    const selects = document.querySelectorAll('#bkFormsGrid .semester-select');
    
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    this.disabled = true;
    
    for (const sel of selects) {
        await fetch('{{ route("admin.siswa.update-bk") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ nisn, semester: sel.dataset.semester, nama_guru_bk: sel.value })
        });
    }
    
    this.innerHTML = '<i class="fas fa-save"></i> Simpan Semua';
    this.disabled = false;
    showToast('Guru BK berhasil diperbarui');
    closeModal('modalBK');
    location.reload();
});

// Close modal on backdrop click
document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
});

// Foto Modal Functions
function openFotoModal(nama, fotoUrl, siswaId) {
    const modal = document.getElementById('modalFoto');
    const img = document.getElementById('fotoFullImg');
    const container = document.getElementById('fotoContainer');
    const noPhotoDiv = document.getElementById('fotoNoPhoto');
    const initialsSpan = document.getElementById('fotoInitials');
    const namaEl = document.getElementById('fotoNama');
    const editLink = document.getElementById('fotoEditLink');
    
    namaEl.textContent = nama;
    editLink.href = '/admin/siswa/' + siswaId + '/edit';
    
    if (fotoUrl) {
        img.src = fotoUrl;
        container.style.display = 'inline-block';
        noPhotoDiv.style.display = 'none';
    } else {
        container.style.display = 'none';
        noPhotoDiv.style.display = 'flex';
        const initials = nama.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
        initialsSpan.textContent = initials;
    }
    
    openModal('modalFoto');
}
</script>
@endpush
