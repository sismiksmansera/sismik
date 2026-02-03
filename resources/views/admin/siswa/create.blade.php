@extends('layouts.app')

@section('title', 'Tambah Siswa | SISMIK')

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
    .form-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--gray-200);
    }
    .form-section:last-child { border-bottom: none; }
    .form-section h3 {
        color: var(--primary-dark);
        font-size: 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    .form-row-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    @media (max-width: 768px) {
        .form-row, .form-row-3 { grid-template-columns: 1fr; }
    }
    .form-group { margin-bottom: 20px; }
    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--gray-700);
        font-size: 14px;
    }
    .form-label .required { color: #ef4444; }
    .form-control, .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--gray-200);
        border-radius: 10px;
        font-size: 14px;
        transition: border-color 0.3s;
    }
    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--primary);
    }
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
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="header-text">
                <h1>Tambah Data Siswa</h1>
                <p>Daftarkan siswa baru ke sistem</p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <div class="form-card">
            <form action="{{ route('admin.siswa.store') }}" method="POST">
                @csrf

                <!-- Login Info -->
                <div class="form-section">
                    <h3><i class="fas fa-key"></i> Informasi Login</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">NISN <span class="required">*</span></label>
                            <input type="text" name="nisn" class="form-control" value="{{ old('nisn') }}" 
                                required placeholder="Nomor Induk Siswa Nasional">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password <span class="required">*</span></label>
                            <input type="password" name="password" class="form-control" required
                                placeholder="Password untuk login siswa">
                        </div>
                    </div>
                </div>

                <!-- Data Pribadi -->
                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Data Pribadi</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">NIS</label>
                            <input type="text" name="nis" class="form-control" value="{{ old('nis') }}"
                                placeholder="Nomor Induk Sekolah">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" 
                                required placeholder="Nama lengkap siswa">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jk" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" {{ old('jk') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jk') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Agama</label>
                            <select name="agama" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir') }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">No HP Siswa</label>
                            <input type="text" name="nohp_siswa" class="form-control" value="{{ old('nohp_siswa') }}"
                                placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                    </div>
                </div>

                <!-- Data Alamat -->
                <div class="form-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Data Alamat</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Provinsi</label>
                            <input type="text" name="provinsi" class="form-control" value="{{ old('provinsi') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kota/Kabupaten</label>
                            <input type="text" name="kota" class="form-control" value="{{ old('kota') }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Kecamatan</label>
                            <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kelurahan</label>
                            <input type="text" name="kelurahan" class="form-control" value="{{ old('kelurahan') }}">
                        </div>
                    </div>
                </div>

                <!-- Data Orang Tua -->
                <div class="form-section">
                    <h3><i class="fas fa-users"></i> Data Orang Tua</h3>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label class="form-label">Nama Ayah</label>
                            <input type="text" name="nama_bapak" class="form-control" value="{{ old('nama_bapak') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_bapak" class="form-control" value="{{ old('pekerjaan_bapak') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">No HP Ayah</label>
                            <input type="text" name="nohp_bapak" class="form-control" value="{{ old('nohp_bapak') }}">
                        </div>
                    </div>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label class="form-label">Nama Ibu</label>
                            <input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu" class="form-control" value="{{ old('pekerjaan_ibu') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">No HP Ibu</label>
                            <input type="text" name="nohp_ibu" class="form-control" value="{{ old('nohp_ibu') }}">
                        </div>
                    </div>
                </div>

                <!-- Data Akademik -->
                <div class="form-section">
                    <h3><i class="fas fa-graduation-cap"></i> Data Akademik</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Angkatan Masuk</label>
                            <select name="angkatan_masuk" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach($angkatanList as $tahun)
                                    <option value="{{ $tahun }}" {{ old('angkatan_masuk') == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Rombel</label>
                            <select name="nama_rombel" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach($rombelList as $rombel)
                                    <option value="{{ $rombel }}" {{ old('nama_rombel') == $rombel ? 'selected' : '' }}>
                                        {{ $rombel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Asal Sekolah</label>
                            <input type="text" name="asal_sekolah" class="form-control" value="{{ old('asal_sekolah') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nilai SKL</label>
                            <input type="text" name="nilai_skl" class="form-control" value="{{ old('nilai_skl') }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Jumlah Saudara</label>
                            <input type="number" name="jml_saudara" class="form-control" value="{{ old('jml_saudara') }}" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Anak Ke-</label>
                            <input type="number" name="anak_ke" class="form-control" value="{{ old('anak_ke') }}" min="1">
                        </div>
                    </div>
                </div>

                <div class="btn-group-action">
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
