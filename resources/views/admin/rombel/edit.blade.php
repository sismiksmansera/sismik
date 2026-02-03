@extends('layouts.app')

@section('title', 'Edit Rombel | SISMIK')

@push('styles')
<style>
    .content-header {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
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
    
    .form-card {
        background: var(--white);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .form-group { margin-bottom: 24px; }
    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--gray-700);
    }
    .form-control, .form-select {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid var(--gray-200);
        border-radius: 12px;
        font-size: 15px;
        transition: border-color 0.3s;
    }
    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #F59E0B;
    }
    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }
    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; }
    }
    .btn-group-action {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 30px;
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
                <i class="fas fa-edit"></i>
            </div>
            <div class="header-text">
                <h1>Edit Rombongan Belajar</h1>
                <p>Perbarui data kelas: {{ $rombel->nama_rombel }}</p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <div class="form-card">
            <form action="{{ route('admin.rombel.update', $rombel->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tahun Pelajaran <span style="color: red;">*</span></label>
                        <select name="tahun_pelajaran" class="form-select" required>
                            <option value="">Pilih Tahun Pelajaran</option>
                            @foreach($tahunList as $tahun)
                                <option value="{{ $tahun }}" 
                                    {{ old('tahun_pelajaran', $rombel->tahun_pelajaran) == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Semester <span style="color: red;">*</span></label>
                        <select name="semester" class="form-select" required>
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil" {{ old('semester', $rombel->semester) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ old('semester', $rombel->semester) == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Rombel <span style="color: red;">*</span></label>
                        <input type="text" name="nama_rombel" class="form-control" 
                            value="{{ old('nama_rombel', $rombel->nama_rombel) }}" placeholder="Contoh: X.1, XI IPA 1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tingkat <span style="color: red;">*</span></label>
                        <select name="tingkat" class="form-select" required>
                            <option value="">Pilih Tingkat</option>
                            <option value="X" {{ old('tingkat', $rombel->tingkat) == 'X' ? 'selected' : '' }}>X (Sepuluh)</option>
                            <option value="XI" {{ old('tingkat', $rombel->tingkat) == 'XI' ? 'selected' : '' }}>XI (Sebelas)</option>
                            <option value="XII" {{ old('tingkat', $rombel->tingkat) == 'XII' ? 'selected' : '' }}>XII (Dua Belas)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Wali Kelas <span style="color: red;">*</span></label>
                    <select name="wali_kelas" class="form-select" required>
                        <option value="">Pilih Wali Kelas</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->nama }}" {{ old('wali_kelas', $rombel->wali_kelas) == $guru->nama ? 'selected' : '' }}>
                                {{ $guru->nama }} - {{ $guru->nip }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Latitude (Opsional)</label>
                        <input type="text" name="latitude" class="form-control" 
                            value="{{ old('latitude', $rombel->latitude) }}" placeholder="Contoh: -7.3041">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Longitude (Opsional)</label>
                        <input type="text" name="longitude" class="form-control" 
                            value="{{ old('longitude', $rombel->longitude) }}" placeholder="Contoh: 110.4267">
                    </div>
                </div>

                <div class="btn-group-action">
                    <a href="{{ route('admin.rombel.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-warning" style="color: white;">
                        <i class="fas fa-save"></i> Update Rombel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
