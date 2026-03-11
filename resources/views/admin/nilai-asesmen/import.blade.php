@extends('layouts.app')

@section('title', 'Import Nilai Asesmen Sekolah')

@push('styles')
<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}
.page-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    color: #1e293b;
}
.filter-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}
.btn-import {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
}
.btn-import:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(245, 87, 108, 0.4);
    color: white;
}
.btn-import:disabled {
    opacity: 0.6;
    transform: none;
    cursor: not-allowed;
}
.btn-back {
    background: #f1f5f9;
    color: #475569;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}
.btn-back:hover {
    background: #e2e8f0;
    color: #1e293b;
}
.upload-area {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    transition: all 0.3s;
    cursor: pointer;
    position: relative;
}
.upload-area:hover, .upload-area.dragover {
    border-color: #f5576c;
    background: #fff5f7;
}
.upload-area i {
    font-size: 2.5rem;
    color: #94a3b8;
    margin-bottom: 10px;
}
.upload-area.has-file i {
    color: #22c55e;
}
.upload-area input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}
.file-name {
    color: #1e293b;
    font-weight: 600;
    margin-top: 10px;
}
.instructions {
    background: #f8f9fa;
    border-left: 4px solid #f5576c;
    padding: 15px;
    border-radius: 8px;
    margin-top: 20px;
}
.loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}
.loading-overlay.active {
    display: flex;
}
.loading-box {
    background: white;
    border-radius: 12px;
    padding: 30px 40px;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}
.loading-box .spinner-border {
    width: 3rem;
    height: 3rem;
    color: #f5576c;
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <div class="container-fluid px-4">
            <!-- Header -->
            <div class="page-header">
                <div>
                    <h1><i class="fas fa-file-upload text-danger"></i> Import Nilai Asesmen Sekolah</h1>
                    <p class="text-muted mb-0">Upload file Excel template yang sudah diisi nilai</p>
                </div>
                <a href="{{ route('admin.nilai-asesmen.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Import Form -->
            <div class="filter-card">
                <h5 class="mb-3"><i class="fas fa-upload"></i> Upload File Excel</h5>
                <form action="{{ route('admin.nilai-asesmen.import.process') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf

                    <div class="upload-area" id="uploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p class="text-muted mb-0">Klik atau seret file Excel ke sini</p>
                        <small class="text-muted">Format: .xlsx atau .xls | Maks: 5MB</small>
                        <div class="file-name" id="fileName" style="display:none"></div>
                        <input type="file" name="file" id="fileInput" accept=".xlsx,.xls" required>
                    </div>

                    <div class="mt-4 text-center">
                        <button type="submit" class="btn-import" id="btnImport" disabled>
                            <i class="fas fa-upload"></i> Import Nilai Asesmen
                        </button>
                    </div>
                </form>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h6><i class="fas fa-info-circle"></i> Petunjuk Import:</h6>
                <ol class="mb-0">
                    <li>Pastikan file Excel yang diupload menggunakan <strong>template yang sudah didownload</strong> dari halaman Download Format</li>
                    <li>Pastikan kolom dalam file sesuai urutan: <strong>No, Jenis Asesmen, Semester, Tahun Pelajaran, Rombel, Mata Pelajaran, Nama Siswa, NISN, Nilai</strong></li>
                    <li>Sistem akan <strong>memvalidasi NISN</strong> siswa terhadap database:
                        <ul>
                            <li><i class="fas fa-check text-success"></i> NISN yang valid → data nilai akan diimport</li>
                            <li><i class="fas fa-times text-danger"></i> NISN yang tidak ditemukan → baris akan <strong>dilewati</strong></li>
                        </ul>
                    </li>
                    <li>Jika data dengan kombinasi yang sama sudah ada, data akan <strong>diperbarui</strong> (bukan duplikasi)</li>
                    <li>Setelah import selesai, Anda akan diarahkan ke halaman tabel untuk melihat hasilnya</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-box">
        <div class="spinner-border mb-3" role="status"></div>
        <p class="mb-0 fw-bold">Sedang mengimport data...</p>
        <small class="text-muted">Mohon tunggu, jangan tutup halaman ini</small>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const uploadArea = document.getElementById('uploadArea');
    const fileName = document.getElementById('fileName');
    const btnImport = document.getElementById('btnImport');
    const importForm = document.getElementById('importForm');
    const loadingOverlay = document.getElementById('loadingOverlay');

    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileName.textContent = this.files[0].name;
            fileName.style.display = 'block';
            uploadArea.classList.add('has-file');
            btnImport.disabled = false;
        } else {
            fileName.style.display = 'none';
            uploadArea.classList.remove('has-file');
            btnImport.disabled = true;
        }
    });

    // Drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    uploadArea.addEventListener('dragleave', function() {
        this.classList.remove('dragover');
    });
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });

    // Loading overlay on submit
    importForm.addEventListener('submit', function() {
        loadingOverlay.classList.add('active');
        btnImport.disabled = true;
    });
});
</script>
@endpush
