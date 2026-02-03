@extends('layouts.app')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Edit Siswa - {{ $siswa->nama }} | SISMIK')

@push('styles')
<style>
    .profile-container {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        margin-top: 20px;
        animation: slideUp 0.6s ease;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .profile-sidebar {
        flex: 1;
        min-width: 300px;
        max-width: 350px;
    }
    
    .profile-form {
        flex: 2;
        min-width: 300px;
    }
    
    .profile-card {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        color: white;
        margin-bottom: 20px;
        position: relative;
    }
    
    .profile-avatar {
        width: 150px;
        height: 150px;
        margin: 0 auto 15px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid white;
        cursor: pointer;
        transition: transform 0.3s;
    }
    .profile-avatar:hover { transform: scale(1.05); }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .profile-avatar-placeholder {
        width: 100%;
        height: 100%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #4f46e5;
        font-weight: bold;
    }
    
    .profile-name { margin: 10px 0 5px; font-size: 1.3rem; }
    .profile-role { opacity: 0.9; font-size: 14px; margin-bottom: 10px; }
    .profile-badge {
        background: rgba(255,255,255,0.15);
        padding: 8px 12px;
        border-radius: 20px;
        display: inline-block;
        font-size: 14px;
    }
    
    .btn-edit-photo {
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        color: #4f46e5;
        border: none;
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    .btn-edit-photo:hover { transform: translateX(-50%) translateY(-2px); }
    
    .info-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .info-card h4 {
        color: #1e40af;
        margin: 0 0 15px;
        font-size: 1.1rem;
    }
    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
    }
    .info-item i { margin-right: 10px; width: 20px; text-align: center; }
    .info-item .label { font-size: 13px; color: #6b7280; }
    .info-item .value { font-weight: 500; }
    
    .form-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    }
    .form-card h3 {
        margin: 0 0 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #4f46e5;
        color: #1e3a8a;
    }
    
    .form-section { margin-bottom: 25px; }
    .form-section h4 {
        color: #1e40af;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 15px;
    }
    
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .form-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
    .form-group { margin-bottom: 20px; }
    .form-group.full { grid-column: span 2; }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }
    .form-group label span { color: #ef4444; }
    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s;
    }
    .form-control:focus {
        outline: none;
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    .form-hint { color: #6b7280; font-size: 13px; margin-top: 5px; }
    
    .password-box {
        background: #f8fafc;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        border-left: 4px solid #4f46e5;
    }
    
    .form-actions-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
        margin-top: 20px;
    }
    .btn-back {
        padding: 10px 20px;
        background: #f3f4f6;
        color: #374151;
        text-decoration: none;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    .btn-back:hover { background: #e5e7eb; }
    .btn-save {
        padding: 12px 30px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(79,70,229,0.4); }
    
    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    .modal-box {
        background: white;
        padding: 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        position: relative;
    }
    .modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #6b7280;
    }
    
    .dropzone {
        width: 150px;
        height: 150px;
        margin: 0 auto 20px;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    .dropzone:hover { border-color: #4f46e5; background: #f8fafc; }
    .dropzone i { font-size: 40px; color: #9ca3af; margin-bottom: 10px; }
    
    /* Responsive */
    @media (max-width: 991px) {
        .profile-sidebar { max-width: 100%; }
    }
    @media (max-width: 768px) {
        .form-grid, .form-grid-3 { grid-template-columns: 1fr; }
        .form-group.full { grid-column: span 1; }
        .form-actions-row { flex-direction: column; gap: 15px; }
        .btn-back, .btn-save { width: 100%; justify-content: center; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <div style="margin-bottom: 20px;">
            <h2><i class="fas fa-user-edit"></i> Edit Data Siswa</h2>
        </div>

        @if(session('success'))
            <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #a7f3d0;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                <strong><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan:</strong>
                <ul style="margin: 8px 0 0 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="profile-container">
            <!-- Sidebar -->
            <div class="profile-sidebar">
                <!-- Profile Card -->
                <div class="profile-card">
                    @php
                        $hasFoto = $siswa->foto && Storage::disk('public')->exists('siswa/' . $siswa->foto);
                        $initials = collect(explode(' ', $siswa->nama))->map(fn($p) => strtoupper(substr($p, 0, 1)))->take(2)->join('');
                    @endphp
                    
                    <div class="profile-avatar" onclick="openFotoModal()">
                        @if($hasFoto)
                            <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}">
                        @else
                            <div class="profile-avatar-placeholder">{{ $initials ?: 'S' }}</div>
                        @endif
                    </div>
                    
                    <h3 class="profile-name">{{ $siswa->nama }}</h3>
                    <p class="profile-role">Siswa</p>
                    
                    <div class="profile-badge">
                        <i class="fas fa-graduation-cap"></i>
                        NIS: {{ $siswa->nis ?? '-' }}
                    </div>
                    
                    <button type="button" onclick="openUploadModal()" class="btn-edit-photo">
                        <i class="fas fa-camera"></i> Ganti Foto
                    </button>
                </div>

                <!-- Info Card -->
                <div class="info-card">
                    <h4><i class="fas fa-info-circle"></i> Informasi Siswa</h4>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 5px;">NIS</label>
                        <div style="padding: 10px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 6px; font-weight: 600;">
                            {{ $siswa->nis ?? 'Belum diatur' }}
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 5px;">NISN</label>
                        <div style="padding: 10px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 6px; font-weight: 600;">
                            {{ $siswa->nisn ?? 'Belum diatur' }}
                        </div>
                    </div>
                </div>

                <!-- History Card -->
                <div class="info-card">
                    <h4><i class="fas fa-history"></i> Riwayat Akun</h4>
                    
                    @if($siswa->created_at)
                    <div class="info-item">
                        <i class="fas fa-calendar-plus" style="color: #10b981;"></i>
                        <div>
                            <div class="label">Didaftarkan</div>
                            <div class="value">{{ \Carbon\Carbon::parse($siswa->created_at)->format('d M Y') }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($siswa->updated_at && $siswa->updated_at != $siswa->created_at)
                    <div class="info-item">
                        <i class="fas fa-sync-alt" style="color: #f59e0b;"></i>
                        <div>
                            <div class="label">Terakhir Update</div>
                            <div class="value">{{ \Carbon\Carbon::parse($siswa->updated_at)->format('d M Y') }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Back Button -->
                <a href="{{ route('admin.siswa.index') }}" style="display: block; padding: 12px; background: #f3f4f6; color: #374151; text-decoration: none; border-radius: 8px; font-weight: 500; text-align: center;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Siswa
                </a>
            </div>

            <!-- Form -->
            <div class="profile-form">
                <form action="{{ route('admin.siswa.update', $siswa->id) }}" method="POST" enctype="multipart/form-data" class="form-card">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="full_update" value="1">
                    
                    <h3><i class="fas fa-user-edit"></i> EDIT DATA SISWA</h3>

                    <!-- Password Section -->
                    <div class="password-box">
                        <h4 style="margin: 0 0 15px; color: #1e3a8a;"><i class="fas fa-key"></i> Informasi Login Siswa</h4>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Password Akun Siswa</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                            <p class="form-hint">Biarkan kosong jika tidak ingin mengubah password saat ini</p>
                        </div>
                    </div>

                    <!-- Section: Data Dasar -->
                    <div class="form-section">
                        <h4><i class="fas fa-id-card"></i> Data Dasar</h4>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>NIS</label>
                                <input type="text" name="nis" class="form-control" value="{{ old('nis', $siswa->nis) }}">
                            </div>
                            <div class="form-group">
                                <label>NISN <span>*</span></label>
                                <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $siswa->nisn) }}" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Nama Lengkap <span>*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama', $siswa->nama) }}" required>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label><i class="fas fa-venus-mars"></i> Jenis Kelamin</label>
                                <select name="jk" class="form-control">
                                    <option value="">--Pilih--</option>
                                    <option value="Laki-laki" {{ old('jk', $siswa->jk) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jk', $siswa->jk) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-pray"></i> Agama</label>
                                <input type="text" name="agama" class="form-control" value="{{ old('agama', $siswa->agama) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Data Kelahiran & Alamat -->
                    <div class="form-section">
                        <h4><i class="fas fa-map-marker-alt"></i> Data Kelahiran & Alamat</h4>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir', $siswa->tgl_lahir) }}">
                            </div>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> No HP Siswa</label>
                                <input type="text" name="nohp_siswa" class="form-control" value="{{ old('nohp_siswa', $siswa->nohp_siswa) }}">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $siswa->email) }}">
                            </div>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Provinsi</label>
                                <input type="text" name="provinsi" class="form-control" value="{{ old('provinsi', $siswa->provinsi) }}">
                            </div>
                            <div class="form-group">
                                <label>Kota/Kabupaten</label>
                                <input type="text" name="kota" class="form-control" value="{{ old('kota', $siswa->kota) }}">
                            </div>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Kecamatan</label>
                                <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan', $siswa->kecamatan) }}">
                            </div>
                            <div class="form-group">
                                <label>Kelurahan</label>
                                <input type="text" name="kelurahan" class="form-control" value="{{ old('kelurahan', $siswa->kelurahan) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Data Orang Tua -->
                    <div class="form-section">
                        <h4><i class="fas fa-users"></i> Data Orang Tua</h4>
                        
                        <p style="font-weight: 600; margin-bottom: 15px;"><i class="fas fa-male"></i> Ayah</p>
                        <div class="form-grid-3">
                            <div class="form-group">
                                <label>Nama Ayah</label>
                                <input type="text" name="nama_bapak" class="form-control" value="{{ old('nama_bapak', $siswa->nama_bapak) }}">
                            </div>
                            <div class="form-group">
                                <label>Pekerjaan</label>
                                <input type="text" name="pekerjaan_bapak" class="form-control" value="{{ old('pekerjaan_bapak', $siswa->pekerjaan_bapak) }}">
                            </div>
                            <div class="form-group">
                                <label>No HP</label>
                                <input type="text" name="nohp_bapak" class="form-control" value="{{ old('nohp_bapak', $siswa->nohp_bapak) }}">
                            </div>
                        </div>
                        
                        <p style="font-weight: 600; margin-bottom: 15px;"><i class="fas fa-female"></i> Ibu</p>
                        <div class="form-grid-3">
                            <div class="form-group">
                                <label>Nama Ibu</label>
                                <input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu', $siswa->nama_ibu) }}">
                            </div>
                            <div class="form-group">
                                <label>Pekerjaan</label>
                                <input type="text" name="pekerjaan_ibu" class="form-control" value="{{ old('pekerjaan_ibu', $siswa->pekerjaan_ibu) }}">
                            </div>
                            <div class="form-group">
                                <label>No HP</label>
                                <input type="text" name="nohp_ibu" class="form-control" value="{{ old('nohp_ibu', $siswa->nohp_ibu) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Data Akademik -->
                    <div class="form-section">
                        <h4><i class="fas fa-graduation-cap"></i> Data Akademik & Lainnya</h4>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Jumlah Saudara</label>
                                <input type="number" name="jml_saudara" class="form-control" value="{{ old('jml_saudara', $siswa->jml_saudara) }}" min="0">
                            </div>
                            <div class="form-group">
                                <label>Anak ke-</label>
                                <input type="number" name="anak_ke" class="form-control" value="{{ old('anak_ke', $siswa->anak_ke) }}" min="1">
                            </div>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Asal Sekolah</label>
                                <input type="text" name="asal_sekolah" class="form-control" value="{{ old('asal_sekolah', $siswa->asal_sekolah) }}">
                            </div>
                            <div class="form-group">
                                <label>Nilai SKL</label>
                                <input type="text" name="nilai_skl" class="form-control" value="{{ old('nilai_skl', $siswa->nilai_skl) }}">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Cita-cita</label>
                            <input type="text" name="cita_cita" class="form-control" value="{{ old('cita_cita', $siswa->cita_cita) }}">
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Mapel Favorit 1</label>
                                <input type="text" name="mapel_fav1" class="form-control" value="{{ old('mapel_fav1', $siswa->mapel_fav1) }}">
                            </div>
                            <div class="form-group">
                                <label>Mapel Favorit 2</label>
                                <input type="text" name="mapel_fav2" class="form-control" value="{{ old('mapel_fav2', $siswa->mapel_fav2) }}">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Harapan</label>
                            <textarea name="harapan" rows="3" class="form-control">{{ old('harapan', $siswa->harapan) }}</textarea>
                        </div>
                    </div>

                    <div class="form-actions-row">
                        <a href="{{ route('admin.siswa.index') }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Foto -->
<div id="uploadModal" class="modal-overlay">
    <div class="modal-box">
        <button onclick="closeUploadModal()" class="modal-close">&times;</button>
        
        <h3 style="margin-top: 0; color: #1e3a8a; margin-bottom: 20px;">
            <i class="fas fa-camera"></i> Upload Foto Profil Siswa
        </h3>
        
        <form action="{{ route('admin.siswa.update', $siswa->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">
            <input type="hidden" name="nama" value="{{ $siswa->nama }}">
            
            <div style="text-align: center;">
                <div class="dropzone" onclick="document.getElementById('fotoInput').click()" id="dropZone">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p style="color: #6b7280; font-size: 14px; margin: 0;">Klik atau drag file</p>
                    <p style="color: #9ca3af; font-size: 12px; margin: 5px 0 0;">Maks. 2MB</p>
                </div>
                <input type="file" name="foto" id="fotoInput" accept="image/*" style="display: none;" onchange="previewImage(this)">
                
                <div id="imagePreview" style="display: none; margin-bottom: 15px;">
                    <img id="previewImg" src="" alt="Preview" style="max-width: 150px; max-height: 150px; border-radius: 8px; margin-bottom: 10px;">
                    <br>
                    <button type="button" onclick="removePreview()" style="background: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
            
            <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <p style="font-weight: 600; margin: 0 0 8px; font-size: 14px; color: #374151;"><i class="fas fa-info-circle"></i> Ketentuan:</p>
                <ul style="margin: 0; padding-left: 20px; color: #6b7280; font-size: 13px;">
                    <li>Format: JPG, JPEG, PNG, GIF</li>
                    <li>Ukuran maksimal: 2MB (akan dikompresi ke max 250KB)</li>
                    <li>Rasio disarankan: 1:1 (persegi)</li>
                </ul>
            </div>
            
            <div style="display: flex; justify-content: space-between; gap: 12px;">
                <button type="button" onclick="closeUploadModal()" style="flex: 1; padding: 10px 20px; background: #f3f4f6; color: #374151; border: none; border-radius: 6px; cursor: pointer;">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" style="flex: 1; padding: 10px 25px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-upload"></i> Upload Foto
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Foto Full -->
<div id="fotoModal" class="modal-overlay" style="background: rgba(0,0,0,0.9);">
    <div style="position: relative; max-width: 90%; max-height: 90%;">
        <button onclick="closeFotoModal()" style="position: absolute; top: -40px; right: 0; background: none; border: none; font-size: 24px; cursor: pointer; color: white;">
            <i class="fas fa-times"></i>
        </button>
        <div style="background: white; padding: 8px; border-radius: 12px;">
            @if($hasFoto)
                <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}" style="max-width: 500px; max-height: 70vh; border-radius: 8px;">
            @endif
        </div>
        <div style="margin-top: 15px; text-align: center; color: white;">
            <h4 style="margin: 0;">{{ $siswa->nama }}</h4>
            <p style="opacity: 0.8; margin: 5px 0;">Foto Profil Siswa</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openUploadModal() {
    document.getElementById('uploadModal').style.display = 'flex';
}
function closeUploadModal() {
    document.getElementById('uploadModal').style.display = 'none';
}
function openFotoModal() {
    @if($hasFoto)
    document.getElementById('fotoModal').style.display = 'flex';
    @endif
}
function closeFotoModal() {
    document.getElementById('fotoModal').style.display = 'none';
}
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
            document.getElementById('dropZone').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function removePreview() {
    document.getElementById('fotoInput').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('dropZone').style.display = 'flex';
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
