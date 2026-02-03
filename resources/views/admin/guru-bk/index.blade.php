@extends('layouts.app')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Manajemen Guru BK | SISMIK')

@push('styles')
<style>
    :root {
        --green-primary: #10b981;
        --green-dark: #059669;
    }
    
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 20px;
    }
    .header-left { display: flex; align-items: center; gap: 15px; }
    .header-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--green-primary), var(--green-dark));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    .header-text h1 { margin: 0; color: #065f46; font-size: 28px; font-weight: 700; }
    .header-text p { margin: 5px 0 0; color: var(--gray-500); font-size: 14px; }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 15px;
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
    .stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-content h3 { margin: 0; font-size: 28px; font-weight: 700; color: var(--gray-800); }
    .stat-content p { margin: 5px 0 0; color: var(--gray-500); font-size: 14px; }
    
    .action-buttons { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; margin-bottom: 24px; }
    
    .content-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .section-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    .section-header h2 { margin: 0; font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
    
    .search-box {
        position: relative;
        display: flex;
        align-items: center;
        background: white;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        padding: 0 12px;
        min-width: 300px;
    }
    .search-box i { color: var(--gray-400); font-size: 14px; margin-right: 8px; }
    .search-box input {
        border: none;
        outline: none;
        padding: 10px 0;
        font-size: 14px;
        background: transparent;
        width: 100%;
    }
    
    .modern-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .modern-table th {
        background: linear-gradient(0deg, var(--green-primary), var(--green-dark));
        color: white;
        padding: 14px 12px;
        font-weight: 600;
        text-align: center;
        font-size: 12px;
    }
    .modern-table td {
        padding: 12px;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }
    .modern-table tbody tr:hover { background: var(--gray-50); }
    
    .guru-info { display: flex; align-items: center; gap: 10px; }
    .guru-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid var(--gray-200);
        flex-shrink: 0;
        cursor: pointer;
        transition: all 0.3s;
    }
    .guru-avatar:hover { transform: scale(1.1); border-color: #10b981; }
    .guru-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .guru-avatar-placeholder {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, var(--green-primary), var(--green-dark));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
        border: 2px solid var(--gray-200);
    }
    .guru-name { font-weight: 600; color: var(--gray-800); font-size: 14px; }
    .guru-gender { font-size: 12px; color: var(--gray-400); margin-top: 2px; }
    
    .login-as-btn {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        cursor: pointer;
        text-decoration: none;
        background: rgba(6, 182, 212, 0.15);
        color: #06b6d4;
        transition: all 0.2s;
        margin-left: 8px;
    }
    .login-as-btn:hover { background: #06b6d4; color: white; }
    
    .badge-pill {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-aktif { background: rgba(16,185,129,0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
    .badge-nonaktif { background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }
    .badge-kepegawaian { background: rgba(16,185,129,0.1); color: #059669; font-family: monospace; }
    .badge-nip { background: var(--gray-100); color: var(--gray-600); font-family: monospace; }
    
    .action-btn-group { display: flex; justify-content: center; gap: 6px; }
    .action-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s;
    }
    .action-btn:hover { transform: translateY(-2px); }
    .btn-edit { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .btn-edit:hover { background: #3b82f6; color: white; }
    .btn-siswa { background: rgba(16,185,129,0.1); color: #10b981; }
    .btn-siswa:hover { background: #10b981; color: white; }
    .btn-aktivitas { background: rgba(139,92,246,0.1); color: #8b5cf6; }
    .btn-aktivitas:hover { background: #8b5cf6; color: white; }
    .btn-reset { background: rgba(245,158,11,0.1); color: #f59e0b; }
    .btn-reset:hover { background: #f59e0b; color: white; }
    .btn-hapus { background: rgba(239,68,68,0.1); color: #ef4444; }
    .btn-hapus:hover { background: #ef4444; color: white; }
    
    .empty-state { padding: 60px 20px; text-align: center; color: var(--gray-500); }
    .empty-state i { font-size: 48px; color: var(--gray-300); margin-bottom: 15px; }
    
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
    
    /* Pagination Styling */
    .pagination-wrapper nav { display: flex; }
    .pagination-wrapper nav > div:first-child { display: none; }
    .pagination-wrapper nav > div:last-child,
    .pagination-wrapper nav > div:last-child > span {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center;
        gap: 4px;
    }
    .pagination-wrapper nav span[aria-current="page"] span,
    .pagination-wrapper nav a {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
    }
    .pagination-wrapper nav span[aria-current="page"] span {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    .pagination-wrapper nav a {
        background: white;
        color: var(--gray-600);
        border: 1px solid var(--gray-300);
    }
    .pagination-wrapper nav a:hover {
        background: var(--gray-100);
        border-color: var(--gray-400);
    }
    .pagination-wrapper nav span[aria-disabled="true"] span {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        border-radius: 8px;
        font-size: 13px;
        background: var(--gray-100);
        color: var(--gray-400);
        border: 1px solid var(--gray-200);
        cursor: not-allowed;
    }
    .pagination-wrapper nav svg {
        width: 16px !important;
        height: 16px !important;
    }
    
    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(5px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .modal-box {
        background: white;
        border-radius: 16px;
        width: 420px;
        max-width: 90%;
        overflow: hidden;
        box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        animation: slideIn 0.3s ease;
    }
    @keyframes slideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
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
                <div class="header-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="header-text">
                    <h1>Manajemen Guru BK</h1>
                    <p>
                        Data Guru Bimbingan dan Konseling SMAN 1 Seputih Raman
                        @if($search)
                            - Hasil pencarian: "{{ $search }}"
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="stat-card" style="border-left: 4px solid var(--green-primary);">
                <div class="stat-icon green">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $guruBKList->total() }}</h3>
                    <p>Total Guru BK</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $totalAktif }}</h3>
                    <p>Guru BK Aktif</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $totalNonaktif }}</h3>
                    <p>Guru BK Nonaktif</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('admin.guru-bk.create') }}" class="btn" style="background: var(--green-primary); color: white;">
                <i class="fas fa-plus"></i> Tambah Guru BK
            </a>
            <a href="#" class="btn btn-secondary" style="font-size: 12px;">
                <i class="fas fa-file-export"></i> Export Data
            </a>
        </div>

        <!-- Table Section -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-list"></i> Daftar Guru BK</h2>
                <form method="GET" action="{{ route('admin.guru-bk.index') }}">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau NIP...">
                        @if($search)
                            <a href="{{ route('admin.guru-bk.index') }}" style="color: var(--gray-400); font-size: 14px;">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div style="overflow-x: auto;">
                @if($guruBKList->count() > 0)
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th style="text-align: left;">Guru BK</th>
                                <th>NIP</th>
                                <th>Status Kepegawaian</th>
                                <th>Golongan</th>
                                <th>Status</th>
                                <th width="200">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($guruBKList as $index => $guruBK)
                                <tr>
                                    <td class="text-center">{{ $guruBKList->firstItem() + $index }}</td>
                                    <td>
                                        <div class="guru-info">
                                            @if($guruBK->foto && Storage::disk('public')->exists('guru_bk/' . $guruBK->foto))
                                                <div class="guru-avatar" onclick="showFotoModal('{{ asset('storage/guru_bk/' . $guruBK->foto) }}', '{{ $guruBK->nama }}')">
                                                    <img src="{{ asset('storage/guru_bk/' . $guruBK->foto) }}" alt="{{ $guruBK->nama }}">
                                                </div>
                                            @else
                                                <div class="guru-avatar-placeholder">
                                                    <i class="fas fa-user-graduate"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <span class="guru-name">{{ $guruBK->nama }}</span>
                                                    <a href="javascript:void(0)" 
                                                       onclick="confirmLoginAs({{ $guruBK->id }}, '{{ $guruBK->nama }}', '{{ $guruBK->nip ?? '-' }}')"
                                                       class="login-as-btn" title="Login sebagai Guru BK">
                                                        <i class="fas fa-sign-in-alt"></i>
                                                    </a>
                                                </div>
                                                <div class="guru-gender">
                                                    {{ $guruBK->jenis_kelamin == 'L' ? 'Laki-laki' : ($guruBK->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-pill badge-nip">{{ $guruBK->nip ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-pill badge-kepegawaian">{{ $guruBK->status_kepegawaian ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-pill badge-kepegawaian">{{ $guruBK->golongan ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-pill {{ ($guruBK->status ?? 'Aktif') == 'Aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                                            {{ $guruBK->status ?? 'Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-btn-group">
                                            <a href="{{ route('admin.guru-bk.edit', $guruBK->id) }}" class="action-btn btn-edit" title="Edit Data Guru BK">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.guru-bk.siswa-bimbingan', $guruBK->id) }}" class="action-btn btn-siswa" title="Siswa Bimbingan">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            <a href="{{ route('admin.guru-bk.aktivitas', $guruBK->id) }}" class="action-btn btn-aktivitas" title="Aktivitas Bimbingan">
                                                <i class="fas fa-chart-line"></i>
                                            </a>
                                            <form action="{{ route('admin.guru-bk.reset-password', $guruBK->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="action-btn btn-reset" title="Reset Password" onclick="return confirm('Reset password guru BK ini ke NIP/Username?')">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                            </form>
                                            <button onclick="confirmDelete({{ $guruBK->id }})" class="action-btn btn-hapus" title="Hapus Guru BK">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <i class="fas fa-user-graduate"></i>
                        <h3>Belum Ada Data Guru BK</h3>
                        <p>Silakan tambah guru BK baru untuk memulai</p>
                        <a href="{{ route('admin.guru-bk.create') }}" class="btn" style="background: var(--green-primary); color: white; margin-top: 15px;">
                            <i class="fas fa-plus"></i> Tambah Guru BK Pertama
                        </a>
                    </div>
                @endif
            </div>

            @if($guruBKList->hasPages())
                <div class="pagination-wrapper">
                    <span style="font-size: 13px; color: var(--gray-600);">
                        Menampilkan {{ $guruBKList->firstItem() }}-{{ $guruBKList->lastItem() }} dari {{ $guruBKList->total() }} data
                    </span>
                    {{ $guruBKList->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div id="modalDelete" class="modal-overlay">
    <div class="modal-box" style="max-width: 400px;">
        <div style="padding: 30px; text-align: center;">
            <div style="font-size: 48px; color: #f59e0b; margin-bottom: 15px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 style="margin: 0 0 10px; color: var(--gray-800); font-size: 20px;">Konfirmasi Hapus</h3>
            <p style="margin: 0 0 10px; color: var(--gray-600); font-size: 14px;">Apakah yakin Anda akan menghapus data guru BK ini?</p>
            <p style="margin: 0 0 20px; color: var(--gray-500); font-size: 12px;">Penghapusan akan mempengaruhi data konseling yang terkait.</p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button onclick="closeDeleteModal()" style="flex: 1; background: var(--gray-500); color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600;">
                    Batal
                </button>
                <form id="deleteForm" method="POST" style="flex: 1;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="width: 100%; background: #ef4444; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600;">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Login As Guru BK -->
<div id="modalLoginAs" class="modal-overlay">
    <div class="modal-box">
        <div style="background: linear-gradient(135deg, #10b981, #059669); padding: 25px 20px; text-align: center;">
            <div style="width: 70px; height: 70px; background: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-graduate" style="font-size: 32px; color: white;"></i>
            </div>
            <h3 style="margin: 0; color: white; font-size: 18px; font-weight: 700;">Login sebagai Guru BK</h3>
        </div>
        <div style="padding: 24px;">
            <p style="margin: 0 0 8px; color: #64748b; font-size: 14px; text-align: center;">Anda akan masuk ke akun:</p>
            <div style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #a7f3d0; border-radius: 12px; padding: 15px; margin: 15px 0; display: flex; align-items: center; gap: 15px;">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div style="text-align: left; flex: 1;">
                    <div id="loginAsNama" style="font-weight: 600; color: #065f46; font-size: 16px;">Nama Guru BK</div>
                    <div id="loginAsNip" style="font-size: 12px; color: #10b981; font-family: monospace;">NIP: -</div>
                </div>
            </div>
            <div style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 10px; padding: 12px 15px; display: flex; align-items: flex-start; gap: 10px; text-align: left;">
                <i class="fas fa-info-circle" style="color: #f59e0b; font-size: 16px; margin-top: 2px;"></i>
                <p style="margin: 0; color: #92400e; font-size: 12px; line-height: 1.5;">
                    Sesi admin Anda akan disimpan. Anda dapat kembali ke akun admin setelah selesai.
                </p>
            </div>
            <div style="display: flex; gap: 12px; justify-content: center; margin-top: 20px;">
                <button onclick="closeLoginAsModal()" style="flex: 1; background: #f1f5f9; color: #64748b; padding: 12px 20px; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer; font-size: 14px; font-weight: 600;">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button id="btnConfirmLoginAs" style="flex: 1; background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 12px 20px; border: none; border-radius: 10px; cursor: pointer; font-size: 14px; font-weight: 600;">
                    <i class="fas fa-sign-in-alt"></i> Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Foto -->
<div id="modalFoto" class="modal-overlay" style="cursor: zoom-out;">
    <div style="position: relative; max-width: 90%; max-height: 90%;">
        <button onclick="closeFotoModal()" style="position: absolute; top: -40px; right: 0; width: 36px; height: 36px; background: rgba(255,255,255,0.2); border: none; border-radius: 50%; cursor: pointer; color: white; font-size: 18px;">
            <i class="fas fa-times"></i>
        </button>
        <div style="background: white; padding: 8px; border-radius: 16px;">
            <img id="modalFotoImg" src="" alt="Foto Guru BK" style="max-width: 500px; max-height: 70vh; border-radius: 12px; display: block;">
        </div>
        <div style="margin-top: 15px; background: rgba(255,255,255,0.15); padding: 12px 20px; border-radius: 10px; display: flex; align-items: center; gap: 15px;">
            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-graduate" style="color: white;"></i>
            </div>
            <div>
                <div id="modalFotoNama" style="color: white; font-weight: 600; font-size: 16px;">Nama Guru BK</div>
                <div style="color: rgba(255,255,255,0.7); font-size: 12px;">Foto Profil Guru BK</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deleteId = 0;
let loginAsId = 0;

// Delete Modal
function confirmDelete(id) {
    deleteId = id;
    document.getElementById('deleteForm').action = '{{ url("/admin/guru-bk") }}/' + id;
    document.getElementById('modalDelete').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('modalDelete').style.display = 'none';
}

// Login As Modal
function confirmLoginAs(id, nama, nip) {
    loginAsId = id;
    document.getElementById('loginAsNama').textContent = nama;
    document.getElementById('loginAsNip').textContent = 'NIP: ' + nip;
    document.getElementById('modalLoginAs').style.display = 'flex';
}

function closeLoginAsModal() {
    document.getElementById('modalLoginAs').style.display = 'none';
}

document.getElementById('btnConfirmLoginAs')?.addEventListener('click', function() {
    window.location.href = '{{ url("/admin/impersonate/guru-bk") }}/' + loginAsId;
});

// Foto Modal
function showFotoModal(fotoPath, nama) {
    document.getElementById('modalFotoImg').src = fotoPath;
    document.getElementById('modalFotoNama').textContent = nama;
    document.getElementById('modalFoto').style.display = 'flex';
}

function closeFotoModal() {
    document.getElementById('modalFoto').style.display = 'none';
}

// Close modals on backdrop click
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});

// ESC to close modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
    }
});
</script>
@endpush
