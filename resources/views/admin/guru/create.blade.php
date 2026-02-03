@extends('layouts.app')

@section('title', 'Tambah Guru | SISMIK')

@push('styles')
<style>
    .form-container {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .form-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 24px 30px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .form-header-icon {
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .form-header h1 { margin: 0; font-size: 22px; font-weight: 700; }
    .form-header p { margin: 5px 0 0; opacity: 0.9; font-size: 14px; }
    
    .form-body { padding: 30px; }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    .form-group { margin-bottom: 20px; }
    .form-group.full { grid-column: span 2; }
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--gray-700);
        font-size: 14px;
    }
    .form-group label span { color: #ef4444; }
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--gray-200);
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s;
    }
    .form-control:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
    }
    
    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 20px;
        border-top: 1px solid var(--gray-200);
        margin-top: 20px;
    }
    
    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
        .form-group.full { grid-column: span 1; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <div class="form-container">
            <div class="form-header">
                <div class="form-header-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <h1>Tambah Guru Baru</h1>
                    <p>Lengkapi data guru dengan benar</p>
                </div>
            </div>

            <div class="form-body">
                @if($errors->any())
                    <div class="alert alert-danger" style="margin-bottom: 20px;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nama Lengkap <span>*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label>NIP</label>
                            <input type="text" name="nip" class="form-control" value="{{ old('nip') }}">
                        </div>
                        
                        <div class="form-group">
                            <label>Username <span>*</span></label>
                            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Password <span>*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                        
                        <div class="form-group">
                            <label>No. HP</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
                        </div>
                        
                        <div class="form-group">
                            <label>Status Kepegawaian</label>
                            <select name="status_kepegawaian" class="form-control">
                                <option value="">-- Pilih --</option>
                                <option value="PNS" {{ old('status_kepegawaian') == 'PNS' ? 'selected' : '' }}>PNS</option>
                                <option value="PPPK" {{ old('status_kepegawaian') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                                <option value="Honor" {{ old('status_kepegawaian') == 'Honor' ? 'selected' : '' }}>Honor</option>
                                <option value="GTY" {{ old('status_kepegawaian') == 'GTY' ? 'selected' : '' }}>GTY</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Golongan</label>
                            <input type="text" name="golongan" class="form-control" value="{{ old('golongan') }}" placeholder="Contoh: III/a">
                        </div>
                        
                        <div class="form-group">
                            <label>Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}">
                        </div>
                        
                        <div class="form-group full">
                            <label>Mapel Diampu</label>
                            <input type="text" name="mapel_diampu" class="form-control" value="{{ old('mapel_diampu') }}" placeholder="Contoh: Matematika, Fisika">
                        </div>
                        
                        <div class="form-group full">
                            <label>Foto</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                        </div>
                    </div>



                    <div class="form-actions">
                        <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">
                            <i class="fas fa-save"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
