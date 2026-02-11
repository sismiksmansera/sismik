@extends('layouts.app')

@section('title', 'Import Penugasan Guru | SISMIK')

@push('styles')
<style>
    .import-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
    }
    .import-header-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    .import-header h1 { margin: 0; color: #065f46; font-size: 24px; font-weight: 700; }
    .import-header p { margin: 4px 0 0; color: #6b7280; font-size: 14px; }

    .periode-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        border: 1px solid #38bdf8;
        border-radius: 12px;
        color: #0369a1;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .import-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .import-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .import-card-header {
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .import-card-header i {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
    }
    .import-card-header h3 { margin: 0; font-size: 16px; font-weight: 600; color: #1f2937; }
    .import-card-header p { margin: 3px 0 0; font-size: 12px; color: #6b7280; }
    .import-card-body { padding: 0 24px 24px; }

    .download-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s;
    }
    .download-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16,185,129,0.3); }

    .upload-zone {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        margin-bottom: 16px;
    }
    .upload-zone:hover { border-color: #10b981; background: #f0fdf4; }
    .upload-zone i { font-size: 36px; color: #10b981; margin-bottom: 8px; }
    .upload-zone h4 { margin: 0 0 4px; color: #374151; }
    .upload-zone p { margin: 0; color: #6b7280; font-size: 12px; }

    .file-selected {
        margin-top: 10px;
        padding: 8px 14px;
        background: #ecfdf5;
        border: 1px solid #6ee7b7;
        border-radius: 8px;
        color: #065f46;
        font-weight: 600;
        font-size: 13px;
        display: none;
    }

    .import-btn {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    .import-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(59,130,246,0.3); }
    .import-btn:disabled { opacity: 0.5; cursor: not-allowed; }

    .info-box {
        background: #eff6ff;
        border: 1px solid #93c5fd;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 16px;
    }
    .info-box h5 { margin: 0 0 8px; color: #1d4ed8; font-size: 13px; }
    .info-box ol { margin: 0; padding-left: 18px; color: #1e40af; font-size: 12px; line-height: 1.8; }

    .result-box {
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .result-box.success { background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46; }
    .result-box.error { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }
    .result-box i { font-size: 20px; margin-top: 2px; }
    .result-box .result-text { flex: 1; }
    .result-box .result-text p { margin: 0; font-weight: 600; }
    .result-box .result-text small { color: #6b7280; font-size: 12px; }

    .error-list {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 20px;
        max-height: 200px;
        overflow-y: auto;
    }
    .error-list h5 { margin: 0 0 8px; color: #991b1b; font-size: 13px; }
    .error-list ul { margin: 0; padding-left: 18px; color: #dc2626; font-size: 12px; line-height: 1.8; }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Back Button -->
        <a href="{{ route('admin.guru.index') }}"
           style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: linear-gradient(135deg, #6b7280, #4b5563); color: white; border-radius: 10px; text-decoration: none; font-weight: 600; margin-bottom: 20px;">
            <i class="fas fa-arrow-left"></i> Kembali ke Data Guru
        </a>

        <!-- Header -->
        <div class="import-header">
            <div class="import-header-icon">
                <i class="fas fa-file-import"></i>
            </div>
            <div>
                <h1>Import Penugasan Guru</h1>
                <p>Download blangko dan import data penugasan guru ke jadwal pelajaran</p>
            </div>
        </div>

        <!-- Periode Info -->
        <div class="periode-badge">
            <i class="fas fa-calendar-alt"></i>
            Periode Aktif: {{ $tahunAktif }} — Semester {{ $semesterAktif }}
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="result-box success">
            <i class="fas fa-check-circle"></i>
            <div class="result-text">
                <p>{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="result-box error">
            <i class="fas fa-exclamation-circle"></i>
            <div class="result-text">
                <p>{{ $errors->first() }}</p>
            </div>
        </div>
        @endif

        @if(session('import_errors') && count(session('import_errors')) > 0)
        <div class="error-list">
            <h5><i class="fas fa-exclamation-triangle"></i> Detail Error:</h5>
            <ul>
                @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Cards Grid -->
        <div class="import-grid">
            <!-- Card 1: Download Blangko -->
            <div class="import-card">
                <div class="import-card-header">
                    <i style="background: linear-gradient(135deg, #10b981, #059669);"><i class="fas fa-download"></i></i>
                    <div>
                        <h3>Download Blangko</h3>
                        <p>Template Excel untuk input penugasan guru</p>
                    </div>
                </div>
                <div class="import-card-body">
                    <div class="info-box">
                        <h5><i class="fas fa-info-circle"></i> Isi Blangko:</h5>
                        <ol>
                            <li>Sheet <strong>"Data Penugasan"</strong> — isi data guru, mapel, rombel, hari, jam ke</li>
                            <li>Sheet <strong>"Referensi"</strong> — daftar guru, mapel, rombel, hari yang tersedia</li>
                            <li>Hapus baris contoh sebelum mengisi data</li>
                            <li>Setiap jam pelajaran = 1 baris (misal jam 1-3 = 3 baris)</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.guru.import-jadwal.download') }}" class="download-btn">
                        <i class="fas fa-file-download"></i> Download Blangko XLSX
                    </a>
                </div>
            </div>

            <!-- Card 2: Upload Import -->
            <div class="import-card">
                <div class="import-card-header">
                    <i style="background: linear-gradient(135deg, #3b82f6, #2563eb);"><i class="fas fa-upload"></i></i>
                    <div>
                        <h3>Upload & Import</h3>
                        <p>Upload file Excel yang sudah diisi</p>
                    </div>
                </div>
                <div class="import-card-body">
                    <form action="{{ route('admin.guru.import-jadwal.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="upload-zone" onclick="document.getElementById('fileJadwalInput').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <h4>Pilih File Excel</h4>
                            <p>Format: .xlsx | Maksimal: 5MB</p>
                            <div class="file-selected" id="selectedFileName"></div>
                        </div>
                        <input type="file" name="file_jadwal" id="fileJadwalInput" accept=".xlsx,.xls" style="display: none;" onchange="showFileName(this)">

                        <div class="info-box">
                            <h5><i class="fas fa-cog"></i> Cara Kerja Import:</h5>
                            <ol>
                                <li>Jika data <strong>belum ada</strong> → di-<strong>insert</strong> sebagai data baru</li>
                                <li>Jika data <strong>sudah ada</strong> (mapel + rombel + hari + jam yang sama) → di-<strong>update</strong> nama guru</li>
                                <li>Data akan masuk ke periode aktif: <strong>{{ $tahunAktif }}</strong> — <strong>{{ $semesterAktif }}</strong></li>
                            </ol>
                        </div>

                        <button type="submit" class="import-btn" id="importBtn" disabled>
                            <i class="fas fa-file-import"></i> Import Data Penugasan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showFileName(input) {
    if (input.files && input.files[0]) {
        const el = document.getElementById('selectedFileName');
        el.textContent = '📄 ' + input.files[0].name;
        el.style.display = 'block';
        document.getElementById('importBtn').disabled = false;
    }
}
</script>
@endpush
