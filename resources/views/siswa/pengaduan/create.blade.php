@extends('layouts.app')

@section('title', 'Buat Pengaduan | SISMIK')

@push('styles')
<style>
    /* Header Card */
    .pengaduan-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 30px;
        color: white;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .pengaduan-header-icon {
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.2);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
    }
    .pengaduan-header-text h1 { margin: 0 0 10px 0; font-size: 1.75rem; font-weight: 700; }
    .pengaduan-header-text p { margin: 0; opacity: 0.9; font-size: 0.95rem; }

    /* Info Card */
    .info-card {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #3b82f6;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }
    .info-icon {
        width: 40px;
        height: 40px;
        background: #3b82f6;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        flex-shrink: 0;
    }
    .info-content h4 { margin: 0 0 8px 0; color: #1e40af; font-weight: 700; }
    .info-content ul { margin: 0; padding-left: 18px; color: #1e3a8a; font-size: 0.9rem; line-height: 1.8; }

    /* Form Card */
    .form-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px 25px;
        color: white;
    }
    .form-header h3 { margin: 0; font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 10px; }
    .form-body { padding: 30px; }
    
    .form-group { margin-bottom: 25px; }
    .form-group label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }
    .form-group label i { color: #667eea; margin-right: 8px; }
    .form-group label .required { color: #ef4444; }
    
    .form-control {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }
    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    textarea.form-control { resize: vertical; min-height: 120px; font-family: inherit; }
    .form-hint { color: #6b7280; font-size: 0.8rem; margin-top: 5px; }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    /* File Upload */
    .file-upload {
        border: 2px dashed #d1d5db;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        background: #f9fafb;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .file-upload:hover { border-color: #667eea; background: #f0f4ff; }
    .file-upload i { font-size: 40px; color: #9ca3af; margin-bottom: 10px; }
    .file-upload p { margin: 0; color: #6b7280; font-size: 0.9rem; }
    .file-upload .hint { margin: 5px 0 0; color: #9ca3af; font-size: 0.8rem; }
    .file-upload input { display: none; }
    .file-name { margin-top: 10px; color: #667eea; font-weight: 600; display: none; }

    /* Buttons */
    .form-actions { display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px; }
    .btn {
        padding: 14px 30px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    .btn-secondary { background: #6b7280; color: white; text-decoration: none; }
    .btn-secondary:hover { background: #4b5563; transform: translateY(-2px); }
    .btn-primary { background: linear-gradient(135deg, #667eea, #764ba2); color: white; box-shadow: 0 4px 15px rgba(102,126,234,0.3); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102,126,234,0.4); }

    /* Validation */
    .is-invalid { border-color: #ef4444 !important; }
    .invalid-feedback { color: #ef4444; font-size: 0.85rem; margin-top: 5px; }

    @media (max-width: 768px) {
        .form-actions { flex-direction: column; }
        .btn { width: 100%; justify-content: center; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-siswa')

    <div class="main-content">
        <!-- Header -->
        <div class="pengaduan-header">
            <div class="pengaduan-header-icon">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="pengaduan-header-text">
                <h1>Form Pengaduan</h1>
                <p>Sampaikan laporan atau pengaduan terkait permasalahan yang terjadi di sekolah. Identitas pelapor akan dijaga kerahasiaannya.</p>
            </div>
        </div>

        <!-- Info Card -->
        <div class="info-card">
            <div class="info-icon"><i class="fas fa-info"></i></div>
            <div class="info-content">
                <h4>Informasi Penting</h4>
                <ul>
                    <li>Pengaduan akan ditindaklanjuti dalam waktu <strong>1-3 hari kerja</strong></li>
                    <li>Identitas pelapor <strong>dijamin kerahasiaannya</strong></li>
                    <li>Sertakan bukti pendukung jika ada (foto, dokumen)</li>
                    <li>Anda dapat memantau status pengaduan di halaman riwayat</li>
                </ul>
            </div>
        </div>

        <!-- Form -->
        <div class="form-card">
            <div class="form-header">
                <h3><i class="fas fa-edit"></i> Formulir Pengaduan</h3>
            </div>
            <div class="form-body">
                <form method="POST" action="{{ route('siswa.pengaduan.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label><i class="fas fa-folder"></i> Kategori Pengaduan <span class="required">*</span></label>
                        <select name="kategori" class="form-control @error('kategori') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Sarana Prasarana" {{ old('kategori') == 'Sarana Prasarana' ? 'selected' : '' }}>🏫 Sarana Prasarana</option>
                            <option value="Kekerasan" {{ old('kategori') == 'Kekerasan' ? 'selected' : '' }}>⚠️ Kekerasan</option>
                            <option value="Bullying" {{ old('kategori') == 'Bullying' ? 'selected' : '' }}>😢 Bullying</option>
                            <option value="Pelanggaran Aturan" {{ old('kategori') == 'Pelanggaran Aturan' ? 'selected' : '' }}>📋 Pelanggaran Aturan</option>
                            <option value="Kegiatan Pembelajaran" {{ old('kategori') == 'Kegiatan Pembelajaran' ? 'selected' : '' }}>📚 Kegiatan Pembelajaran</option>
                            <option value="Pelayanan Sekolah" {{ old('kategori') == 'Pelayanan Sekolah' ? 'selected' : '' }}>🏢 Pelayanan Sekolah</option>
                            <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>📝 Lainnya</option>
                        </select>
                        @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-user-tag"></i> Subyek/Obyek yang Dilaporkan <span class="required">*</span></label>
                        <input type="text" name="subyek_terlapor" class="form-control @error('subyek_terlapor') is-invalid @enderror" 
                               value="{{ old('subyek_terlapor') }}" placeholder="Contoh: Nama orang, fasilitas, atau hal yang dilaporkan" required>
                        <div class="form-hint">Tuliskan nama orang/siswa/guru atau fasilitas yang menjadi obyek pengaduan</div>
                        @error('subyek_terlapor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Tanggal Kejadian <span class="required">*</span></label>
                            <input type="date" name="tanggal_kejadian" class="form-control @error('tanggal_kejadian') is-invalid @enderror" 
                                   value="{{ old('tanggal_kejadian', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                            @error('tanggal_kejadian')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-clock"></i> Waktu Kejadian (Opsional)</label>
                            <input type="time" name="waktu_kejadian" class="form-control" value="{{ old('waktu_kejadian') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Lokasi Kejadian (Opsional)</label>
                        <input type="text" name="lokasi_kejadian" class="form-control" value="{{ old('lokasi_kejadian') }}"
                               placeholder="Contoh: Ruang kelas X IPA 1, Kantin, Toilet lantai 2, dll">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-align-left"></i> Deskripsi Pengaduan <span class="required">*</span></label>
                        <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="5"
                                  placeholder="Jelaskan kronologi kejadian secara detail..." required>{{ old('deskripsi') }}</textarea>
                        <div class="form-hint">Jelaskan secara kronologis: apa yang terjadi, siapa yang terlibat, bagaimana kejadiannya</div>
                        @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-paperclip"></i> Bukti Pendukung (Opsional)</label>
                        <div class="file-upload" onclick="document.getElementById('bukti_input').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Klik untuk upload atau drag & drop</p>
                            <p class="hint">Format: JPG, PNG, GIF, PDF (Maks. 5MB)</p>
                            <input type="file" id="bukti_input" name="bukti_pendukung" accept=".jpg,.jpeg,.png,.gif,.pdf" onchange="showFileName(this)">
                            <div id="file_name" class="file-name"></div>
                        </div>
                        @error('bukti_pendukung')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('siswa.pengaduan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Kirim Pengaduan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showFileName(input) {
    const fileNameDiv = document.getElementById('file_name');
    if (input.files && input.files[0]) {
        fileNameDiv.textContent = '📎 ' + input.files[0].name;
        fileNameDiv.style.display = 'block';
    } else {
        fileNameDiv.style.display = 'none';
    }
}
</script>
@endpush
