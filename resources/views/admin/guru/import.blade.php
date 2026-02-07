@extends('layouts.app')

@section('title', 'Import Data Guru | SISMIK')

@push('styles')
<style>
    .content-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
        margin-bottom: 30px;
    }
    
    .import-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--gray-100);
    }
    .import-card-title i {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
    }
    .import-card-title h3 {
        margin: 0;
        font-size: 18px;
        color: var(--gray-800);
    }
    .import-card-title p {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--gray-500);
    }
    
    .upload-zone {
        border: 3px dashed var(--gray-300);
        border-radius: 16px;
        padding: 50px 30px;
        text-align: center;
        background: var(--gray-50);
        transition: all 0.3s;
        cursor: pointer;
        margin-bottom: 20px;
    }
    .upload-zone:hover {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.05);
    }
    .upload-zone.dragover {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.1);
    }
    .upload-zone i {
        font-size: 48px;
        color: #10b981;
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
        background: #d1fae5;
        padding: 16px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: none;
    }
    .file-info.show { display: flex; align-items: center; gap: 12px; }
    .file-info i { font-size: 24px; color: #059669; }
    .file-info .file-name { font-weight: 600; color: #065f46; }
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
    .info-box h4 i { color: #10b981; }
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
    
    .success-message {
        background: #d1fae5;
        border: 1px solid #10b981;
        color: #065f46;
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .success-message i { font-size: 20px; }
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
                <h1>Import Data Guru</h1>
                <p>Download dan import data guru dari file Excel</p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 20px; background: #fef2f2; border: 1px solid #ef4444; color: #b91c1c; padding: 15px 20px; border-radius: 10px;">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Download Section -->
        <div class="import-card">
            <div class="import-card-title">
                <i class="fas fa-download"></i>
                <div>
                    <h3>Download Data Guru</h3>
                    <p>Download semua data guru dalam format Excel untuk diedit</p>
                </div>
            </div>

            <div class="template-box">
                <div class="template-text">
                    <h4><i class="fas fa-file-excel"></i> Download Data Guru</h4>
                    <p>File Excel berisi semua data guru yang dapat diedit</p>
                </div>
                <a href="{{ route('admin.guru.export') }}" class="btn btn-light" style="color: #059669;">
                    <i class="fas fa-file-excel"></i> Download
                </a>
            </div>

            <div class="info-box">
                <h4><i class="fas fa-info-circle"></i> Format Kolom Excel</h4>
                <ul>
                    <li><strong>Kolom A:</strong> ID (jangan diubah, untuk identifikasi)</li>
                    <li><strong>Kolom B:</strong> NIP</li>
                    <li><strong>Kolom C:</strong> Nama</li>
                    <li><strong>Kolom D:</strong> Jenis Kelamin (L/P)</li>
                    <li><strong>Kolom E:</strong> No HP</li>
                    <li><strong>Kolom F:</strong> Email</li>
                    <li><strong>Kolom G:</strong> Alamat</li>
                    <li><strong>Kolom H:</strong> Status Kepegawaian</li>
                    <li><strong>Kolom I:</strong> Golongan</li>
                    <li><strong>Kolom J:</strong> Status (Aktif/Nonaktif)</li>
                </ul>
            </div>
        </div>

        <!-- Upload Section -->
        <div class="import-card">
            <div class="import-card-title">
                <i class="fas fa-upload" style="background: linear-gradient(135deg, #f59e0b, #d97706);"></i>
                <div>
                    <h3>Upload Data Guru</h3>
                    <p>Upload file Excel yang sudah diedit untuk update data guru</p>
                </div>
            </div>

            <form action="{{ route('admin.guru.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                
                <div class="upload-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-cloud-upload-alt" style="color: #f59e0b;"></i>
                    <h3>Drag & Drop file Excel</h3>
                    <p>atau klik untuk memilih file</p>
                    <p style="margin-top: 10px;"><strong>Format: .xlsx</strong> | Maksimal: 5MB</p>
                </div>
                <input type="file" name="file_guru" id="fileInput" accept=".xlsx,.xls" style="display: none;">

                <div class="file-info" id="fileInfo">
                    <i class="fas fa-file-excel" style="color: #f59e0b;"></i>
                    <div>
                        <div class="file-name" id="fileName" style="color: #92400e;">-</div>
                        <div class="file-size" id="fileSize">-</div>
                    </div>
                    <button type="button" onclick="removeFile()" style="margin-left: auto; background: none; border: none; color: #ef4444; cursor: pointer;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="info-box" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
                    <h4 style="color: #92400e;"><i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i> Perhatian</h4>
                    <ul style="color: #92400e;">
                        <li>Jangan mengubah kolom <strong>ID</strong>, karena digunakan untuk identifikasi data</li>
                        <li>Pastikan format data sesuai dengan ketentuan</li>
                        <li>Import akan <strong>mengupdate</strong> data yang sudah ada (berdasarkan ID)</li>
                    </ul>
                </div>

                <div class="btn-group-action">
                    <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;" id="submitBtn" disabled>
                        <i class="fas fa-upload"></i> Import Data
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

// Drag and drop handlers
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
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        showFileInfo(files[0]);
    }
});

fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
        showFileInfo(fileInput.files[0]);
    }
});

function showFileInfo(file) {
    if (!file.name.endsWith('.xlsx') && !file.name.endsWith('.xls')) {
        alert('Hanya file Excel (.xlsx/.xls) yang diperbolehkan!');
        fileInput.value = '';
        return;
    }

    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(2) + ' KB';
    fileInfo.classList.add('show');
    fileInfo.style.background = '#fef3c7';
    dropZone.style.display = 'none';
    submitBtn.disabled = false;
}

function removeFile() {
    fileInput.value = '';
    fileInfo.classList.remove('show');
    dropZone.style.display = 'block';
    submitBtn.disabled = true;
}
</script>
@endpush
