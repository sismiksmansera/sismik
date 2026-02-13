@extends('layouts.app-guru-bk')

@section('content')
<div class="main-content panggilan-form-page">
    {{-- Header --}}
    <div class="bk-page-header">
        <div class="header-content-wrapper">
            <div class="header-icon-box">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="header-info">
                <div class="header-greeting">
                    <span class="greeting-text">Buat Surat Panggilan untuk</span>
                    <h1>{{ $siswaList->count() }} Siswa</h1>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="action-buttons-row">
        <a href="{{ url()->previous() }}" class="btn-action-header btn-secondary-header">
            <i class="fas fa-arrow-left"></i> <span class="btn-text">Kembali</span>
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('error'))
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Daftar Siswa Terpilih --}}
    <div class="siswa-list-card">
        <div class="siswa-list-header">
            <i class="fas fa-users"></i>
            <h3>Siswa yang Akan Dibuatkan Surat ({{ $siswaList->count() }})</h3>
        </div>
        <div class="siswa-list-body">
            @foreach($siswaList as $s)
            <div class="siswa-item-row">
                <div class="siswa-avatar {{ ($s->jk ?? '') == 'Laki-laki' ? 'laki' : 'perempuan' }}">
                    {{ strtoupper(substr($s->nama ?? '?', 0, 1)) }}
                </div>
                <div class="siswa-detail">
                    <div class="siswa-nama">{{ $s->nama }}</div>
                    <div class="siswa-meta">{{ $s->nama_rombel ?? '-' }} · NISN: {{ $s->nisn }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Form --}}
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-file-alt"></i>
            <h2>Form Surat Panggilan Orang Tua</h2>
        </div>
        
        <form method="POST" action="{{ route('guru_bk.panggilan-ortu.store-batch') }}" class="form-body">
            @csrf
            <input type="hidden" name="nisn_list" value="{{ $siswaList->pluck('nisn')->implode(',') }}">

            {{-- Row: No Surat & Tanggal Surat --}}
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-hashtag"></i> Nomor Surat</label>
                    <input type="text" name="no_surat" class="form-control"
                           value="{{ old('no_surat', 'SPO/' . date('Ymd') . '/' . rand(100, 999)) }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Tanggal Surat <span class="required">*</span></label>
                    <input type="date" name="tanggal_surat" class="form-control" required
                           value="{{ old('tanggal_surat', date('Y-m-d')) }}">
                </div>
            </div>

            {{-- Perihal --}}
            <div class="form-group">
                <label><i class="fas fa-clipboard"></i> Perihal <span class="required">*</span></label>
                <input type="text" name="perihal" class="form-control" required
                       placeholder="Contoh: Undangan Pertemuan Orang Tua/Wali Siswa"
                       value="{{ old('perihal') }}">
            </div>

            {{-- Alasan --}}
            <div class="form-group">
                <label><i class="fas fa-info-circle"></i> Alasan / Keterangan</label>
                <textarea name="alasan" class="form-control" rows="3"
                          placeholder="Jelaskan alasan pemanggilan orang tua...">{{ old('alasan') }}</textarea>
            </div>

            {{-- Menghadap Ke --}}
            <div class="form-group">
                <label><i class="fas fa-user-tie"></i> Menghadap Ke</label>
                <input type="text" name="menghadap_ke" class="form-control readonly-field"
                       value="{{ $guruBK->nama }}" readonly>
            </div>

            {{-- Row: Tanggal, Jam, Tempat --}}
            <div class="form-row three-cols">
                <div class="form-group">
                    <label><i class="fas fa-calendar-check"></i> Tanggal Panggilan <span class="required">*</span></label>
                    <input type="date" name="tanggal_panggilan" class="form-control" required
                           value="{{ old('tanggal_panggilan', date('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Jam Panggilan</label>
                    <input type="time" name="jam_panggilan" class="form-control"
                           value="{{ old('jam_panggilan', '09:00') }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Tempat</label>
                    <input type="text" name="tempat" class="form-control" placeholder="Ruang BK"
                           value="{{ old('tempat', 'Ruang BK') }}">
                </div>
            </div>

            {{-- Buttons --}}
            <div class="form-actions">
                <a href="{{ url()->previous() }}" class="btn-cancel">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Simpan untuk {{ $siswaList->count() }} Siswa
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.main-content.panggilan-form-page { padding: 25px; background: #f9fafb; min-height: calc(100vh - 70px); }

/* Header */
.panggilan-form-page .bk-page-header {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    border-radius: 16px; padding: 25px 30px; margin-bottom: 20px;
    box-shadow: 0 10px 30px rgba(124, 58, 237, 0.2);
}
.panggilan-form-page .header-content-wrapper { display: flex; align-items: center; gap: 20px; }
.panggilan-form-page .header-icon-box {
    width: 70px; height: 70px; background: rgba(255, 255, 255, 0.2);
    border-radius: 16px; display: flex; align-items: center; justify-content: center;
    font-size: 28px; color: white; flex-shrink: 0;
}
.panggilan-form-page .header-info { flex: 1; }
.panggilan-form-page .header-greeting .greeting-text {
    font-size: 14px; color: rgba(255,255,255,0.8); font-weight: 500; display: block; margin-bottom: 4px;
}
.panggilan-form-page .header-greeting h1 { font-size: 22px; font-weight: 700; color: white; margin: 0; }

/* Action Buttons */
.action-buttons-row { display: flex; justify-content: flex-start; gap: 10px; margin-bottom: 20px; }
.btn-action-header {
    padding: 12px 20px; border-radius: 12px; font-weight: 600; font-size: 14px;
    display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
    border: none; cursor: pointer; transition: all 0.3s;
}
.btn-secondary-header { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.btn-secondary-header:hover { background: #e5e7eb; }

/* Alerts */
.alert { padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px; }
.alert-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.alert ul { margin: 0; padding-left: 20px; }

/* Siswa List Card */
.siswa-list-card {
    background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    overflow: hidden; margin-bottom: 20px;
}
.siswa-list-header {
    background: linear-gradient(135deg, #f5f3ff, #ede9fe);
    padding: 14px 20px; display: flex; align-items: center; gap: 10px;
    border-bottom: 1px solid #e9d5ff;
}
.siswa-list-header i { color: #7c3aed; font-size: 16px; }
.siswa-list-header h3 { margin: 0; font-size: 14px; font-weight: 600; color: #5b21b6; }
.siswa-list-body { padding: 12px 16px; max-height: 250px; overflow-y: auto; }
.siswa-item-row {
    display: flex; align-items: center; gap: 10px; padding: 8px 10px;
    border-radius: 8px; transition: background 0.2s;
}
.siswa-item-row:hover { background: #f9fafb; }
.siswa-avatar {
    width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; color: white;
}
.siswa-avatar.laki { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.siswa-avatar.perempuan { background: linear-gradient(135deg, #ec4899, #db2777); }
.siswa-nama { font-weight: 600; font-size: 13px; color: #1f2937; }
.siswa-meta { font-size: 11px; color: #6b7280; }

/* Form Card */
.form-card { background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
.form-header {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    padding: 18px 25px; display: flex; align-items: center; gap: 12px;
}
.form-header i { font-size: 20px; color: white; }
.form-header h2 { margin: 0; color: white; font-size: 18px; font-weight: 600; }
.form-body { padding: 25px; }

/* Form Elements */
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 0; }
.form-row.three-cols { grid-template-columns: 1fr 1fr 1fr; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px; }
.form-group label i { color: #7c3aed; margin-right: 6px; }
.required { color: #ef4444; }
.form-control {
    width: 100%; padding: 12px 15px; border: 2px solid #e5e7eb; border-radius: 10px;
    font-size: 14px; transition: all 0.3s; background: white; box-sizing: border-box;
}
.form-control:focus { outline: none; border-color: #7c3aed; box-shadow: 0 0 0 4px rgba(124,58,237,0.1); }
.readonly-field { background: #f8fafc; color: #6b7280; }
textarea.form-control { resize: vertical; min-height: 100px; }

/* Form Actions */
.form-actions {
    display: flex; gap: 15px; justify-content: space-between;
    padding-top: 25px; border-top: 1px solid #e5e7eb; margin-top: 10px;
}
.btn-cancel, .btn-submit {
    padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px;
    display: flex; align-items: center; gap: 8px; cursor: pointer;
    transition: all 0.3s; text-decoration: none; border: none;
}
.btn-cancel { background: #f3f4f6; color: #64748b; }
.btn-cancel:hover { background: #e5e7eb; }
.btn-submit {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    color: white; box-shadow: 0 4px 15px rgba(124,58,237,0.3);
}
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(124,58,237,0.4); }

/* Responsive */
@media (max-width: 768px) {
    .main-content.panggilan-form-page { padding: 15px; }
    .panggilan-form-page .bk-page-header { padding: 15px; border-radius: 12px; }
    .panggilan-form-page .header-content-wrapper { flex-direction: column; align-items: center; text-align: center; gap: 10px; }
    .panggilan-form-page .header-icon-box { width: 60px; height: 60px; font-size: 24px; border-radius: 50%; }
    .panggilan-form-page .header-greeting .greeting-text { font-size: 11px; }
    .panggilan-form-page .header-greeting h1 { font-size: 16px; }
    .action-buttons-row { margin-bottom: 15px; }
    .btn-action-header { padding: 10px 15px; font-size: 12px; }
    .form-header { padding: 15px 18px; }
    .form-header h2 { font-size: 14px; }
    .form-body { padding: 18px; }
    .form-row, .form-row.three-cols { grid-template-columns: 1fr; gap: 0; }
    .form-group { margin-bottom: 15px; }
    .form-group label { font-size: 13px; }
    .form-control { padding: 10px 12px; font-size: 13px; }
    .form-actions { flex-direction: row; gap: 10px; }
    .btn-cancel, .btn-submit { flex: 1; justify-content: center; padding: 10px 15px; font-size: 12px; }
}
</style>
@endsection
