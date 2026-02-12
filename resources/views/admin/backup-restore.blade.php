@extends('layouts.app')

@section('title', 'Backup & Restore | SISMIK')

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
        width: 60px; height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
    }
    .header-text h1 { font-size: 24px; margin: 0 0 5px; }
    .header-text p { margin: 0; opacity: 0.85; font-size: 14px; }

    .backup-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 30px;
    }

    .backup-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
    }

    .card-icon {
        width: 56px; height: 56px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; color: white;
        margin-bottom: 20px;
    }

    .card-icon.db { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .card-icon.storage { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .card-icon.restore { background: linear-gradient(135deg, #f59e0b, #d97706); }

    .backup-card h3 {
        font-size: 18px;
        margin: 0 0 6px;
        color: var(--dark);
    }

    .backup-card .subtitle {
        color: var(--gray-500);
        font-size: 13px;
        margin: 0 0 16px;
    }

    .backup-card .description {
        color: var(--gray-500);
        font-size: 13px;
        line-height: 1.6;
        margin: 0 0 20px;
        flex: 1;
    }

    .backup-card .description i { color: var(--primary); }

    .btn-backup {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        color: white;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        width: 100%;
    }
    .btn-backup:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
    .btn-backup.green { background: linear-gradient(135deg, #10b981, #059669); }
    .btn-backup.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .btn-backup.amber { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .btn-backup:disabled { opacity: 0.5; transform: none; cursor: not-allowed; }

    .drop-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        margin-bottom: 14px;
        transition: all 0.3s;
    }
    .drop-zone:hover { border-color: #f59e0b; background: #fffbeb; }
    .drop-zone i { font-size: 24px; color: #94a3b8; }
    .drop-zone p { margin: 8px 0 0; color: #64748b; font-size: 13px; }

    .info-section {
        background: white;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .info-section h3 {
        font-size: 16px;
        margin: 0 0 16px;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .info-section h3 i { color: var(--primary); }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .info-item {
        background: #f8fafc;
        padding: 16px;
        border-radius: 10px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .info-item .num {
        width: 28px; height: 28px;
        background: var(--primary);
        color: white;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700;
        flex-shrink: 0;
    }

    .info-item h4 { margin: 0 0 4px; font-size: 14px; color: var(--dark); }
    .info-item p { margin: 0; font-size: 12px; color: var(--gray-500); line-height: 1.5; }

    .alert {
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }
    .alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }

    @media (max-width: 1024px) {
        .backup-grid { grid-template-columns: 1fr; }
        .info-grid { grid-template-columns: 1fr; }
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
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="header-text">
                <h1>Backup & Restore</h1>
                <p>Kelola cadangan database dan file upload aplikasi</p>
            </div>
        </div>

        @if($errors->has('backup'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first('backup') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Backup Cards -->
        <div class="backup-grid">
            <!-- Database Backup -->
            <div class="backup-card">
                <div class="card-icon db">
                    <i class="fas fa-database"></i>
                </div>
                <h3>Backup Database</h3>
                <p class="subtitle">{{ $dbName }}</p>
                <p class="description">
                    <i class="fas fa-info-circle"></i> Mengunduh seluruh data database termasuk struktur tabel, data, trigger, dan routines dalam format <strong>.sql</strong>
                </p>
                <a href="{{ route('admin.backup.database') }}" 
                   onclick="return confirm('Download backup database sekarang?')"
                   class="btn-backup green">
                    <i class="fas fa-download"></i> Download Database (.sql)
                </a>
            </div>

            <!-- Storage Backup -->
            <div class="backup-card">
                <div class="card-icon storage">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3>Backup File Upload</h3>
                <p class="subtitle">storage/app/public</p>
                <p class="description">
                    <i class="fas fa-info-circle"></i> Mengunduh semua file upload: foto guru, foto siswa, logo sekolah, background login, dan dokumen lainnya dalam format <strong>.zip</strong>
                </p>
                <a href="{{ route('admin.backup.storage') }}" 
                   onclick="return confirm('Download backup file upload? Proses ini mungkin memakan waktu.')"
                   class="btn-backup purple">
                    <i class="fas fa-download"></i> Download Storage (.zip)
                </a>
            </div>

            <!-- Restore Storage -->
            <div class="backup-card">
                <div class="card-icon restore">
                    <i class="fas fa-upload"></i>
                </div>
                <h3>Restore File Upload</h3>
                <p class="subtitle">Upload file .zip backup</p>
                <p class="description">
                    <i class="fas fa-info-circle"></i> Upload file <strong>.zip</strong> hasil backup storage untuk memulihkan foto dan dokumen. File yang sama akan ditimpa. Maks <strong>200MB</strong>.
                </p>
                <form action="{{ route('admin.backup.restore-storage') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirmRestore()">
                    @csrf
                    <input type="file" name="storage_zip" id="restoreZipInput" accept=".zip" style="display: none;" onchange="updateFileName(this)">
                    <div class="drop-zone" onclick="document.getElementById('restoreZipInput').click()">
                        <i class="fas fa-file-archive"></i>
                        <p id="restoreFileName">Klik untuk pilih file .zip</p>
                    </div>
                    <button type="submit" class="btn-backup amber" id="restoreBtn" disabled>
                        <i class="fas fa-upload"></i> Restore Storage
                    </button>
                </form>
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <h3><i class="fas fa-lightbulb"></i> Panduan Backup & Restore</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="num">1</div>
                    <div>
                        <h4>Backup Rutin</h4>
                        <p>Lakukan backup database dan storage secara berkala, terutama sebelum update aplikasi atau perubahan besar.</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="num">2</div>
                    <div>
                        <h4>Restore Database</h4>
                        <p>File .sql dapat di-restore melalui halaman Setup/Installer saat konfigurasi awal, atau melalui phpMyAdmin.</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="num">3</div>
                    <div>
                        <h4>Restore Storage</h4>
                        <p>Upload file .zip hasil backup untuk mengembalikan foto dan dokumen. File lama akan ditimpa jika namanya sama.</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="num">4</div>
                    <div>
                        <h4>Simpan di Tempat Aman</h4>
                        <p>Simpan file backup di lokasi terpisah dari server (Google Drive, flashdisk, dll) untuk keamanan maksimal.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateFileName(input) {
        const label = document.getElementById('restoreFileName');
        const btn = document.getElementById('restoreBtn');
        if (input.files && input.files.length > 0) {
            const file = input.files[0];
            const sizeMB = (file.size / 1024 / 1024).toFixed(1);
            label.innerHTML = '<strong>' + file.name + '</strong> (' + sizeMB + ' MB)';
            label.style.color = '#059669';
            btn.disabled = false;
        } else {
            label.textContent = 'Klik untuk pilih file .zip';
            label.style.color = '#64748b';
            btn.disabled = true;
        }
    }

    function confirmRestore() {
        if (!confirm('Yakin ingin restore file upload? File yang sama akan ditimpa.')) {
            return false;
        }
        const btn = document.getElementById('restoreBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        btn.disabled = true;
        return true;
    }
</script>
@endpush
