@extends('layouts.app-guru-bk')

@section('title', ($catatan ? 'Edit' : 'Tambah') . ' Catatan - ' . $siswa->nama)

@section('content')
<div class="main-content form-page">
    <!-- HEADER -->
    <div class="page-header">
        <div class="header-icon">
            <i class="fas fa-sticky-note"></i>
        </div>
        <h1>{{ $catatan ? 'Edit Catatan' : 'Tambah Catatan' }} Guru Wali</h1>
        <p>{{ $siswa->nama }} - {{ $rombel }}</p>
    </div>

    <!-- FORM CARD -->
    <div class="form-card">
        <div class="form-card-header">
            <h2><i class="fas fa-edit"></i> Form Catatan</h2>
        </div>
        
        <form action="{{ $catatan 
            ? route('guru_bk.catatan-guru-wali.update', $catatan->id) 
            : route('guru_bk.catatan-guru-wali.store', $siswa->id) }}" method="POST">
            @csrf
            @if($catatan)
                @method('PUT')
            @endif

            <div class="form-card-body">
                @if($errors->any())
                    <div class="alert-danger">
                        <strong>Terjadi kesalahan:</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- READ-ONLY INFO -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Siswa</label>
                        <input type="text" class="form-control" value="{{ $siswa->nama }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Rombel</label>
                        <input type="text" class="form-control" value="{{ $rombel }}" readonly>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tahun Pelajaran</label>
                        <input type="text" class="form-control" value="{{ $periodeAktif->tahun_pelajaran }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Semester</label>
                        <input type="text" class="form-control" value="{{ $periodeAktif->semester }}" readonly>
                    </div>
                </div>

                <hr style="margin: 25px 0; border: none; border-top: 1px solid #e5e7eb;">

                <!-- FORM INPUTS -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Pencatatan <span class="required">*</span></label>
                        <input type="date" name="tanggal_pencatatan" class="form-control" 
                               value="{{ old('tanggal_pencatatan', $catatan?->tanggal_pencatatan?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Bimbingan <span class="required">*</span></label>
                        <select name="jenis_bimbingan" id="jenisBimbingan" class="form-control" required>
                            <option value="">-- Pilih Jenis Bimbingan --</option>
                            @foreach($jenisBimbinganOptions as $jenis)
                                <option value="{{ $jenis }}" {{ old('jenis_bimbingan', $catatan?->jenis_bimbingan) == $jenis ? 'selected' : '' }}>
                                    {{ $jenis }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row single">
                    <div class="form-group">
                        <label>Catatan <span class="required">*</span></label>
                        <textarea name="catatan" class="form-control" placeholder="Tuliskan catatan bimbingan..." required>{{ old('catatan', $catatan?->catatan) }}</textarea>
                    </div>
                </div>

                <div class="form-row">
                    <!-- CONDITIONAL: Nilai Praktik Ibadah -->
                    <div class="form-group conditional-field" id="nilaiIbadahGroup">
                        <label>Nilai Praktik Ibadah</label>
                        <select name="nilai_praktik_ibadah" id="nilaiPraktikIbadah" class="form-control">
                            <option value="">-- Pilih Nilai --</option>
                            @foreach($nilaiPraktikIbadahOptions as $nilai)
                                <option value="{{ $nilai }}" {{ old('nilai_praktik_ibadah', $catatan?->nilai_praktik_ibadah) == $nilai ? 'selected' : '' }}>
                                    {{ $nilai }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Perkembangan</label>
                        <select name="perkembangan" class="form-control">
                            <option value="">-- Pilih Perkembangan --</option>
                            @foreach($perkembanganOptions as $opt)
                                <option value="{{ $opt }}" {{ old('perkembangan', $catatan?->perkembangan) == $opt ? 'selected' : '' }}>
                                    {{ $opt }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('guru_bk.catatan-guru-wali.index', $siswa->id) }}" class="btn-modern btn-cancel">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" class="btn-modern btn-submit">
                    <i class="fas fa-save"></i> {{ $catatan ? 'Update' : 'Simpan' }} Catatan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* PAGE STYLES */
.form-page {
    padding: 30px;
    background: #f8f9fa;
    min-height: calc(100vh - 60px);
}

/* HEADER */
.page-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    color: white;
    text-align: center;
}

.header-icon {
    width: 70px;
    height: 70px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin: 0 auto 15px;
}

.page-header h1 {
    margin: 0 0 8px;
    font-size: 24px;
    font-weight: 700;
}

.page-header p {
    margin: 0;
    opacity: 0.9;
}

/* FORM CARD */
.form-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.form-card-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.form-card-header h2 {
    margin: 0;
    font-size: 18px;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-card-body {
    padding: 25px;
}

/* FORM STYLES */
.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

.form-row.single {
    grid-template-columns: 1fr;
}

.form-group {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-group label .required {
    color: #dc2626;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-control:disabled, .form-control[readonly] {
    background: #f3f4f6;
    color: #6b7280;
}

textarea.form-control {
    min-height: 120px;
    resize: vertical;
}

select.form-control {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 12px center;
    background-repeat: no-repeat;
    background-size: 20px;
    padding-right: 40px;
}

/* CONDITIONAL FIELD */
.conditional-field {
    display: none;
}

.conditional-field.show {
    display: block;
}

/* FORM ACTIONS */
.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    padding: 20px 25px;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
}

.btn-modern {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-cancel {
    background: white;
    color: #374151;
    border: 2px solid #d1d5db;
}

.btn-cancel:hover {
    background: #f3f4f6;
}

.btn-submit {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
}

/* ALERT */
.alert-danger {
    background: #fee2e2;
    border: 1px solid #dc2626;
    color: #991b1b;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.alert-danger ul {
    margin: 10px 0 0 20px;
    padding: 0;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .form-page { padding: 20px; }
    .form-row { grid-template-columns: 1fr; }
    .form-actions { flex-direction: column; }
    .btn-modern { justify-content: center; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisBimbingan = document.getElementById('jenisBimbingan');
    const nilaiIbadahGroup = document.getElementById('nilaiIbadahGroup');

    function toggleNilaiIbadah() {
        if (jenisBimbingan.value === 'Bimbingan Ibadah') {
            nilaiIbadahGroup.classList.add('show');
        } else {
            nilaiIbadahGroup.classList.remove('show');
            document.getElementById('nilaiPraktikIbadah').value = '';
        }
    }

    jenisBimbingan.addEventListener('change', toggleNilaiIbadah);
    toggleNilaiIbadah(); // Initial check
});
</script>
@endsection
