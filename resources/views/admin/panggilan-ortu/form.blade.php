@extends('layouts.app')

@section('title', (isset($panggilan) ? 'Edit' : 'Buat') . ' Surat Panggilan - ' . $siswa->nama . ' | SISMIK')

@push('styles')
<style>
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }
    .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .header-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    .header-text h1 { margin: 0; color: #065f46; font-size: 24px; font-weight: 700; }
    .header-text p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }
    
    /* Form Card */
    .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .form-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        padding: 15px 20px;
    }
    .form-header h2 {
        margin: 0;
        color: white;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .form-body { padding: 25px; }
    
    /* Siswa Info Card */
    .siswa-info-card {
        background: #f8fafc;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .siswa-foto {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #10b981;
        flex-shrink: 0;
    }
    .siswa-foto img { width: 100%; height: 100%; object-fit: cover; }
    .siswa-foto-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    
    /* Form Grid */
    .form-grid { display: grid; gap: 20px; margin-bottom: 20px; }
    .form-grid-2 { grid-template-columns: 1fr 1fr; }
    .form-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
    
    /* Form Group */
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }
    .form-group label i { color: #10b981; margin-right: 5px; }
    .form-group .required { color: #ef4444; }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
        outline: none;
    }
    textarea.form-control { resize: vertical; }
    
    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }
    
    @media (max-width: 768px) {
        .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="header-text">
                    <h1>{{ isset($panggilan) ? 'Edit Surat Panggilan' : 'Buat Surat Panggilan' }}</h1>
                    <p>Siswa: <strong>{{ $siswa->nama }}</strong> | NISN: {{ $siswa->nisn }}</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ isset($panggilan) ? route('admin.panggilan-ortu.update', $panggilan->id) : route('admin.panggilan-ortu.store') }}" method="POST" class="form-card">
            @csrf
            @if(isset($panggilan))
                @method('PUT')
            @endif
            
            <div class="form-header">
                <h2><i class="fas fa-file-alt"></i> Form Surat Panggilan Orang Tua</h2>
            </div>
            
            <div class="form-body">
                <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">
                <input type="hidden" name="guru_bk_id_param" value="{{ $guruBkId }}">
                
                <!-- Siswa Info -->
                <div class="siswa-info-card">
                    <div class="siswa-foto">
                        @if($hasFoto)
                            <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="">
                        @else
                            <div class="siswa-foto-placeholder" style="background: {{ $siswa->jk == 'Laki-laki' ? 'linear-gradient(135deg, #3b82f6, #1d4ed8)' : 'linear-gradient(135deg, #ec4899, #db2777)' }};">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #1f2937; font-size: 16px;">{{ $siswa->nama }}</div>
                        <div style="font-size: 13px; color: #6b7280;">NISN: {{ $siswa->nisn }} | Kelas: {{ $siswa->nama_rombel ?? '-' }}</div>
                        <div style="font-size: 12px; color: #9ca3af; margin-top: 3px;">Orang Tua: {{ $siswa->nama_bapak ?? '-' }} / {{ $siswa->nama_ibu ?? '-' }}</div>
                    </div>
                </div>
                
                <!-- Row: No Surat & Tanggal Surat -->
                <div class="form-grid form-grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-hashtag"></i> Nomor Surat</label>
                        <input type="text" name="no_surat" class="form-control" 
                               value="{{ old('no_surat', $panggilan->no_surat ?? $defaultNoSurat ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Tanggal Surat <span class="required">*</span></label>
                        <input type="date" name="tanggal_surat" class="form-control" required
                               value="{{ old('tanggal_surat', isset($panggilan) ? $panggilan->tanggal_surat->format('Y-m-d') : ($defaultTanggal ?? date('Y-m-d'))) }}">
                    </div>
                </div>
                
                <!-- Row: Perihal -->
                <div class="form-group">
                    <label><i class="fas fa-clipboard"></i> Perihal <span class="required">*</span></label>
                    <input type="text" name="perihal" class="form-control" required 
                           placeholder="Contoh: Undangan Pertemuan Orang Tua/Wali Siswa"
                           value="{{ old('perihal', $panggilan->perihal ?? '') }}">
                </div>
                
                <!-- Row: Alasan -->
                <div class="form-group">
                    <label><i class="fas fa-info-circle"></i> Alasan / Keterangan</label>
                    <textarea name="alasan" rows="3" class="form-control" 
                              placeholder="Jelaskan alasan pemanggilan orang tua...">{{ old('alasan', $panggilan->alasan ?? '') }}</textarea>
                </div>
                
                <!-- Row: Menghadap Ke -->
                <div class="form-group">
                    <label><i class="fas fa-user-tie"></i> Menghadap Ke <span class="required">*</span></label>
                    <select name="guru_bk_id" class="form-control" required>
                        <option value="">-- Pilih Guru BK --</option>
                        @foreach($guruBKList as $guru)
                            <option value="{{ $guru->id }}" {{ old('guru_bk_id', $panggilan->guru_bk_id ?? $guruBkId) == $guru->id ? 'selected' : '' }}>
                                {{ $guru->nama }} ({{ $guru->nip }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Row: Tanggal, Jam, Tempat -->
                <div class="form-grid form-grid-3">
                    <div class="form-group">
                        <label><i class="fas fa-calendar-check"></i> Tanggal Panggilan <span class="required">*</span></label>
                        <input type="date" name="tanggal_panggilan" class="form-control" required
                               value="{{ old('tanggal_panggilan', isset($panggilan) ? $panggilan->tanggal_panggilan->format('Y-m-d') : ($defaultTanggal ?? date('Y-m-d'))) }}">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Jam Panggilan</label>
                        <input type="time" name="jam_panggilan" class="form-control"
                               value="{{ old('jam_panggilan', $panggilan->jam_panggilan ?? $defaultJam ?? '09:00') }}">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Tempat</label>
                        <input type="text" name="tempat" class="form-control" placeholder="Ruang BK"
                               value="{{ old('tempat', $panggilan->tempat ?? 'Ruang BK') }}">
                    </div>
                </div>
                
                @if(isset($panggilan))
                    <!-- Status (edit only) -->
                    <div class="form-group">
                        <label><i class="fas fa-flag"></i> Status</label>
                        <select name="status" class="form-control">
                            <option value="Menunggu" {{ old('status', $panggilan->status) == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="Hadir" {{ old('status', $panggilan->status) == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="Tidak Hadir" {{ old('status', $panggilan->status) == 'Tidak Hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                            <option value="Dijadwalkan Ulang" {{ old('status', $panggilan->status) == 'Dijadwalkan Ulang' ? 'selected' : '' }}>Dijadwalkan Ulang</option>
                        </select>
                    </div>
                    
                    <!-- Catatan (edit only) -->
                    <div class="form-group">
                        <label><i class="fas fa-sticky-note"></i> Catatan</label>
                        <textarea name="catatan" rows="2" class="form-control" 
                                  placeholder="Catatan tambahan...">{{ old('catatan', $panggilan->catatan ?? '') }}</textarea>
                    </div>
                @endif
                
                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('admin.panggilan-ortu.index', ['nisn' => $siswa->nisn, 'guru_bk_id' => $guruBkId]) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-save"></i> {{ isset($panggilan) ? 'Simpan Perubahan' : 'Simpan Surat' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
