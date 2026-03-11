@extends('layouts.app')

@section('title', 'Download Format Nilai Asesmen')

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
.ref-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}
.ref-card h5 {
    color: #1e293b;
    font-weight: 700;
}
.ref-list {
    max-height: 300px;
    overflow-y: auto;
    padding: 0;
    list-style: none;
}
.ref-list li {
    padding: 8px 12px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ref-list li:last-child {
    border-bottom: none;
}
.ref-badge {
    background: #dbeafe;
    color: #1e40af;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
    min-width: 28px;
    text-align: center;
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
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <div class="container-fluid px-4">
            <!-- Header -->
            <div class="page-header">
                <div>
                    <h1><i class="fas fa-file-excel text-success"></i> Download Format Nilai Asesmen</h1>
                    <p class="text-muted mb-0">Download template Excel dengan referensi mata pelajaran dan rombel</p>
                </div>
                <a href="{{ route('admin.nilai-asesmen.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Download Form -->
            <div class="filter-card">
                <h5 class="mb-3"><i class="fas fa-download"></i> Pilih Parameter</h5>
                <form action="{{ route('admin.nilai-asesmen.download-template') }}" method="POST" id="downloadForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Jenis Asesmen</label>
                            <select class="form-select" name="jenis_asesmen" required>
                                <option value="">Pilih Jenis Asesmen</option>
                                <option value="Asesmen Sumatif Akhir Semester">Asesmen Sumatif Akhir Semester</option>
                                <option value="Asesmen Sumatif Tengah Semester">Asesmen Sumatif Tengah Semester</option>
                                <option value="Asesmen Sumatif Akhir Jenjang">Asesmen Sumatif Akhir Jenjang</option>
                                <option value="Asesmen Formatif">Asesmen Formatif</option>
                                <option value="Asesmen Diagnostik">Asesmen Diagnostik</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tahun Pelajaran</label>
                            <select class="form-select" name="tahun_pelajaran" required>
                                <option value="">Pilih Tahun Pelajaran</option>
                                @foreach($tahunList as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Semester</label>
                            <select class="form-select" name="semester" required>
                                <option value="">Pilih Semester</option>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn-download">
                            <i class="fas fa-file-excel"></i> Download Template Excel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Reference Cards -->
            <div class="row">
                <div class="col-md-6">
                    <div class="ref-card">
                        <h5><i class="fas fa-book text-primary"></i> Referensi Mata Pelajaran</h5>
                        <p class="text-muted" style="font-size:0.85rem">Daftar nama mata pelajaran yang dapat digunakan di kolom "Mata Pelajaran"</p>
                        <ul class="ref-list">
                            @foreach($mapelList as $index => $mapel)
                            <li>
                                <span class="ref-badge">{{ $index + 1 }}</span>
                                {{ $mapel }}
                            </li>
                            @endforeach
                            @if($mapelList->isEmpty())
                            <li class="text-muted"><em>Belum ada data mata pelajaran</em></li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="ref-card">
                        <h5><i class="fas fa-chalkboard text-warning"></i> Referensi Rombel</h5>
                        <p class="text-muted" style="font-size:0.85rem">Daftar rombel yang dapat digunakan di kolom "Rombel"</p>
                        <ul class="ref-list">
                            @foreach($rombelList as $index => $rombel)
                            <li>
                                <span class="ref-badge">{{ $index + 1 }}</span>
                                {{ $rombel->nama_rombel }}
                                <small class="text-muted">({{ $rombel->tahun_pelajaran }} - {{ $rombel->semester }})</small>
                            </li>
                            @endforeach
                            @if($rombelList->isEmpty())
                            <li class="text-muted"><em>Belum ada data rombel</em></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h6><i class="fas fa-info-circle"></i> Petunjuk Penggunaan:</h6>
                <ol class="mb-0">
                    <li>Pilih <strong>Jenis Asesmen</strong>, <strong>Tahun Pelajaran</strong>, dan <strong>Semester</strong></li>
                    <li>Klik tombol <strong>Download Template Excel</strong></li>
                    <li>File Excel berisi 3 sheet:
                        <ul>
                            <li><strong>Template Import</strong> — isi data nilai di sheet ini</li>
                            <li><strong>Ref Mata Pelajaran</strong> — referensi nama mata pelajaran yang valid</li>
                            <li><strong>Ref Rombel</strong> — referensi nama rombel yang valid</li>
                        </ul>
                    </li>
                    <li>Isi kolom: Rombel, Mata Pelajaran, Nama Siswa, NISN, dan Nilai</li>
                    <li>Kolom Jenis Asesmen, Semester, dan Tahun Pelajaran sudah otomatis terisi</li>
                    <li>Setelah selesai, gunakan fitur <strong>Import Data</strong> untuk mengupload file</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
