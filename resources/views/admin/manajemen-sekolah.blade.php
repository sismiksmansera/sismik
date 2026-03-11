@extends('layouts.app')

@section('title', 'Manajemen Sekolah | SISMIK')

@push('styles')
<style>
    .content-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 20px;
    }
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
    
    .content-section {
        background: var(--white);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--gray-200);
    }
    .section-header h2 {
        font-size: 18px;
        font-weight: 600;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-header h2 i { color: var(--primary); }
    .header-actions { display: flex; gap: 10px; align-items: center; }
    
    .badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: white;
    }
    
    /* Table Styles */
    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }
    .modern-table th {
        background: var(--gray-100);
        padding: 14px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .modern-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }
    .modern-table tbody tr:hover {
        background: var(--gray-50);
    }
    
    .semester-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(16, 185, 129, 0.15);
        color: var(--primary);
    }
    
    .action-buttons-small {
        display: flex;
        gap: 8px;
        justify-content: center;
    }
    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
        text-decoration: none;
        color: white;
    }
    .btn-action:hover { transform: translateY(-2px); }
    .btn-warning { background: linear-gradient(135deg, #F59E0B, #D97706); }
    .btn-success { background: linear-gradient(135deg, #10B981, #059669); }
    .btn-danger { background: linear-gradient(135deg, #EF4444, #DC2626); }
    .btn-info { background: linear-gradient(135deg, #3B82F6, #2563EB); }
    
    /* Toggle Switch */
    .modern-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }
    .modern-switch input { display: none; }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--gray-300);
        transition: 0.3s;
        border-radius: 26px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }
    .modern-switch input:checked + .slider { background-color: var(--primary); }
    .modern-switch input:checked + .slider:before { transform: translateX(24px); }
    
    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1050;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-content {
        background: white;
        border-radius: 20px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
    }
    @keyframes slideIn {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .modal-header {
        padding: 24px;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header h3 { font-size: 20px; font-weight: 600; }
    .modal-close {
        width: 36px;
        height: 36px;
        border: none;
        background: var(--gray-100);
        border-radius: 10px;
        cursor: pointer;
        font-size: 18px;
    }
    .modal-body { padding: 24px; }
    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--gray-200);
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    
    .form-group { margin-bottom: 20px; }
    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--gray-700);
    }
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--gray-200);
        border-radius: 10px;
        font-size: 14px;
        transition: border-color 0.3s;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary);
    }
    .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--gray-200);
        border-radius: 10px;
        font-size: 14px;
        background: white;
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <div class="header-icon">
                <i class="fas fa-school"></i>
            </div>
            <div class="header-text">
                <h1>Manajemen Sekolah</h1>
                <p>Kelola data periodik & admin sekolah</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Data Periodik Section -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-calendar-alt"></i> Data Periodik</h2>
                <div class="header-actions">
                    <span class="badge" style="background: var(--primary);">{{ $periodikList->count() }} Data</span>
                    <button class="btn btn-primary" onclick="openModal('modalPeriodik')">
                        <i class="fas fa-plus-circle"></i> Tambah Periode
                    </button>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="text-center" width="60">No</th>
                            <th>Tahun Pelajaran</th>
                            <th>Semester</th>
                            <th>Kepala Sekolah</th>
                            <th class="text-center" width="120">Status</th>
                            <th class="text-center" width="250">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periodikList as $index => $periodik)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-calendar" style="color: var(--primary);"></i>
                                    {{ $periodik->tahun_pelajaran }}
                                </div>
                            </td>
                            <td><span class="semester-badge">{{ $periodik->semester }}</span></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-user-tie" style="color: var(--gray-500);"></i>
                                    {{ $periodik->nama_kepala }}
                                </div>
                            </td>
                            <td class="text-center">
                                <label class="modern-switch">
                                    <input type="checkbox" class="toggle-aktif" 
                                        data-id="{{ $periodik->id }}" 
                                        {{ $periodik->aktif === 'Ya' ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons-small">
                                    <button class="btn-action btn-warning" title="Edit" 
                                        onclick="editPeriodik({{ json_encode($periodik) }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action btn-success" title="Pengaturan Raport" 
                                        onclick="openRaportSettings({{ json_encode($periodik) }})">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <button class="btn-action btn-info" title="Jam Pelajaran" 
                                        onclick="openJamPelajaran({{ json_encode($periodik) }})">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                    <button class="btn-action" title="Setting Hari Non Efektif KBM" 
                                        onclick="openHariEfektifModal()" style="background: #f59e0b; color: white;">
                                        <i class="fas fa-calendar-times"></i>
                                    </button>
                                    <form action="{{ route('admin.periodik.destroy', $periodik->id) }}" 
                                        method="POST" style="display:inline;" 
                                        onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 40px; color: var(--gray-500);">
                                <i class="fas fa-inbox" style="font-size: 40px; margin-bottom: 10px; display: block;"></i>
                                Belum ada data periodik
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Data Admin Section -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-users-cog"></i> Data Admin Sekolah</h2>
                <div class="header-actions">
                    <span class="badge" style="background: var(--info);">{{ $adminList->count() }} Admin</span>
                    <button class="btn btn-primary" onclick="openModal('modalAdmin')">
                        <i class="fas fa-user-plus"></i> Tambah Admin
                    </button>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="text-center" width="60">No</th>
                            <th>Nama Admin</th>
                            <th>Username</th>
                            <th class="text-center" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adminList as $index => $adm)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-user" style="color: var(--primary);"></i>
                                    {{ $adm->nama }}
                                </div>
                            </td>
                            <td><span class="semester-badge" style="background: rgba(59, 130, 246, 0.15); color: var(--info);">{{ $adm->username }}</span></td>
                            <td class="text-center">
                                <div class="action-buttons-small">
                                    <button class="btn-action btn-warning" title="Edit" 
                                        onclick="editAdmin({{ json_encode($adm) }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 40px; color: var(--gray-500);">
                                <i class="fas fa-inbox" style="font-size: 40px; margin-bottom: 10px; display: block;"></i>
                                Belum ada data admin
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


    </div>
</div>


<!-- Modal Tambah Periodik -->
<div class="modal-overlay" id="modalPeriodik">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="periodikModalTitle">Tambah Data Periodik</h3>
            <button class="modal-close" onclick="closeModal('modalPeriodik')">&times;</button>
        </div>
        <form id="formPeriodik" action="{{ route('admin.periodik.store') }}" method="POST">
            @csrf
            <div id="periodikMethodField"></div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Tahun Pelajaran</label>
                    <input type="text" name="tahun_pelajaran" class="form-control" 
                        placeholder="Contoh: 2024/2025" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-select" required>
                        <option value="">Pilih Semester</option>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Kepala Sekolah</label>
                    <input type="text" name="nama_kepala" class="form-control" 
                        placeholder="Nama lengkap kepala sekolah" required>
                </div>
                <div class="form-group">
                    <label class="form-label">NIP Kepala Sekolah</label>
                    <input type="text" name="nip_kepala" class="form-control" 
                        placeholder="NIP kepala sekolah" required>
                </div>
                
                <hr style="margin: 24px 0;">
                <h4 style="margin-bottom: 16px; color: var(--gray-700);">Wakil Kepala Sekolah</h4>
                
                <div class="form-group">
                    <label class="form-label">Waka Kurikulum</label>
                    <select name="waka_kurikulum" class="form-select">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->nama }}">{{ $guru->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Waka Kesiswaan</label>
                    <select name="waka_kesiswaan" class="form-select">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->nama }}">{{ $guru->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Waka Sarpras</label>
                    <select name="waka_sarpras" class="form-select">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->nama }}">{{ $guru->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Waka Humas</label>
                    <select name="waka_humas" class="form-select">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->nama }}">{{ $guru->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalPeriodik')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Admin -->
<div class="modal-overlay" id="modalAdmin">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="adminModalTitle">Tambah Admin</h3>
            <button class="modal-close" onclick="closeModal('modalAdmin')">&times;</button>
        </div>
        <form id="formAdmin" action="{{ route('admin.admin-sekolah.store') }}" method="POST">
            @csrf
            <div id="adminMethodField"></div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama lengkap admin" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Username untuk login" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Password minimal 6 karakter">
                    <small style="color: var(--gray-500);" id="passwordHint">Minimal 6 karakter</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAdmin')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Raport Settings -->
<div class="modal-overlay" id="modalRaport">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3 id="raportModalTitle">Pengaturan Raport</h3>
            <button class="modal-close" onclick="closeModal('modalRaport')">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="raportPeriodikId">
            <div class="form-group">
                <label class="form-label">Tanggal Bagi Raport</label>
                <input type="date" id="tanggal_bagi_raport" class="form-control">
            </div>
            
            <h4 style="margin: 20px 0 15px; color: var(--gray-700);">Lock Settings</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <label class="modern-switch">
                        <input type="checkbox" id="lock_print_raport">
                        <span class="slider"></span>
                    </label>
                    <span>Lock Print Raport (Guru)</span>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <label class="modern-switch">
                        <input type="checkbox" id="lock_print_raport_all">
                        <span class="slider"></span>
                    </label>
                    <span>Lock Print Raport (Semua)</span>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <label class="modern-switch">
                        <input type="checkbox" id="lock_print_riwayat_guru">
                        <span class="slider"></span>
                    </label>
                    <span>Lock Print Riwayat (Guru)</span>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <label class="modern-switch">
                        <input type="checkbox" id="lock_print_riwayat_all">
                        <span class="slider"></span>
                    </label>
                    <span>Lock Print Riwayat (Semua)</span>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <label class="modern-switch">
                        <input type="checkbox" id="lock_print_leger_nilai">
                        <span class="slider"></span>
                    </label>
                    <span>Lock Print Leger Nilai</span>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <label class="modern-switch">
                        <input type="checkbox" id="lock_print_leger_katrol">
                        <span class="slider"></span>
                    </label>
                    <span>Lock Print Leger Katrol</span>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <label class="modern-switch">
                        <input type="checkbox" id="lock_nilai_minmax">
                        <span class="slider"></span>
                    </label>
                    <span>Lock Nilai MinMax</span>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <label class="modern-switch">
                        <input type="checkbox" id="lock_katrol_nilai">
                        <span class="slider"></span>
                    </label>
                    <span>Lock Katrol Nilai</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('modalRaport')">Batal</button>
            <button type="button" class="btn btn-primary" onclick="saveRaportSettings()">
                <i class="fas fa-save"></i> Simpan Pengaturan
            </button>
        </div>
    </div>
</div>

<!-- Modal Jam Pelajaran -->
<div class="modal-overlay" id="modalJamPelajaran">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3 id="jpModalTitle">Pengaturan Jam Pelajaran</h3>
            <button class="modal-close" onclick="closeModal('modalJamPelajaran')">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="jpPeriodikId">
            <div style="max-height: 400px; overflow-y: auto;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th width="80">JP</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 1; $i <= 11; $i++)
                        <tr>
                            <td class="text-center"><strong>JP {{ $i }}</strong></td>
                            <td><input type="time" id="jp_{{ $i }}_mulai" class="form-control"></td>
                            <td><input type="time" id="jp_{{ $i }}_selesai" class="form-control"></td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('modalJamPelajaran')">Batal</button>
            <button type="button" class="btn btn-primary" onclick="saveJamPelajaran()">
                <i class="fas fa-save"></i> Simpan Jam Pelajaran
            </button>
        </div>
</div>
</div>

<!-- Modal Setting Hari Non Efektif KBM -->
<div class="modal-overlay" id="modalHariEfektif">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3><i class="fas fa-calendar-times" style="color: var(--primary);"></i> Setting Hari Non Efektif KBM</h3>
            <button class="modal-close" onclick="closeModal('modalHariEfektif')">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Daftar tanggal non efektif -->
            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h4 style="margin: 0; font-size: 14px; font-weight: 600; color: #374151;">Daftar Hari Non Efektif KBM</h4>
                    <span id="heCount" style="background: #f59e0b; color: white; padding: 2px 10px; border-radius: 10px; font-size: 11px; font-weight: 600;">{{ $hariEfektifList->count() }} data</span>
                </div>
                <div id="hariEfektifListContainer" style="max-height: 280px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 10px;">
                    @if($hariEfektifList->isEmpty())
                    <div id="heEmptyState" style="text-align: center; padding: 30px 20px; color: #9ca3af;">
                        <i class="fas fa-calendar-check" style="font-size: 32px; margin-bottom: 8px; display: block; color: #d1d5db;"></i>
                        <p style="margin: 0; font-size: 13px;">Belum ada data. Semua hari dianggap efektif.</p>
                    </div>
                    @else
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e5e7eb; position: sticky; top: 0;">
                                <th style="padding: 8px 12px; text-align: left; font-weight: 600; color: #374151;">Tanggal</th>
                                <th style="padding: 8px 12px; text-align: left; font-weight: 600; color: #374151;">Status</th>
                                <th style="padding: 8px 12px; text-align: left; font-weight: 600; color: #374151;">Keterangan</th>
                                <th style="padding: 8px 12px; text-align: center; font-weight: 600; color: #374151; width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="heTableBody">
                            @foreach($hariEfektifList->sortBy('tanggal') as $he)
                            <tr style="border-bottom: 1px solid #f1f5f9;" id="he-row-{{ $he->tanggal }}">
                                <td style="padding: 8px 12px; font-weight: 500; font-size: 12px;">
                                    {{ \Carbon\Carbon::parse($he->tanggal)->isoFormat('ddd, D MMM Y') }}
                                </td>
                                <td style="padding: 8px 12px;">
                                    @if($he->status === 'Libur')
                                    <span style="background: #ef4444; color: white; padding: 2px 8px; border-radius: 8px; font-size: 10px; font-weight: 600;">Libur</span>
                                    @else
                                    <span style="background: #f59e0b; color: white; padding: 2px 8px; border-radius: 8px; font-size: 10px; font-weight: 600;">Non-KBM</span>
                                    @endif
                                </td>
                                <td style="padding: 8px 12px; color: #4b5563; font-size: 12px;">{{ $he->keterangan }}</td>
                                <td style="padding: 8px 12px; text-align: center;">
                                    <button onclick="deleteHariEfektif('{{ $he->tanggal }}')" style="background: none; border: none; cursor: pointer; color: #ef4444; font-size: 13px;" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>

            <!-- Form Tambah -->
            <div style="border-top: 2px solid #e5e7eb; padding-top: 16px;">
                <h4 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 600; color: #374151; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-plus-circle" style="color: #3b82f6;"></i> Tambah Tanggal Non Efektif KBM
                </h4>
                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 13px;">Tanggal <small style="color: #9ca3af;">(bisa input beberapa tanggal sekaligus)</small></label>
                    <div id="heDateInputs">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                            <input type="date" class="form-control he-date-input" style="flex: 1; padding: 8px 12px;">
                        </div>
                    </div>
                    <button type="button" onclick="addDateInput()" style="background: none; border: 1px dashed #d1d5db; color: #6b7280; padding: 6px 12px; border-radius: 8px; cursor: pointer; font-size: 11px; width: 100%; margin-top: 4px; transition: all 0.2s;" onmouseover="this.style.borderColor='#3b82f6'; this.style.color='#3b82f6';" onmouseout="this.style.borderColor='#d1d5db'; this.style.color='#6b7280';">
                        <i class="fas fa-plus"></i> Tambah Tanggal Lainnya
                    </button>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-size: 13px;">Status</label>
                        <select id="heStatus" class="form-select" style="padding: 8px 12px;">
                            <option value="Libur">Libur</option>
                            <option value="Non-KBM">Non-KBM</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-size: 13px;">Keterangan Kegiatan</label>
                        <input type="text" id="heKeterangan" class="form-control" placeholder="Contoh: Hari Raya Idul Fitri" style="padding: 8px 12px;">
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer" style="justify-content: space-between;">
            <button type="button" class="btn btn-secondary" onclick="closeModal('modalHariEfektif')">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="saveHariEfektif()" id="heSaveBtn">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </div>
</div>

<!-- Modal Maintenance Mode -->
<div class="modal-overlay" id="modalMaintenance">
    <div class="modal-content" style="max-width: 550px;">
        <div class="modal-header">
            <h3><i class="fas fa-tools" style="color: #f59e0b;"></i> Pengaturan Maintenance Mode</h3>
            <button class="modal-close" onclick="closeModal('modalMaintenance')">&times;</button>
        </div>
        <div class="modal-body">
            <div style="background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1px solid #fde68a; border-radius: 12px; padding: 16px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 18px;"></i>
                    <strong style="color: #92400e;">Perhatian</strong>
                </div>
                <p style="color: #78350f; font-size: 13px; margin: 0;">Saat maintenance mode aktif, semua pengguna (guru, guru BK, siswa) tidak dapat mengakses sistem. Hanya admin yang tetap bisa login.</p>
            </div>

            <div class="form-group">
                <label class="form-label">Status Maintenance</label>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <label class="toggle-switch" style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" id="maintenanceToggle" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: #d1d5db; border-radius: 28px; transition: 0.3s;"></span>
                    </label>
                    <span id="maintenanceStatusLabel" style="font-weight: 600; font-size: 14px; color: #6b7280;">Non-Aktif</span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Pesan Maintenance</label>
                <textarea id="maintenanceMessage" class="form-control" rows="3" placeholder="Tulis pesan yang akan ditampilkan kepada pengguna..." style="resize: vertical;">Sistem sedang dalam pemeliharaan. Silakan kembali beberapa saat lagi.</textarea>
            </div>

            <div id="maintenancePreviewLink" style="display: none; margin-top: 10px;">
                <a href="/" target="_blank" style="color: #6366f1; font-size: 13px; text-decoration: none;">
                    <i class="fas fa-external-link-alt"></i> Lihat halaman maintenance
                </a>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('modalMaintenance')">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="saveMaintenanceSettings()">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Modal Functions
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    // Edit Periodik
    function editPeriodik(data) {
        const form = document.getElementById('formPeriodik');
        form.action = '/admin/periodik/' + data.id;
        document.getElementById('periodikMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('periodikModalTitle').textContent = 'Edit Data Periodik';
        
        form.querySelector('[name="tahun_pelajaran"]').value = data.tahun_pelajaran;
        form.querySelector('[name="semester"]').value = data.semester;
        form.querySelector('[name="nama_kepala"]').value = data.nama_kepala;
        form.querySelector('[name="nip_kepala"]').value = data.nip_kepala;
        form.querySelector('[name="waka_kurikulum"]').value = data.waka_kurikulum || '';
        form.querySelector('[name="waka_kesiswaan"]').value = data.waka_kesiswaan || '';
        form.querySelector('[name="waka_sarpras"]').value = data.waka_sarpras || '';
        form.querySelector('[name="waka_humas"]').value = data.waka_humas || '';
        
        openModal('modalPeriodik');
    }

    // Edit Admin
    function editAdmin(data) {
        const form = document.getElementById('formAdmin');
        form.action = '/admin/admin-sekolah/' + data.id;
        document.getElementById('adminMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('adminModalTitle').textContent = 'Edit Admin';
        document.getElementById('passwordHint').textContent = 'Kosongkan jika tidak ingin mengubah password';
        
        form.querySelector('[name="nama"]').value = data.nama;
        form.querySelector('[name="username"]').value = data.username;
        form.querySelector('[name="password"]').removeAttribute('required');
        
        openModal('modalAdmin');
    }

    // Open Raport Settings Modal
    function openRaportSettings(data) {
        document.getElementById('raportPeriodikId').value = data.id;
        document.getElementById('raportModalTitle').textContent = 'Pengaturan Raport - ' + data.tahun_pelajaran + ' ' + data.semester;
        
        // Set values from data
        document.getElementById('tanggal_bagi_raport').value = data.tanggal_bagi_raport || '';
        document.getElementById('lock_print_raport').checked = data.lock_print_raport === 'Ya';
        document.getElementById('lock_print_raport_all').checked = data.lock_print_raport_all === 'Ya';
        document.getElementById('lock_print_riwayat_guru').checked = data.lock_print_riwayat_guru === 'Ya';
        document.getElementById('lock_print_riwayat_all').checked = data.lock_print_riwayat_all === 'Ya';
        document.getElementById('lock_print_leger_nilai').checked = data.lock_print_leger_nilai === 'Ya';
        document.getElementById('lock_print_leger_katrol').checked = data.lock_print_leger_katrol === 'Ya';
        document.getElementById('lock_nilai_minmax').checked = data.lock_nilai_minmax === 'Ya';
        document.getElementById('lock_katrol_nilai').checked = data.lock_katrol_nilai === 'Ya';
        
        openModal('modalRaport');
    }

    // Save Raport Settings
    function saveRaportSettings() {
        const periodikId = document.getElementById('raportPeriodikId').value;
        const data = {
            periodik_id: periodikId,
            tanggal_bagi_raport: document.getElementById('tanggal_bagi_raport').value,
            lock_print_raport: document.getElementById('lock_print_raport').checked ? 'Ya' : 'Tidak',
            lock_print_raport_all: document.getElementById('lock_print_raport_all').checked ? 'Ya' : 'Tidak',
            lock_print_riwayat_guru: document.getElementById('lock_print_riwayat_guru').checked ? 'Ya' : 'Tidak',
            lock_print_riwayat_all: document.getElementById('lock_print_riwayat_all').checked ? 'Ya' : 'Tidak',
            lock_print_leger_nilai: document.getElementById('lock_print_leger_nilai').checked ? 'Ya' : 'Tidak',
            lock_print_leger_katrol: document.getElementById('lock_print_leger_katrol').checked ? 'Ya' : 'Tidak',
            lock_nilai_minmax: document.getElementById('lock_nilai_minmax').checked ? 'Ya' : 'Tidak',
            lock_katrol_nilai: document.getElementById('lock_katrol_nilai').checked ? 'Ya' : 'Tidak',
        };

        fetch('{{ route("admin.raport-settings.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert('Pengaturan raport berhasil disimpan!');
                closeModal('modalRaport');
                location.reload();
            } else {
                alert('Gagal menyimpan: ' + result.message);
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
        });
    }

    // Open Jam Pelajaran Modal
    function openJamPelajaran(data) {
        document.getElementById('jpPeriodikId').value = data.id;
        document.getElementById('jpModalTitle').textContent = 'Jam Pelajaran - ' + data.tahun_pelajaran + ' ' + data.semester;
        
        // Clear all inputs first
        for (let i = 1; i <= 11; i++) {
            document.getElementById('jp_' + i + '_mulai').value = '';
            document.getElementById('jp_' + i + '_selesai').value = '';
        }
        
        // Fetch existing data
        fetch('{{ route("admin.jam-pelajaran.get") }}?periodik_id=' + data.id)
        .then(res => res.json())
        .then(result => {
            if (result.success && result.data) {
                for (let i = 1; i <= 11; i++) {
                    const mulai = result.data['jp_' + i + '_mulai'];
                    const selesai = result.data['jp_' + i + '_selesai'];
                    if (mulai) document.getElementById('jp_' + i + '_mulai').value = mulai;
                    if (selesai) document.getElementById('jp_' + i + '_selesai').value = selesai;
                }
            }
        });
        
        openModal('modalJamPelajaran');
    }

    // Save Jam Pelajaran
    function saveJamPelajaran() {
        const periodikId = document.getElementById('jpPeriodikId').value;
        const data = { periodik_id: periodikId };
        
        for (let i = 1; i <= 11; i++) {
            data['jp_' + i + '_mulai'] = document.getElementById('jp_' + i + '_mulai').value;
            data['jp_' + i + '_selesai'] = document.getElementById('jp_' + i + '_selesai').value;
        }

        fetch('{{ route("admin.jam-pelajaran.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert('Jam pelajaran berhasil disimpan!');
                closeModal('modalJamPelajaran');
            } else {
                alert('Gagal menyimpan: ' + result.message);
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
        });
    }

    // Toggle Aktif
    document.querySelectorAll('.toggle-aktif').forEach(el => {
        el.addEventListener('change', function() {
            const id = this.dataset.id;
            const aktif = this.checked ? 'Ya' : 'Tidak';
            
            fetch('{{ route("admin.periodik.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id, aktif })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        });
    });

    // Close modal when clicking outside
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });



    // ========== RESTORE STORAGE FUNCTIONS ==========
    function updateRestoreFileName(input) {
        const label = document.getElementById('restoreFileName');
        const btn = document.getElementById('restoreBtn');
        if (input.files && input.files.length > 0) {
            const file = input.files[0];
            const sizeMB = (file.size / 1024 / 1024).toFixed(1);
            label.innerHTML = '<strong>' + file.name + '</strong> (' + sizeMB + ' MB)';
            label.style.color = '#059669';
            btn.disabled = false;
            btn.style.opacity = '1';
        } else {
            label.textContent = 'Klik untuk pilih file .zip';
            label.style.color = '#64748b';
            btn.disabled = true;
            btn.style.opacity = '0.5';
        }
    }

    function confirmRestore(form) {
        if (!confirm('Yakin ingin restore file upload? File yang sama akan ditimpa.')) {
            return false;
        }
        const btn = document.getElementById('restoreBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        btn.disabled = true;
        return true;
    }
    // ========== HARI EFEKTIF FUNCTIONS ==========
    function openHariEfektifModal() {
        // Reset form fields
        document.getElementById('heDateInputs').innerHTML = `
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <input type="date" class="form-control he-date-input" style="flex: 1; padding: 8px 12px;">
            </div>
        `;
        document.getElementById('heStatus').value = 'Libur';
        document.getElementById('heKeterangan').value = '';
        openModal('modalHariEfektif');
    }

    function addDateInput() {
        const container = document.getElementById('heDateInputs');
        const row = document.createElement('div');
        row.style.cssText = 'display: flex; align-items: center; gap: 8px; margin-bottom: 6px;';
        row.innerHTML = `
            <input type="date" class="form-control he-date-input" style="flex: 1; padding: 8px 12px;">
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; color: #ef4444; font-size: 16px; padding: 4px;" title="Hapus">
                <i class="fas fa-times-circle"></i>
            </button>
        `;
        container.appendChild(row);
    }

    function saveHariEfektif() {
        const dateInputs = document.querySelectorAll('.he-date-input');
        const tanggal = [];
        dateInputs.forEach(input => {
            if (input.value) tanggal.push(input.value);
        });
        
        if (tanggal.length === 0) { alert('Harap isi minimal 1 tanggal.'); return; }
        
        const status = document.getElementById('heStatus').value;
        const keterangan = document.getElementById('heKeterangan').value.trim();
        
        if (!keterangan) { alert('Keterangan kegiatan harus diisi.'); return; }
        
        const btn = document.getElementById('heSaveBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        btn.disabled = true;
        
        fetch('{{ route("admin.hari-efektif.save") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ tanggal, status, keterangan })
        })
        .then(r => r.json())
        .then(result => {
            btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
            btn.disabled = false;
            if (result.success) {
                alert(result.message);
                // Reset form inputs
                document.getElementById('heDateInputs').innerHTML = `
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <input type="date" class="form-control he-date-input" style="flex: 1; padding: 8px 12px;">
                    </div>
                `;
                document.getElementById('heKeterangan').value = '';
                // Reload list via AJAX
                refreshHariEfektifList();
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(() => {
            btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
            btn.disabled = false;
        });
    }

    function deleteHariEfektif(tanggal) {
        if (!confirm('Yakin ingin menghapus? Tanggal ini akan kembali menjadi hari efektif.')) return;
        
        fetch('{{ route("admin.hari-efektif.delete") }}', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ tanggal })
        })
        .then(r => r.json())
        .then(result => {
            if (result.success) {
                refreshHariEfektifList();
            } else {
                alert('Error: ' + result.message);
            }
        });
    }

    function refreshHariEfektifList() {
        fetch('{{ route("admin.hari-efektif.get") }}')
        .then(r => r.json())
        .then(result => {
            const container = document.getElementById('hariEfektifListContainer');
            const countEl = document.getElementById('heCount');
            
            if (!result.success || result.data.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 30px 20px; color: #9ca3af;">
                        <i class="fas fa-calendar-check" style="font-size: 32px; margin-bottom: 8px; display: block; color: #d1d5db;"></i>
                        <p style="margin: 0; font-size: 13px;">Belum ada data. Semua hari dianggap efektif.</p>
                    </div>
                `;
                countEl.textContent = '0 data';
                return;
            }
            
            countEl.textContent = result.data.length + ' data';
            
            const namaHari = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
            const namaBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            
            let html = `<table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e5e7eb; position: sticky; top: 0;">
                        <th style="padding: 8px 12px; text-align: left; font-weight: 600; color: #374151;">Tanggal</th>
                        <th style="padding: 8px 12px; text-align: left; font-weight: 600; color: #374151;">Status</th>
                        <th style="padding: 8px 12px; text-align: left; font-weight: 600; color: #374151;">Keterangan</th>
                        <th style="padding: 8px 12px; text-align: center; font-weight: 600; color: #374151; width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>`;
            
            result.data.forEach(item => {
                const d = new Date(item.tanggal);
                const hari = namaHari[d.getDay()];
                const tgl = d.getDate();
                const bln = namaBulan[d.getMonth()];
                const thn = d.getFullYear();
                const dateStr = hari + ', ' + tgl + ' ' + bln + ' ' + thn;
                
                const statusBg = item.status === 'Libur' ? '#ef4444' : '#f59e0b';
                
                html += `
                    <tr style="border-bottom: 1px solid #f1f5f9;" id="he-row-${item.tanggal}">
                        <td style="padding: 8px 12px; font-weight: 500; font-size: 12px;">${dateStr}</td>
                        <td style="padding: 8px 12px;">
                            <span style="background: ${statusBg}; color: white; padding: 2px 8px; border-radius: 8px; font-size: 10px; font-weight: 600;">${item.status}</span>
                        </td>
                        <td style="padding: 8px 12px; color: #4b5563; font-size: 12px;">${item.keterangan}</td>
                        <td style="padding: 8px 12px; text-align: center;">
                            <button onclick="deleteHariEfektif('${item.tanggal}')" style="background: none; border: none; cursor: pointer; color: #ef4444; font-size: 13px;" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>`;
            });
            
            html += '</tbody></table>';
            container.innerHTML = html;
        });
    }

</script>
@endpush
