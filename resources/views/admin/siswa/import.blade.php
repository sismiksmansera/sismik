@extends('layouts.app')

@section('title', 'Import Data Siswa | SISMIK')

@push('styles')
<style>
    .content-header {
        background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
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
    
    .import-card {
        background: var(--white);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        max-width: 700px;
    }
    
    .upload-zone {
        border: 3px dashed var(--gray-300);
        border-radius: 16px;
        padding: 50px 30px;
        text-align: center;
        background: var(--gray-50);
        transition: all 0.3s;
        cursor: pointer;
        margin-bottom: 30px;
    }
    .upload-zone:hover {
        border-color: #8B5CF6;
        background: rgba(139, 92, 246, 0.05);
    }
    .upload-zone.dragover {
        border-color: #8B5CF6;
        background: rgba(139, 92, 246, 0.1);
    }
    .upload-zone i {
        font-size: 48px;
        color: #8B5CF6;
        margin-bottom: 16px;
    }
    .upload-zone h3 {
        font-size: 18px;
        margin-bottom: 8px;
        color: var(--gray-700);
    }
    .upload-zone p {
        color: var(--gray-500);
        font-size: 14px;
    }
    
    .file-info {
        background: #ddd6fe;
        padding: 16px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: none;
    }
    .file-info.show { display: flex; align-items: center; gap: 12px; }
    .file-info i { font-size: 24px; color: #7c3aed; }
    .file-info .file-name { font-weight: 600; color: #5b21b6; }
    .file-info .file-size { font-size: 12px; color: #6b7280; }
    
    .info-box {
        background: var(--gray-100);
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 24px;
    }
    .info-box h4 {
        font-size: 15px;
        margin-bottom: 12px;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-box h4 i { color: #8B5CF6; }
    .info-box ul {
        margin: 0;
        padding-left: 20px;
        color: var(--gray-600);
        font-size: 13px;
    }
    .info-box li { margin-bottom: 6px; }
    
    .template-box {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        padding: 20px;
        border-radius: 12px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }
    .template-box i { font-size: 32px; opacity: 0.8; }
    .template-box .template-text h4 { margin: 0 0 4px; }
    .template-box .template-text p { margin: 0; opacity: 0.9; font-size: 13px; }
    
    .btn-group-action {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--gray-200);
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
                <i class="fas fa-file-import"></i>
            </div>
            <div class="header-text">
                <h1>Import Data Siswa</h1>
                <p>Import data siswa dari file CSV</p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success" style="background: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle" style="color: #10b981;"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="import-card">
            <form action="{{ route('admin.siswa.import.process') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf

                <!-- Template Download -->
                <div class="template-box">
                    <div class="template-text">
                        <h4><i class="fas fa-download"></i> Download Template</h4>
                        <p>Gunakan template CSV yang sudah disediakan</p>
                    </div>
                    <a href="#" class="btn btn-light" style="color: #059669;">
                        <i class="fas fa-file-csv"></i> Download
                    </a>
                </div>

                <!-- Upload Zone -->
                <div class="upload-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <h3>Drag & Drop file CSV di sini</h3>
                    <p>atau klik untuk memilih file</p>
                    <p style="margin-top: 10px;"><strong>Format: .csv</strong> | Maksimal: 5MB</p>
                </div>
                <input type="file" name="file_csv" id="fileInput" accept=".csv" style="display: none;">

                <!-- File Info (shown after selection) -->
                <div class="file-info" id="fileInfo">
                    <i class="fas fa-file-csv"></i>
                    <div>
                        <div class="file-name" id="fileName">-</div>
                        <div class="file-size" id="fileSize">-</div>
                    </div>
                    <button type="button" onclick="removeFile()" style="margin-left: auto; background: none; border: none; color: #ef4444; cursor: pointer;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Info Box -->
                <div class="info-box">
                    <h4><i class="fas fa-info-circle"></i> Format Kolom CSV</h4>
                    <ul>
                        <li><strong>Kolom 1:</strong> NISN (wajib, unik)</li>
                        <li><strong>Kolom 2:</strong> NIS</li>
                        <li><strong>Kolom 3:</strong> Nama Lengkap (wajib)</li>
                        <li><strong>Kolom 4:</strong> Jenis Kelamin (Laki-laki/Perempuan)</li>
                        <li><strong>Kolom 5:</strong> Agama</li>
                        <li><strong>Kolom 6:</strong> Tempat Lahir</li>
                        <li><strong>Kolom 7:</strong> Tanggal Lahir (format: YYYY-MM-DD)</li>
                        <li><strong>Kolom 8:</strong> Angkatan Masuk</li>
                    </ul>
                </div>

                <div class="info-box" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
                    <h4 style="color: #92400e;"><i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i> Catatan Penting</h4>
                    <ul style="color: #92400e;">
                        <li>Password default untuk siswa baru adalah <strong>NISN</strong></li>
                        <li>Data dengan NISN yang sudah ada akan dilewati</li>
                        <li>Pastikan menggunakan format CSV dengan separator <strong>koma (,)</strong></li>
                        <li>Baris pertama harus berupa header kolom</li>
                    </ul>
                </div>

                <div class="btn-group-action">
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #8B5CF6, #7C3AED); color: white;" id="submitBtn" disabled>
                        <i class="fas fa-upload"></i> Import Data
                    </button>
                </div>
            </form>
        </div>

        <!-- Periodic Data Import Section -->
        <div class="import-card" style="margin-top: 30px;">
            <h2 style="margin-bottom: 20px; font-size: 20px; color: var(--gray-700);">
                <i class="fas fa-sync-alt" style="color: #f59e0b;"></i> Import Data Periodik
            </h2>
            <p style="color: var(--gray-600); margin-bottom: 20px;">
                Update data Rombel, Guru BK, dan Guru Wali untuk periode aktif saat ini.
            </p>

            <!-- Download Template -->
            <div class="template-box" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="template-text">
                    <h4><i class="fas fa-download"></i> Download Template Data Periodik</h4>
                    <p>Download data siswa aktif dengan kolom Rombel, Guru BK, Guru Wali</p>
                </div>
                <a href="{{ route('admin.siswa.import.periodic-template') }}" class="btn btn-light" style="color: #d97706;">
                    <i class="fas fa-file-csv"></i> Download
                </a>
            </div>

            <form action="{{ route('admin.siswa.import.periodic') }}" method="POST" enctype="multipart/form-data" id="periodicImportForm">
                @csrf

                <!-- Upload Zone for Periodic Data -->
                <div class="upload-zone" id="dropZonePeriodic" onclick="document.getElementById('fileInputPeriodic').click()" style="border-color: #f59e0b;">
                    <i class="fas fa-cloud-upload-alt" style="color: #f59e0b;"></i>
                    <h3>Drag & Drop file CSV Data Periodik</h3>
                    <p>atau klik untuk memilih file</p>
                    <p style="margin-top: 10px;"><strong>Format: .csv</strong> | Maksimal: 5MB</p>
                </div>
                <input type="file" name="file_periodic" id="fileInputPeriodic" accept=".csv" style="display: none;">

                <!-- File Info for Periodic -->
                <div class="file-info" id="fileInfoPeriodic" style="background: #fef3c7;">
                    <i class="fas fa-file-csv" style="color: #d97706;"></i>
                    <div>
                        <div class="file-name" id="fileNamePeriodic" style="color: #92400e;">-</div>
                        <div class="file-size" id="fileSizePeriodic">-</div>
                    </div>
                    <button type="button" onclick="removeFilePeriodic()" style="margin-left: auto; background: none; border: none; color: #ef4444; cursor: pointer;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Info Box -->
                <div class="info-box">
                    <h4><i class="fas fa-info-circle"></i> Format Kolom Template</h4>
                    <ul>
                        <li><strong>Kolom 1:</strong> Nama (hanya untuk referensi)</li>
                        <li><strong>Kolom 2:</strong> NISN (wajib, untuk identifikasi)</li>
                        <li><strong>Kolom 3:</strong> Tahun Pelajaran (hanya untuk referensi)</li>
                        <li><strong>Kolom 4:</strong> Semester (1-6, menentukan kolom yang diupdate)</li>
                        <li><strong>Kolom 5:</strong> Rombel (akan diupdate)</li>
                        <li><strong>Kolom 6:</strong> Guru BK (akan diupdate)</li>
                        <li><strong>Kolom 7:</strong> Guru Wali (akan diupdate)</li>
                    </ul>
                </div>

                <div class="info-box" style="background: #dbeafe; border-left: 4px solid #3b82f6;">
                    <h4 style="color: #1e40af;"><i class="fas fa-lightbulb" style="color: #3b82f6;"></i> Cara Penggunaan</h4>
                    <ul style="color: #1e40af;">
                        <li>Download template → Isi kolom Rombel, Guru BK, Guru Wali</li>
                        <li>Jangan ubah kolom Nama, NISN, Tahun Pelajaran, Semester</li>
                        <li>Upload kembali file yang sudah diisi</li>
                        <li>Sistem akan mengupdate kolom sesuai semester yang tertera</li>
                    </ul>
                </div>

                <div class="btn-group-action">
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;" id="submitBtnPeriodic" disabled>
                        <i class="fas fa-upload"></i> Import Data Periodik
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const submitBtn = document.getElementById('submitBtn');

    // Drag and drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            showFileInfo(e.dataTransfer.files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', function() {
        if (this.files.length) {
            showFileInfo(this.files[0]);
        }
    });

    function showFileInfo(file) {
        if (!file.name.endsWith('.csv')) {
            alert('Hanya file CSV yang diperbolehkan!');
            fileInput.value = '';
            return;
        }

        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(2) + ' KB';
        fileInfo.classList.add('show');
        dropZone.style.display = 'none';
        submitBtn.disabled = false;
    }

    function removeFile() {
        fileInput.value = '';
        fileInfo.classList.remove('show');
        dropZone.style.display = 'block';
        submitBtn.disabled = true;
    }

    // ==========================================
    // PERIODIC DATA IMPORT HANDLERS
    // ==========================================
    const dropZonePeriodic = document.getElementById('dropZonePeriodic');
    const fileInputPeriodic = document.getElementById('fileInputPeriodic');
    const fileInfoPeriodic = document.getElementById('fileInfoPeriodic');
    const submitBtnPeriodic = document.getElementById('submitBtnPeriodic');

    // Drag and drop for periodic
    dropZonePeriodic.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZonePeriodic.classList.add('dragover');
    });
    dropZonePeriodic.addEventListener('dragleave', () => {
        dropZonePeriodic.classList.remove('dragover');
    });
    dropZonePeriodic.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZonePeriodic.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            fileInputPeriodic.files = e.dataTransfer.files;
            showFileInfoPeriodic(e.dataTransfer.files[0]);
        }
    });

    // File input change for periodic
    fileInputPeriodic.addEventListener('change', function() {
        if (this.files.length) {
            showFileInfoPeriodic(this.files[0]);
        }
    });

    function showFileInfoPeriodic(file) {
        if (!file.name.endsWith('.csv')) {
            alert('Hanya file CSV yang diperbolehkan!');
            fileInputPeriodic.value = '';
            return;
        }

        document.getElementById('fileNamePeriodic').textContent = file.name;
        document.getElementById('fileSizePeriodic').textContent = (file.size / 1024).toFixed(2) + ' KB';
        fileInfoPeriodic.classList.add('show');
        dropZonePeriodic.style.display = 'none';
        submitBtnPeriodic.disabled = false;
    }

    function removeFilePeriodic() {
        fileInputPeriodic.value = '';
        fileInfoPeriodic.classList.remove('show');
        dropZonePeriodic.style.display = 'block';
        submitBtnPeriodic.disabled = true;
    }
</script>
@endpush
