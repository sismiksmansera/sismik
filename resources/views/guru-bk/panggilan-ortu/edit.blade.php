@extends('layouts.app-guru-bk')

@section('content')
<div class="main-content panggilan-form-page">
    {{-- HEADER SECTION - Dashboard Style --}}
    @php
        $foto_exists = $siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto);
        $initials = collect(explode(' ', $siswa->nama))
            ->map(fn($p) => strtoupper(substr($p, 0, 1)))
            ->take(2)
            ->join('');
        $foto_dok_exists = property_exists($panggilan, 'foto_dokumentasi') && $panggilan->foto_dokumentasi && \Illuminate\Support\Facades\Storage::disk('public')->exists('panggilan_ortu/' . $panggilan->foto_dokumentasi);
    @endphp
    
    <div class="bk-page-header">
        <div class="header-content-wrapper">
            <div class="header-icon-box clickable-avatar" onclick="showPhotoModal('{{ $siswa->nama }}', '{{ $foto_exists ? asset('storage/siswa/' . $siswa->foto) : '' }}', '{{ $initials ?: 'S' }}', '{{ $siswa->jk }}')" title="Lihat Foto">
                @if($foto_exists)
                    <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}" class="header-photo-img">
                @else
                    <i class="fas fa-edit"></i>
                @endif
                <div class="avatar-overlay"><i class="fas fa-search-plus"></i></div>
            </div>
            <div class="header-info">
                <div class="header-greeting">
                    <span class="greeting-text">Edit Surat Panggilan</span>
                    <h1>{{ $siswa->nama }}</h1>
                </div>
                <div class="header-details">
                    <span class="detail-badge"><i class="fas fa-id-card"></i> {{ $siswa->nisn }}</span>
                    <span class="detail-badge"><i class="fas fa-hashtag"></i> {{ $panggilan->no_surat ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS ROW --}}
    <div class="action-buttons-row">
        <a href="{{ route('guru_bk.panggilan-ortu', $siswa->nisn) }}" class="btn-action-header btn-secondary-header">
            <i class="fas fa-arrow-left"></i> <span class="btn-text">Kembali</span>
        </a>
    </div>

    {{-- ALERTS --}}
    @if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- FORM CARD --}}
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-file-alt"></i>
            <h2>Edit Surat Panggilan Orang Tua</h2>
        </div>
        
        <form method="POST" action="{{ route('guru_bk.panggilan-ortu.update', $panggilan->id) }}" class="form-body" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ROW: No Surat & Tanggal Surat --}}
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-hashtag"></i> Nomor Surat</label>
                    <input type="text" name="no_surat" class="form-control" 
                           value="{{ old('no_surat', $panggilan->no_surat) }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Tanggal Surat <span class="required">*</span></label>
                    <input type="date" name="tanggal_surat" class="form-control" required
                           value="{{ old('tanggal_surat', $panggilan->tanggal_surat) }}">
                </div>
            </div>

            {{-- Perihal --}}
            <div class="form-group">
                <label><i class="fas fa-clipboard"></i> Perihal <span class="required">*</span></label>
                <input type="text" name="perihal" class="form-control" required
                       placeholder="Contoh: Undangan Pertemuan Orang Tua/Wali Siswa"
                       value="{{ old('perihal', $panggilan->perihal) }}">
            </div>

            {{-- Alasan --}}
            <div class="form-group">
                <label><i class="fas fa-info-circle"></i> Alasan / Keterangan</label>
                <textarea name="alasan" class="form-control" rows="3" 
                          placeholder="Jelaskan alasan pemanggilan orang tua...">{{ old('alasan', $panggilan->alasan) }}</textarea>
            </div>

            {{-- Menghadap Ke --}}
            <div class="form-group">
                <label><i class="fas fa-user-tie"></i> Menghadap Ke</label>
                <input type="text" name="menghadap_ke" class="form-control readonly-field"
                       value="{{ old('menghadap_ke', $panggilan->menghadap_ke) }}" readonly>
            </div>

            {{-- ROW: Tanggal, Jam, Tempat --}}
            <div class="form-row three-cols">
                <div class="form-group">
                    <label><i class="fas fa-calendar-check"></i> Tanggal Panggilan <span class="required">*</span></label>
                    <input type="date" name="tanggal_panggilan" class="form-control" required
                           value="{{ old('tanggal_panggilan', $panggilan->tanggal_panggilan) }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Jam Panggilan</label>
                    <input type="time" name="jam_panggilan" class="form-control"
                           value="{{ old('jam_panggilan', $panggilan->jam_panggilan) }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Tempat</label>
                    <input type="text" name="tempat" class="form-control" placeholder="Ruang BK"
                           value="{{ old('tempat', $panggilan->tempat ?? 'Ruang BK') }}">
                </div>
            </div>

            {{-- Status --}}
            <div class="form-group">
                <label><i class="fas fa-flag"></i> Status</label>
                <select name="status" class="form-control">
                    <option value="Menunggu" {{ old('status', $panggilan->status) == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="Hadir" {{ old('status', $panggilan->status) == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="Tidak Hadir" {{ old('status', $panggilan->status) == 'Tidak Hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                    <option value="Dijadwalkan Ulang" {{ old('status', $panggilan->status) == 'Dijadwalkan Ulang' ? 'selected' : '' }}>Dijadwalkan Ulang</option>
                </select>
            </div>

            {{-- Catatan --}}
            <div class="form-group">
                <label><i class="fas fa-sticky-note"></i> Catatan</label>
                <textarea name="catatan" class="form-control" rows="2" 
                          placeholder="Catatan tambahan...">{{ old('catatan', $panggilan->catatan) }}</textarea>
            </div>

            {{-- FOTO DOKUMENTASI --}}
            <div class="form-group foto-upload-section">
                <label><i class="fas fa-camera"></i> Foto Dokumentasi</label>
                
                <div class="foto-upload-wrapper">
                    {{-- Preview Area --}}
                    <div class="foto-preview-area" id="fotoPreviewArea">
                        @if($foto_dok_exists)
                            <img src="{{ asset('storage/panggilan_ortu/' . $panggilan->foto_dokumentasi) }}" alt="Foto Dokumentasi" id="currentFoto">
                            <div class="foto-overlay">
                                <button type="button" class="btn-view-foto" onclick="viewDokumentasi()">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                                <button type="button" class="btn-remove-foto" onclick="removeFoto()">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @else
                            <div class="foto-placeholder-upload" id="fotoPlaceholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Klik untuk upload foto</span>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Hidden File Input --}}
                    <input type="file" name="foto_dokumentasi" id="fotoInput" accept="image/*" class="hidden-file-input">
                    <input type="hidden" name="hapus_foto" id="hapusFoto" value="0">
                    
                    {{-- Upload Info --}}
                    <div class="upload-info" id="uploadInfo" style="display: none;">
                        <span class="file-name" id="fileName"></span>
                        <span class="file-size" id="fileSize"></span>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="form-actions">
                <a href="{{ route('guru_bk.panggilan-ortu', $siswa->nisn) }}" class="btn-cancel">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- PHOTO MODAL --}}
<div id="photoModal" class="photo-modal-overlay" style="display: none;">
    <div class="photo-modal-content">
        <button type="button" class="photo-modal-close" onclick="closePhotoModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="photo-modal-body">
            <div id="photoModalImage" class="photo-modal-image"></div>
            <div class="photo-modal-name" id="photoModalName"></div>
        </div>
    </div>
</div>

{{-- DOKUMENTASI MODAL --}}
<div id="dokumentasiModal" class="photo-modal-overlay" style="display: none;">
    <div class="photo-modal-content dokumentasi-modal">
        <button type="button" class="photo-modal-close" onclick="closeDokumentasiModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="photo-modal-body">
            <img id="dokumentasiModalImage" src="" alt="Foto Dokumentasi">
            <div class="photo-modal-name">Foto Dokumentasi Pertemuan</div>
        </div>
    </div>
</div>

<style>
.main-content.panggilan-form-page {
    padding: 25px;
    background: #f9fafb;
    min-height: calc(100vh - 70px);
}

/* ============== HEADER - Dashboard Style ============== */
.panggilan-form-page .bk-page-header {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    border-radius: 16px;
    padding: 25px 30px;
    margin-bottom: 20px;
    box-shadow: 0 10px 30px rgba(124, 58, 237, 0.2);
}

.panggilan-form-page .header-content-wrapper {
    display: flex;
    align-items: center;
    gap: 20px;
}

.panggilan-form-page .header-icon-box {
    width: 70px;
    height: 70px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}

.panggilan-form-page .header-icon-box.clickable-avatar {
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.panggilan-form-page .header-icon-box.clickable-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
}

.panggilan-form-page .header-photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.panggilan-form-page .avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.panggilan-form-page .header-icon-box.clickable-avatar:hover .avatar-overlay {
    opacity: 1;
}

.panggilan-form-page .avatar-overlay i {
    color: white;
    font-size: 18px;
}

.panggilan-form-page .header-info { flex: 1; }

.panggilan-form-page .header-greeting .greeting-text {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
    display: block;
    margin-bottom: 4px;
}

.panggilan-form-page .header-greeting h1 {
    font-size: 22px;
    font-weight: 700;
    color: white;
    margin: 0;
}

.panggilan-form-page .header-details {
    display: flex;
    gap: 12px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.panggilan-form-page .detail-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.15);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.9);
}

/* ============== ACTION BUTTONS ROW ============== */
.action-buttons-row {
    display: flex;
    justify-content: flex-start;
    gap: 10px;
    margin-bottom: 20px;
}

.btn-action-header {
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-secondary-header {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #e5e7eb;
}

.btn-secondary-header:hover {
    background: #e5e7eb;
}

/* ============== FORM CARD ============== */
.form-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    overflow: hidden;
}

.form-header {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    padding: 18px 25px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.form-header i {
    font-size: 20px;
    color: white;
}

.form-header h2 {
    margin: 0;
    color: white;
    font-size: 18px;
    font-weight: 600;
}

.form-body {
    padding: 25px;
}

/* ============== ALERTS ============== */
.alert {
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
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

/* ============== FORM ELEMENTS ============== */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 0;
}

.form-row.three-cols {
    grid-template-columns: 1fr 1fr 1fr;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
    font-size: 14px;
}

.form-group label i {
    color: #7c3aed;
    margin-right: 6px;
}

.form-hint {
    font-size: 12px;
    color: #9ca3af;
    margin: -5px 0 10px 0;
}

.required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #7c3aed;
    box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
}

.readonly-field {
    background: #f8fafc;
    color: #6b7280;
}

textarea.form-control {
    resize: vertical;
    min-height: 80px;
}

select.form-control {
    background: white;
    cursor: pointer;
}

/* ============== FOTO UPLOAD SECTION ============== */
.foto-upload-section {
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    border: 2px dashed #c4b5fd;
    border-radius: 12px;
    padding: 20px;
}

.foto-upload-wrapper {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.foto-preview-area {
    width: 200px;
    height: 150px;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    background: white;
    border: 2px solid #e5e7eb;
    cursor: pointer;
}

.foto-preview-area img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.foto-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.foto-preview-area:hover .foto-overlay {
    opacity: 1;
}

.btn-view-foto, .btn-remove-foto {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-view-foto {
    background: white;
    color: #7c3aed;
}

.btn-remove-foto {
    background: #ef4444;
    color: white;
}

.btn-view-foto:hover, .btn-remove-foto:hover {
    transform: scale(1.1);
}

.foto-placeholder-upload {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #7c3aed;
    transition: all 0.3s ease;
}

.foto-placeholder-upload:hover {
    background: #ede9fe;
}

.foto-placeholder-upload i {
    font-size: 36px;
}

.foto-placeholder-upload span {
    font-size: 12px;
    font-weight: 500;
}

.hidden-file-input {
    display: none;
}

.upload-info {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 10px 15px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.file-name {
    font-weight: 500;
    color: #374151;
}

.file-size {
    font-size: 12px;
    color: #10b981;
    background: #d1fae5;
    padding: 3px 8px;
    border-radius: 10px;
}

/* ============== FORM ACTIONS ============== */
.form-actions {
    display: flex;
    gap: 15px;
    justify-content: space-between;
    padding-top: 25px;
    border-top: 1px solid #e5e7eb;
    margin-top: 10px;
}

.btn-cancel, .btn-submit {
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    border: none;
}

.btn-cancel {
    background: #f3f4f6;
    color: #64748b;
}

.btn-cancel:hover {
    background: #e5e7eb;
}

.btn-submit {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(124, 58, 237, 0.4);
}

/* ============== PHOTO MODAL ============== */
.photo-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(8px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.photo-modal-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    animation: zoomIn 0.3s ease;
}

.photo-modal-content.dokumentasi-modal img {
    max-width: 100%;
    max-height: 80vh;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
}

@keyframes zoomIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.photo-modal-close {
    position: absolute;
    top: -15px;
    right: -15px;
    width: 40px;
    height: 40px;
    background: white;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 18px;
    color: #374151;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    z-index: 10001;
}

.photo-modal-close:hover {
    background: #ef4444;
    color: white;
}

.photo-modal-body { text-align: center; }

.photo-modal-image {
    width: 280px;
    height: 280px;
    border-radius: 50%;
    overflow: hidden;
    border: 5px solid white;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
    margin: 0 auto 20px;
}

.photo-modal-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-modal-image .modal-avatar-initial {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 100px;
    font-weight: 700;
    color: white;
}

.photo-modal-image .modal-avatar-initial.male {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.photo-modal-image .modal-avatar-initial.female {
    background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
}

.photo-modal-name {
    color: white;
    font-size: 1.5rem;
    font-weight: 600;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
}

/* ============== RESPONSIVE ============== */
@media (max-width: 768px) {
    .main-content.panggilan-form-page { padding: 15px; }
    
    .panggilan-form-page .bk-page-header {
        padding: 15px;
        border-radius: 12px;
    }
    
    .panggilan-form-page .header-content-wrapper {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 10px;
    }
    
    .panggilan-form-page .header-icon-box {
        width: 60px;
        height: 60px;
        font-size: 24px;
        border-radius: 50%;
    }
    
    .panggilan-form-page .header-info { text-align: center; }
    .panggilan-form-page .header-greeting .greeting-text { font-size: 11px; }
    .panggilan-form-page .header-greeting h1 { font-size: 16px; }
    .panggilan-form-page .header-details { justify-content: center; gap: 6px; }
    .panggilan-form-page .detail-badge { font-size: 9px; padding: 4px 8px; }
    
    .action-buttons-row { margin-bottom: 15px; }
    .btn-action-header { padding: 10px 15px; font-size: 12px; }
    
    .form-header { padding: 15px 18px; }
    .form-header h2 { font-size: 14px; }
    .form-body { padding: 18px; }
    
    .form-row, .form-row.three-cols {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .form-group { margin-bottom: 15px; }
    .form-group label { font-size: 13px; }
    .form-control { padding: 10px 12px; font-size: 13px; }
    
    .foto-upload-section { padding: 15px; }
    .foto-preview-area { width: 150px; height: 120px; }
    
    .form-actions {
        flex-direction: row;
        gap: 10px;
    }
    
    .btn-cancel, .btn-submit {
        flex: 1;
        justify-content: center;
        padding: 10px 15px;
        font-size: 12px;
    }
    
    .photo-modal-image { width: 200px; height: 200px; }
    .photo-modal-image .modal-avatar-initial { font-size: 60px; }
    .photo-modal-name { font-size: 1.2rem; }
}
</style>

<script>
// Photo Modal Functions
function showPhotoModal(nama, fotoUrl, initials, jk) {
    const modal = document.getElementById('photoModal');
    const imageContainer = document.getElementById('photoModalImage');
    const nameElement = document.getElementById('photoModalName');
    
    nameElement.textContent = nama;
    
    if (fotoUrl && fotoUrl.trim() !== '') {
        imageContainer.innerHTML = `<img src="${fotoUrl}" alt="${nama}">`;
    } else {
        const genderClass = jk === 'Laki-laki' || jk === 'L' ? 'male' : 'female';
        imageContainer.innerHTML = `<div class="modal-avatar-initial ${genderClass}">${initials}</div>`;
    }
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePhotoModal() {
    document.getElementById('photoModal').style.display = 'none';
    document.body.style.overflow = '';
}

// Dokumentasi Modal
function viewDokumentasi() {
    const currentImg = document.getElementById('currentFoto');
    if (currentImg) {
        document.getElementById('dokumentasiModalImage').src = currentImg.src;
        document.getElementById('dokumentasiModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeDokumentasiModal() {
    document.getElementById('dokumentasiModal').style.display = 'none';
    document.body.style.overflow = '';
}

// File Upload and Compression
const fotoPreviewArea = document.getElementById('fotoPreviewArea');
const fotoInput = document.getElementById('fotoInput');
const hapusFoto = document.getElementById('hapusFoto');
const uploadInfo = document.getElementById('uploadInfo');

// Click to upload
fotoPreviewArea.addEventListener('click', function(e) {
    if (!e.target.closest('.btn-view-foto') && !e.target.closest('.btn-remove-foto')) {
        fotoInput.click();
    }
});

// Handle file selection
fotoInput.addEventListener('change', async function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // Check if image
    if (!file.type.startsWith('image/')) {
        alert('File harus berupa gambar!');
        return;
    }
    
    // Compress image
    try {
        const compressedFile = await compressImage(file, 250); // Target 250KB
        
        // Create new file input with compressed image
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(compressedFile);
        fotoInput.files = dataTransfer.files;
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            fotoPreviewArea.innerHTML = `
                <img src="${e.target.result}" alt="Preview" id="currentFoto">
                <div class="foto-overlay">
                    <button type="button" class="btn-view-foto" onclick="viewDokumentasi()">
                        <i class="fas fa-search-plus"></i>
                    </button>
                    <button type="button" class="btn-remove-foto" onclick="removeFoto()">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        };
        reader.readAsDataURL(compressedFile);
        
        // Show file info
        const sizeKB = (compressedFile.size / 1024).toFixed(1);
        document.getElementById('fileName').textContent = compressedFile.name;
        document.getElementById('fileSize').textContent = sizeKB + ' KB';
        uploadInfo.style.display = 'flex';
        
        // Reset hapus flag
        hapusFoto.value = '0';
        
    } catch (error) {
        console.error('Compression error:', error);
        alert('Gagal mengkompresi gambar. Silakan coba gambar lain.');
    }
});

// Compress image function
async function compressImage(file, maxSizeKB) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function(e) {
            const img = new Image();
            img.src = e.target.result;
            img.onload = function() {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;
                
                // Max dimensions
                const maxDim = 1200;
                if (width > maxDim || height > maxDim) {
                    if (width > height) {
                        height = (height / width) * maxDim;
                        width = maxDim;
                    } else {
                        width = (width / height) * maxDim;
                        height = maxDim;
                    }
                }
                
                canvas.width = width;
                canvas.height = height;
                
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                
                // Try different quality levels
                let quality = 0.9;
                let blob;
                
                const attemptCompress = () => {
                    canvas.toBlob(function(b) {
                        blob = b;
                        
                        if (blob.size / 1024 > maxSizeKB && quality > 0.1) {
                            quality -= 0.1;
                            attemptCompress();
                        } else {
                            const compressedFile = new File([blob], file.name, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            resolve(compressedFile);
                        }
                    }, 'image/jpeg', quality);
                };
                
                attemptCompress();
            };
            img.onerror = reject;
        };
        reader.onerror = reject;
    });
}

// Remove foto
function removeFoto() {
    fotoPreviewArea.innerHTML = `
        <div class="foto-placeholder-upload" id="fotoPlaceholder">
            <i class="fas fa-cloud-upload-alt"></i>
            <span>Klik untuk upload foto</span>
        </div>
    `;
    fotoInput.value = '';
    hapusFoto.value = '1';
    uploadInfo.style.display = 'none';
}

// Close modals on overlay click
document.addEventListener('click', function(e) {
    if (e.target.id === 'photoModal') closePhotoModal();
    if (e.target.id === 'dokumentasiModal') closeDokumentasiModal();
});

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhotoModal();
        closeDokumentasiModal();
    }
});
</script>
@endsection
