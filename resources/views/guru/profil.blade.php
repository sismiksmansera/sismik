@extends('layouts.app')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Profil Saya | SISMIK')

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
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
        color: #10b981;
        font-weight: bold;
    }
    
    .profile-name { margin: 10px 0 5px; font-size: 1.3rem; }
    .profile-role { opacity: 0.9; font-size: 14px; margin-bottom: 10px; }
    .profile-status {
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
        color: #10b981;
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
    .btn-edit-photo:hover { transform: translateX(-50%) translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.2); }
    
    .info-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .info-card h4 {
        color: #059669;
        margin: 0 0 15px;
        font-size: 1.1rem;
    }
    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
    }
    .info-item i { color: #10b981; margin-right: 10px; width: 20px; text-align: center; }
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
        border-bottom: 2px solid #10b981;
        color: #065f46;
    }
    
    .form-section { margin-bottom: 25px; }
    .form-section h4 {
        color: #059669;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 15px;
    }
    
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-group.full { grid-column: span 2; }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 15px;
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
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    .form-control:disabled, .form-control[readonly] {
        background: #f9fafb;
        color: #6b7280;
        cursor: not-allowed;
    }
    .form-hint { color: #6b7280; font-size: 13px; margin-top: 5px; }
    
    .form-actions-row {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
        margin-top: 20px;
    }
    .btn-save {
        padding: 12px 30px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16,185,129,0.4); }
    
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
    .dropzone:hover { border-color: #10b981; background: #f0fdf4; }
    .dropzone i { font-size: 40px; color: #9ca3af; margin-bottom: 10px; }
    
    /* Responsive */
    @media (max-width: 991px) {
        .profile-sidebar { max-width: 100%; }
    }
    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
        .form-group.full { grid-column: span 1; }
        .form-actions-row { flex-direction: column; gap: 15px; }
        .btn-save { width: 100%; justify-content: center; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')

    <div class="main-content">
        <div style="margin-bottom: 20px;">
            <h2><i class="fas fa-user-circle"></i> Profil Saya</h2>
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
                        $hasFoto = $guru->foto && Storage::disk('public')->exists('guru/' . $guru->foto);
                        $initials = collect(explode(' ', $guru->nama))->map(fn($p) => strtoupper(substr($p, 0, 1)))->take(2)->join('');
                    @endphp
                    
                    <div class="profile-avatar" onclick="openFotoModal()">
                        @if($hasFoto)
                            <img src="{{ asset('storage/guru/' . $guru->foto) }}" alt="{{ $guru->nama }}" id="profilePhoto">
                        @else
                            <div class="profile-avatar-placeholder">{{ $initials ?: 'G' }}</div>
                        @endif
                    </div>
                    
                    <h3 class="profile-name">{{ $guru->nama }}</h3>
                    <p class="profile-role">Guru Mata Pelajaran</p>
                    
                    <div class="profile-status">
                        <i class="fas fa-circle" style="color: {{ ($guru->status ?? 'Aktif') === 'Aktif' ? '#10b981' : '#ef4444' }}; font-size: 10px;"></i>
                        {{ $guru->status ?? 'Aktif' }}
                    </div>
                    
                    <button type="button" onclick="openUploadModal()" class="btn-edit-photo">
                        <i class="fas fa-camera"></i> Ganti Foto
                    </button>
                </div>

                <!-- Info Card -->
                <div class="info-card">
                    <h4><i class="fas fa-info-circle"></i> Informasi Akun</h4>
                    
                    <div class="info-item">
                        <i class="fas fa-id-badge"></i>
                        <div>
                            <div class="label">NIP</div>
                            <div class="value">{{ $guru->nip ?: '-' }}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-user-circle" style="color: #10b981;"></i>
                        <div>
                            <div class="label">Username</div>
                            <div class="value">{{ $guru->username }}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-calendar-plus"></i>
                        <div>
                            <div class="label">Bergabung Sejak</div>
                            <div class="value">{{ $guru->created_at ? \Carbon\Carbon::parse($guru->created_at)->format('d M Y') : '-' }}</div>
                        </div>
                    </div>
                    
                    @if($guru->updated_at && $guru->updated_at != $guru->created_at)
                    <div class="info-item">
                        <i class="fas fa-sync-alt" style="color: #f59e0b;"></i>
                        <div>
                            <div class="label">Terakhir Update</div>
                            <div class="value">{{ \Carbon\Carbon::parse($guru->updated_at)->format('d M Y') }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Quick Info Card -->
                <div class="info-card">
                    <h4><i class="fas fa-briefcase"></i> Kepegawaian</h4>
                    
                    <div class="info-item">
                        <i class="fas fa-user-tie"></i>
                        <div>
                            <div class="label">Status Kepegawaian</div>
                            <div class="value">{{ $guru->status_kepegawaian ?: '-' }}</div>
                        </div>
                    </div>
                    
                    @if($guru->golongan)
                    <div class="info-item">
                        <i class="fas fa-arrow-up"></i>
                        <div>
                            <div class="label">Golongan</div>
                            <div class="value">{{ $guru->golongan }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($guru->jabatan)
                    <div class="info-item">
                        <i class="fas fa-user-tag"></i>
                        <div>
                            <div class="label">Jabatan</div>
                            <div class="value">{{ $guru->jabatan }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($guru->mapel_diampu)
                    <div class="info-item">
                        <i class="fas fa-book"></i>
                        <div>
                            <div class="label">Mapel Diampu</div>
                            <div class="value">{{ $guru->mapel_diampu }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Form -->
            <div class="profile-form">
                <form action="{{ route('guru.profil.update') }}" method="POST" class="form-card">
                    @csrf
                    @method('PUT')
                    
                    <h3><i class="fas fa-user-edit"></i> Edit Profil</h3>

                    <!-- Section: Identitas Dasar -->
                    <div class="form-section">
                        <h4><i class="fas fa-id-card"></i> Identitas Dasar</h4>
                        
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Nama Lengkap</label>
                            <input type="text" class="form-control" value="{{ $guru->nama }}" disabled>
                            <p class="form-hint">Nama tidak dapat diubah. Hubungi admin jika ada kesalahan.</p>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label><i class="fas fa-id-card"></i> NIP</label>
                                <input type="text" class="form-control" value="{{ $guru->nip ?: '-' }}" disabled>
                                <p class="form-hint">NIP tidak dapat diubah. Hubungi admin jika ada kesalahan.</p>
                            </div>
                            
                            <div class="form-group">
                                <label><i class="fas fa-venus-mars"></i> Jenis Kelamin</label>
                                <input type="text" class="form-control" value="{{ $guru->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}" disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Kontak & Alamat -->
                    <div class="form-section">
                        <h4><i class="fas fa-address-book"></i> Kontak & Alamat</h4>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $guru->email) }}" placeholder="email@sekolah.sch.id">
                            </div>
                            
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> Nomor HP</label>
                                <input type="tel" name="no_hp" class="form-control" value="{{ old('no_hp', $guru->no_hp) }}" placeholder="0812xxxxxxx">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> Alamat Lengkap</label>
                            <textarea name="alamat" rows="3" class="form-control" placeholder="Alamat lengkap">{{ old('alamat', $guru->alamat) }}</textarea>
                        </div>
                    </div>

                    <!-- Section: Ubah Password -->
                    <div class="form-section">
                        <h4><i class="fas fa-key"></i> Ubah Password</h4>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label><i class="fas fa-lock"></i> Password Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                                <p class="form-hint">Minimal 6 karakter jika ingin mengubah password</p>
                            </div>
                            
                            <div class="form-group">
                                <label><i class="fas fa-lock"></i> Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions-row">
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
        
        <h3 style="margin-top: 0; color: #065f46; margin-bottom: 20px;">
            <i class="fas fa-camera"></i> Upload Foto Profil
        </h3>
        
        <form action="{{ route('guru.profil.upload-foto') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
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
            
            <div style="background: #f0fdf4; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <p style="font-weight: 600; margin: 0 0 8px; font-size: 14px; color: #065f46;"><i class="fas fa-info-circle"></i> Ketentuan:</p>
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
                <button type="submit" style="flex: 1; padding: 10px 25px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
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
                <img src="{{ asset('storage/guru/' . $guru->foto) }}" alt="{{ $guru->nama }}" style="max-width: 500px; max-height: 70vh; border-radius: 8px;">
            @endif
        </div>
        <div style="margin-top: 15px; text-align: center; color: white;">
            <h4 style="margin: 0;">{{ $guru->nama }}</h4>
            <p style="opacity: 0.8; margin: 5px 0;">Foto Profil</p>
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
