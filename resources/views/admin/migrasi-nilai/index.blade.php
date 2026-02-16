@extends('layouts.app')

@section('title', 'Migrasi Nilai Manual')

@push('styles')
<style>
.filter-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}
.btn-download {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
}
.btn-download:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}
.btn-import {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
}
.instructions {
    background: #f8f9fa;
    border-left: 4px solid #667eea;
    padding: 15px;
    border-radius: 8px;
    margin-top: 20px;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-file-upload text-primary"></i> Migrasi Nilai Manual
            </h1>
            <p class="text-muted mb-0">Upload nilai ke tabel Katrol Nilai Leger via Excel</p>
        </div>
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

    <!-- Filter Card -->
    <div class="filter-card">
        <h5 class="mb-3"><i class="fas fa-filter"></i> Filter Data</h5>
        <form id="filterForm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tahun Pelajaran</label>
                    <select class="form-select" id="tahun_pelajaran" name="tahun_pelajaran" required>
                        <option value="">Pilih Tahun Pelajaran</option>
                        @foreach($periods as $period)
                            <option value="{{ $period->tahun_pelajaran }}">{{ $period->tahun_pelajaran }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Semester</label>
                    <select class="form-select" id="semester" name="semester" required>
                        <option value="">Pilih Semester</option>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Rombel</label>
                    <select class="form-select" id="rombel_id" name="rombel_id" required>
                        <option value="">Pilih Rombel</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->id }}">{{ $rombel->nama_rombel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Download Template Card -->
    <div class="filter-card">
        <h5 class="mb-3"><i class="fas fa-download"></i> Download Template Excel</h5>
        <p class="text-muted">Download template Excel yang sudah berisi daftar siswa dari rombel yang dipilih.</p>
        <form action="{{ route('admin.migrasi-nilai.download-template') }}" method="POST" id="downloadForm">
            @csrf
            <input type="hidden" name="tahun_pelajaran" id="download_tahun">
            <input type="hidden" name="semester" id="download_semester">
            <input type="hidden" name="rombel_id" id="download_rombel">
            <button type="submit" class="btn btn-download" id="btnDownload" disabled>
                <i class="fas fa-file-excel"></i> Download Template Excel
            </button>
        </form>
    </div>

    <!-- Import Card -->
    <div class="filter-card">
        <h5 class="mb-3"><i class="fas fa-upload"></i> Import File Excel</h5>
        <p class="text-muted">Upload file Excel yang sudah diisi dengan nilai siswa.</p>
        <form action="{{ route('admin.migrasi-nilai.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
            @csrf
            <input type="hidden" name="tahun_pelajaran" id="import_tahun">
            <input type="hidden" name="semester" id="import_semester">
            <input type="hidden" name="rombel_id" id="import_rombel">
            
            <div class="mb-3">
                <input type="file" class="form-control" name="file" id="fileInput" accept=".xlsx,.xls" required>
                <small class="text-muted">Format: .xlsx atau .xls | Max: 5MB</small>
            </div>
            
            <button type="submit" class="btn btn-import" id="btnImport" disabled>
                <i class="fas fa-upload"></i> Import Nilai
            </button>
        </form>
    </div>

    <!-- Instructions -->
    <div class="instructions">
        <h6><i class="fas fa-info-circle"></i> Petunjuk Penggunaan:</h6>
        <ol class="mb-0">
            <li>Pilih <strong>Tahun Pelajaran</strong>, <strong>Semester</strong>, dan <strong>Rombel</strong></li>
            <li>Klik tombol <strong>Download Template Excel</strong> untuk mendapatkan template dengan daftar siswa</li>
            <li>Isi nilai untuk setiap mata pelajaran di file Excel (kolom-kolom yang tersedia)</li>
            <li>Simpan file Excel yang sudah diisi</li>
            <li>Upload file Excel menggunakan tombol <strong>Import Nilai</strong></li>
            <li>Sistem akan menyimpan nilai ke tabel <code>katrol_nilai_leger</code></li>
        </ol>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tahunInput = document.getElementById('tahun_pelajaran');
    const semesterInput = document.getElementById('semester');
    const rombelInput = document.getElementById('rombel_id');
    const btnDownload = document.getElementById('btnDownload');
    const btnImport = document.getElementById('btnImport');
    
    // Enable/disable buttons based on filter selection
    function checkFilters() {
        const tahun = tahunInput.value;
        const semester = semesterInput.value;
        const rombel = rombelInput.value;
        
        if (tahun && semester && rombel) {
            // Enable buttons
            btnDownload.disabled = false;
            btnImport.disabled = false;
            
            // Set hidden values
            document.getElementById('download_tahun').value = tahun;
            document.getElementById('download_semester').value = semester;
            document.getElementById('download_rombel').value = rombel;
            document.getElementById('import_tahun').value = tahun;
            document.getElementById('import_semester').value = semester;
            document.getElementById('import_rombel').value = rombel;
        } else {
            btnDownload.disabled = true;
            btnImport.disabled = true;
        }
    }
    
    tahunInput.addEventListener('change', checkFilters);
    semesterInput.addEventListener('change', checkFilters);
    rombelInput.addEventListener('change', checkFilters);
    
    // Disable import button if no file selected
    document.getElementById('fileInput').addEventListener('change', function() {
        if (!this.files.length) {
            btnImport.disabled = true;
        } else {
            checkFilters();
        }
    });
});
</script>
@endpush
