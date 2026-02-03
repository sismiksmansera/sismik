@extends('layouts.app-guru-bk')

@section('content')
<div class="main-content edit-catatan-page">
    <div class="content">
        {{-- PROFILE CONTAINER --}}
        <div class="profile-container">
            {{-- SIDEBAR --}}
            <div class="profile-sidebar">
                {{-- STUDENT PROFILE CARD --}}
                <div class="profile-photo-card">
                    @php
                        $foto_exists = $catatan->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $catatan->foto);
                        $initials = collect(explode(' ', $catatan->nama_siswa))
                            ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                            ->take(2)
                            ->join('');
                    @endphp

                    <div class="photo-container" onclick="showFullPhoto()">
                        @if($foto_exists)
                            <img src="{{ asset('storage/siswa/' . $catatan->foto) }}" 
                                alt="Foto {{ $catatan->nama_siswa }}" 
                                class="profile-photo">
                            <div class="photo-overlay">
                                <i class="fas fa-expand-alt"></i>
                            </div>
                        @else
                            <div class="photo-placeholder">{{ $initials ?: 'S' }}</div>
                        @endif
                    </div>

                    <h3 class="profile-name">{{ $catatan->nama_siswa }}</h3>
                    <p class="profile-role">Siswa Bimbingan</p>

                    <div class="profile-status">
                        <i class="fas fa-circle" style="color: #10b981;"></i>
                        Aktif
                    </div>
                </div>

                {{-- INFO SISWA --}}
                <div class="info-card">
                    <h4 class="info-title">
                        <i class="fas fa-info-circle"></i> Informasi Siswa
                    </h4>

                    <div class="info-item">
                        <label>NIS/NISN</label>
                        <div class="info-value">{{ $catatan->nis }} / {{ $catatan->nisn }}</div>
                    </div>

                    <div class="info-item">
                        <label>Jenis Kelamin</label>
                        <div class="info-value">{{ $catatan->jk }}</div>
                    </div>

                    <div class="info-item">
                        <label>Rombel ({{ $catatan->semester }})</label>
                        <div class="info-value rombel-highlight">{{ $selected_rombel }}</div>
                    </div>
                </div>

                {{-- INFO CATATAN --}}
                <div class="info-card">
                    <h4 class="info-title">
                        <i class="fas fa-file-alt"></i> Informasi Catatan
                    </h4>

                    <div class="info-item">
                        <label>Tanggal Dibuat</label>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($catatan->created_at)->format('d M Y H:i') }}
                        </div>
                    </div>

                    <div class="info-item">
                        <label>Terakhir Diupdate</label>
                        <div class="info-value">
                            @if($catatan->updated_at && $catatan->updated_at != $catatan->created_at)
                                {{ \Carbon\Carbon::parse($catatan->updated_at)->format('d M Y H:i') }}
                            @else
                                - (belum pernah diupdate)
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <label>Dicatat oleh</label>
                        <div class="info-value">{{ $catatan->nama_guru_pembuat ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- FORM EDIT --}}
            <div class="profile-form">
                {{-- ALERTS --}}
                @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- FORM SECTION --}}
                <div class="form-card">
                    <div class="form-header">
                        <h3><i class="fas fa-edit"></i> Edit Catatan Bimbingan</h3>
                    </div>

                    <form method="POST" action="{{ route('guru_bk.catatan-bimbingan.update', $catatan->id) }}" class="catatan-form">
                        @csrf
                        @method('PUT')

                        <div class="form-body">
                            <div class="form-grid">
                                {{-- LEFT COLUMN --}}
                                <div class="form-column">
                                    <div class="form-group">
                                        <label for="tanggal">
                                            <i class="fas fa-calendar-day"></i> Tanggal Bimbingan *
                                        </label>
                                        <input type="date" 
                                            id="tanggal"
                                            name="tanggal" 
                                            class="form-control" 
                                            value="{{ $catatan->tanggal }}" 
                                            min="{{ $min_date }}" 
                                            max="{{ $max_date }}"
                                            required>
                                    </div>

                                    <div class="form-group">
                                        <label for="jenis_bimbingan">
                                            <i class="fas fa-tag"></i> Jenis Bimbingan *
                                        </label>
                                        <select id="jenis_bimbingan" name="jenis_bimbingan" class="form-control" required>
                                            <option value="">Pilih Jenis Bimbingan</option>
                                            <option value="Pribadi" {{ $catatan->jenis_bimbingan == 'Pribadi' ? 'selected' : '' }}>Pribadi</option>
                                            <option value="Sosial" {{ $catatan->jenis_bimbingan == 'Sosial' ? 'selected' : '' }}>Sosial</option>
                                            <option value="Belajar" {{ $catatan->jenis_bimbingan == 'Belajar' ? 'selected' : '' }}>Belajar</option>
                                            <option value="Karir" {{ $catatan->jenis_bimbingan == 'Karir' ? 'selected' : '' }}>Karir</option>
                                            <option value="Lainnya" {{ $catatan->jenis_bimbingan == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="status">
                                            <i class="fas fa-clipboard-check"></i> Status *
                                        </label>
                                        <select id="status" name="status" class="form-control" required>
                                            <option value="">Pilih Status</option>
                                            <option value="Belum Ditangani" {{ $status_form_value == 'Belum Ditangani' ? 'selected' : '' }}>Belum Ditangani</option>
                                            <option value="Dalam Proses" {{ $status_form_value == 'Dalam Proses' ? 'selected' : '' }}>Dalam Proses</option>
                                            <option value="Selesai" {{ $status_form_value == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- RIGHT COLUMN --}}
                                <div class="form-column">
                                    <div class="form-group">
                                        <label for="tahun_pelajaran">
                                            <i class="fas fa-calendar"></i> Tahun Pelajaran *
                                        </label>
                                        <input type="text" 
                                            id="tahun_pelajaran"
                                            name="tahun_pelajaran" 
                                            class="form-control disabled-field"
                                            value="{{ $catatan->tahun_pelajaran }}" 
                                            required
                                            readonly>
                                    </div>

                                    <div class="form-group">
                                        <label for="semester">
                                            <i class="fas fa-calendar-week"></i> Semester *
                                        </label>
                                        <select id="semester" name="semester" class="form-control disabled-field" required disabled>
                                            <option value="Ganjil" {{ $catatan->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                            <option value="Genap" {{ $catatan->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                        </select>
                                        <input type="hidden" name="semester" value="{{ $catatan->semester }}">
                                    </div>
                                </div>
                            </div>

                            {{-- MASALAH --}}
                            <div class="form-section">
                                <div class="section-header danger">
                                    <h4><i class="fas fa-exclamation-triangle"></i> Masalah/Permasalahan *</h4>
                                </div>
                                <div class="form-group">
                                    <textarea id="masalah"
                                            name="masalah" 
                                            class="form-control" 
                                            rows="5" 
                                            placeholder="Deskripsikan masalah atau permasalahan yang dihadapi siswa..." 
                                            required>{{ $catatan->masalah }}</textarea>
                                </div>
                            </div>

                            {{-- PENYELESAIAN --}}
                            <div class="form-section">
                                <div class="section-header success">
                                    <h4><i class="fas fa-lightbulb"></i> Penyelesaian *</h4>
                                </div>
                                <div class="form-group">
                                    <textarea id="penyelesaian"
                                            name="penyelesaian" 
                                            class="form-control" 
                                            rows="5" 
                                            placeholder="Deskripsikan langkah-langkah penyelesaian yang dilakukan..." 
                                            required>{{ $catatan->penyelesaian }}</textarea>
                                </div>
                            </div>

                            {{-- TINDAK LANJUT --}}
                            <div class="form-section">
                                <div class="section-header primary">
                                    <h4><i class="fas fa-tasks"></i> Tindak Lanjut *</h4>
                                </div>
                                <div class="form-group">
                                    <textarea id="tindak_lanjut"
                                            name="tindak_lanjut" 
                                            class="form-control" 
                                            rows="4" 
                                            placeholder="Deskripsikan rencana tindak lanjut..." 
                                            required>{{ $catatan->tindak_lanjut }}</textarea>
                                </div>
                            </div>

                            {{-- KETERANGAN --}}
                            <div class="form-section">
                                <div class="section-header secondary">
                                    <h4><i class="fas fa-sticky-note"></i> Keterangan Tambahan</h4>
                                </div>
                                <div class="form-group">
                                    <textarea id="keterangan"
                                            name="keterangan" 
                                            class="form-control" 
                                            rows="3" 
                                            placeholder="Tambahan keterangan lainnya (opsional)...">{{ $catatan->keterangan }}</textarea>
                                </div>
                            </div>

                            {{-- FORM ACTIONS --}}
                            <div class="form-actions">
                                <div class="action-left">
                                    <a href="{{ route('guru_bk.catatan-bimbingan', ['nisn' => $catatan->nisn]) }}" class="btn-back">
                                        <i class="fas fa-arrow-left"></i> <span class="btn-text">Kembali</span>
                                    </a>
                                </div>
                                <div class="action-right">
                                    <button type="reset" class="btn-reset">
                                        <i class="fas fa-redo"></i> <span class="btn-text">Reset</span>
                                    </button>
                                    <button type="submit" class="btn-submit">
                                        <i class="fas fa-save"></i> <span class="btn-text">Update Catatan</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- FULL PHOTO MODAL --}}
<div id="fullPhotoModal" class="full-photo-modal" style="display: none;">
    <div class="modal-content-photo">
        <button onclick="closeFullPhoto()" class="modal-close-photo">
            <i class="fas fa-times"></i>
        </button>
        @if($foto_exists ?? false)
            <img src="{{ asset('storage/siswa/' . $catatan->foto) }}" alt="Foto {{ $catatan->nama_siswa }}" class="full-photo-img">
            <div class="photo-info">
                <h4>{{ $catatan->nama_siswa }}</h4>
                <p>NISN: {{ $catatan->nisn }}</p>
            </div>
        @else
            <div class="no-photo-full">
                <div class="no-photo-icon-full">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h4>{{ $catatan->nama_siswa }}</h4>
                <p>Belum ada foto profil</p>
            </div>
        @endif
    </div>
</div>

<style>
/* Main Content */
.main-content.edit-catatan-page {
    background: #f9fafb;
    min-height: calc(100vh - 70px);
    padding: 25px;
}

.content {
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
}

.profile-container {
    animation: slideUp 0.6s ease;
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    margin-top: 20px;
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

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Profile Card */
.profile-photo-card {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    padding: 30px;
    border-radius: 16px;
    text-align: center;
    color: white;
    margin-bottom: 20px;
}

.photo-container {
    width: 120px;
    height: 120px;
    margin: 0 auto 15px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid rgba(255,255,255,0.3);
    cursor: pointer;
    position: relative;
}

.profile-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-placeholder {
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    font-weight: 700;
}

.photo-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.photo-container:hover .photo-overlay {
    opacity: 1;
}

.profile-name {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0 0 5px;
}

.profile-role {
    font-size: 0.9rem;
    opacity: 0.8;
    margin: 0 0 15px;
}

.profile-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.2);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
}

/* Info Cards */
.info-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.info-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f3f4f6;
}

.info-title i {
    color: #7c3aed;
}

.info-item {
    margin-bottom: 12px;
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-item label {
    display: block;
    font-size: 0.75rem;
    color: #9ca3af;
    font-weight: 500;
    margin-bottom: 4px;
}

.info-value {
    font-size: 0.9rem;
    color: #1f2937;
    font-weight: 500;
}

.rombel-highlight {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 8px;
    display: inline-block;
}

/* Form Card */
.form-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.form-header {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    color: white;
    padding: 20px 25px;
}

.form-header h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-body {
    padding: 25px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.form-group label i {
    color: #7c3aed;
    margin-right: 6px;
}

.form-control {
    width: 100%;
    padding: 12px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #7c3aed;
    box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
}

.disabled-field {
    background: #f9fafb;
    color: #6b7280;
    cursor: not-allowed;
}

/* Form Sections */
.form-section {
    margin-bottom: 25px;
}

.section-header {
    padding: 12px 15px;
    border-radius: 10px;
    margin-bottom: 15px;
}

.section-header h4 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-header.danger {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    color: #dc2626;
}

.section-header.success {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    color: #059669;
}

.section-header.primary {
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    color: #7c3aed;
}

.section-header.secondary {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    color: #6b7280;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 20px;
    border-top: 2px solid #f3f4f6;
}

.action-left, .action-right {
    display: flex;
    gap: 10px;
}

.btn-back, .btn-reset, .btn-submit {
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    border: none;
    transition: all 0.3s ease;
}

.btn-back {
    background: #f3f4f6;
    color: #374151;
}

.btn-back:hover {
    background: #e5e7eb;
}

.btn-reset {
    background: #fef2f2;
    color: #dc2626;
}

.btn-reset:hover {
    background: #fee2e2;
}

.btn-submit {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    color: white;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);
}

/* Alerts */
.alert {
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 500;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.alert-success {
    background: #f0fdf4;
    color: #059669;
    border: 1px solid #a7f3d0;
}

.alert-danger {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.alert ul {
    margin: 0;
    padding-left: 20px;
}

/* Full Photo Modal */
.full-photo-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content-photo {
    background: white;
    border-radius: 20px;
    padding: 30px;
    max-width: 500px;
    text-align: center;
    position: relative;
}

.modal-close-photo {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #ef4444;
    color: white;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.full-photo-img {
    max-width: 100%;
    max-height: 400px;
    border-radius: 15px;
}

.no-photo-full {
    padding: 40px;
}

.no-photo-icon-full {
    font-size: 80px;
    color: #d1d5db;
    margin-bottom: 20px;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .main-content.edit-catatan-page {
        padding: 15px;
    }

    .profile-container {
        flex-direction: column;
    }

    .profile-sidebar {
        max-width: 100%;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
        gap: 15px;
    }

    .action-left, .action-right {
        width: 100%;
    }

    .btn-back, .btn-reset, .btn-submit {
        flex: 1;
        justify-content: center;
    }
}
</style>

<script>
function showFullPhoto() {
    document.getElementById('fullPhotoModal').style.display = 'flex';
}

function closeFullPhoto() {
    document.getElementById('fullPhotoModal').style.display = 'none';
}

document.getElementById('fullPhotoModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeFullPhoto();
});
</script>
@endsection
