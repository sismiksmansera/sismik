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
    @endphp
    
    <div class="bk-page-header">
        <div class="header-content-wrapper">
            <div class="header-icon-box clickable-avatar" onclick="showPhotoModal('{{ $siswa->nama }}', '{{ $foto_exists ? asset('storage/siswa/' . $siswa->foto) : '' }}', '{{ $initials ?: 'S' }}', '{{ $siswa->jk }}')" title="Lihat Foto">
                @if($foto_exists)
                    <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}" class="header-photo-img">
                @else
                    <i class="fas fa-envelope"></i>
                @endif
                <div class="avatar-overlay"><i class="fas fa-search-plus"></i></div>
            </div>
            <div class="header-info">
                <div class="header-greeting">
                    <span class="greeting-text">Buat Surat Panggilan</span>
                    <h1>{{ $siswa->nama }}</h1>
                </div>
                <div class="header-details">
                    <span class="detail-badge"><i class="fas fa-id-card"></i> {{ $siswa->nisn }}</span>
                    <span class="detail-badge"><i class="fas fa-chalkboard-teacher"></i> {{ $siswa->nama_rombel ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS ROW --}}
    <div class="action-buttons-row">
        <a href="{{ route('guru_bk.panggilan-ortu', $nisn) }}" class="btn-action-header btn-secondary-header">
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
            <h2>Form Surat Panggilan Orang Tua</h2>
        </div>
        
        <form method="POST" action="{{ route('guru_bk.panggilan-ortu.store', $nisn) }}" class="form-body">
            @csrf


            {{-- ROW: No Surat & Tanggal Surat --}}
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-hashtag"></i> Nomor Surat</label>
                    <input type="text" name="no_surat" class="form-control" 
                           value="{{ old('no_surat', 'SPO/' . date('Ymd') . '/' . rand(100, 999)) }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Tanggal Surat <span class="required">*</span></label>
                    <input type="date" name="tanggal_surat" class="form-control" required
                           value="{{ old('tanggal_surat', date('Y-m-d')) }}">
                </div>
            </div>

            {{-- Perihal --}}
            <div class="form-group">
                <label><i class="fas fa-clipboard"></i> Perihal <span class="required">*</span></label>
                <input type="text" name="perihal" class="form-control" required
                       placeholder="Contoh: Undangan Pertemuan Orang Tua/Wali Siswa"
                       value="{{ old('perihal') }}">
            </div>

            {{-- Alasan --}}
            <div class="form-group">
                <label><i class="fas fa-info-circle"></i> Alasan / Keterangan</label>
                <textarea name="alasan" class="form-control" rows="3" 
                          placeholder="Jelaskan alasan pemanggilan orang tua...">{{ old('alasan') }}</textarea>
            </div>

            {{-- Menghadap Ke --}}
            <div class="form-group">
                <label><i class="fas fa-user-tie"></i> Menghadap Ke</label>
                <input type="text" name="menghadap_ke" class="form-control readonly-field"
                       value="{{ $guruBK->nama }}" readonly>
            </div>

            {{-- ROW: Tanggal, Jam, Tempat --}}
            <div class="form-row three-cols">
                <div class="form-group">
                    <label><i class="fas fa-calendar-check"></i> Tanggal Panggilan <span class="required">*</span></label>
                    <input type="date" name="tanggal_panggilan" class="form-control" required
                           value="{{ old('tanggal_panggilan', date('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Jam Panggilan</label>
                    <input type="time" name="jam_panggilan" class="form-control"
                           value="{{ old('jam_panggilan', '09:00') }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Tempat</label>
                    <input type="text" name="tempat" class="form-control" placeholder="Ruang BK"
                           value="{{ old('tempat', 'Ruang BK') }}">
                </div>
            </div>

            {{-- Buttons --}}
            <div class="form-actions">
                <a href="{{ route('guru_bk.panggilan-ortu', $nisn) }}" class="btn-cancel">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Simpan Surat
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

/* ============== STUDENT INFO CARD ============== */
.student-info-card {
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
    border: 1px solid #e9d5ff;
}

.student-photo {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #7c3aed;
    flex-shrink: 0;
}

.student-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    font-weight: 700;
}

.student-name {
    font-weight: 700;
    color: #1f2937;
    font-size: 16px;
    margin-bottom: 5px;
}

.student-meta {
    display: flex;
    gap: 15px;
    font-size: 13px;
    color: #6b7280;
    flex-wrap: wrap;
}

.student-meta span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.student-meta i {
    color: #7c3aed;
}

.student-parents {
    font-size: 12px;
    color: #9ca3af;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.student-parents i {
    color: #7c3aed;
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
    min-height: 100px;
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
    
    .student-info-card {
        flex-direction: column;
        text-align: center;
        padding: 15px;
    }
    
    .student-meta { justify-content: center; }
    .student-parents { justify-content: center; }
    
    .form-row, .form-row.three-cols {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .form-group { margin-bottom: 15px; }
    .form-group label { font-size: 13px; }
    .form-control { padding: 10px 12px; font-size: 13px; }
    
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

// Close modal on overlay click
document.addEventListener('click', function(e) {
    if (e.target.id === 'photoModal') {
        closePhotoModal();
    }
});

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhotoModal();
    }
});
</script>
@endsection
