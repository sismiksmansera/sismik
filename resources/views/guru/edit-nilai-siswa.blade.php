@extends('layouts.app')

@section('title', 'Edit Nilai Siswa')

@push('styles')
<style>
/* HEADER SECTION - Green gradient */
.nilai-header-section {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    color: white;
    text-align: center;
}

.nilai-header-section .header-icon-large {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: white;
    margin: 0 auto 20px;
}

.nilai-header-section .page-title {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 10px 0;
    color: white;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.nilai-header-section .subtitle {
    font-size: 14px;
    opacity: 0.9;
    margin: 0;
}

.subtitle-badges {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
}

.subtitle-badge {
    background: rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ACTION BUTTONS */
.action-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 25px;
}

.btn-back {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-back:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
    color: white;
}

.btn-save {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

/* FORM CARD */
.form-section-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 25px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.form-card-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    padding: 20px 25px;
    color: white;
    display: flex;
    align-items: center;
    gap: 12px;
}

.form-card-header i { font-size: 20px; }
.form-card-header h3 { margin: 0; font-size: 18px; font-weight: 600; }

.form-card-body { padding: 25px; }

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group.full-width { grid-column: 1 / -1; }

.form-label {
    font-weight: 600;
    color: #374151;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-label i { color: #10b981; font-size: 14px; }

.modern-input {
    padding: 12px 15px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
    width: 100%;
    box-sizing: border-box;
}

.modern-input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.modern-input:disabled, .modern-input[readonly] {
    background: #f9fafb;
    color: #6b7280;
}

/* STUDENT CARD */
.student-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.section-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-header h2 {
    margin: 0;
    color: #1f2937;
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.badge {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.student-card {
    margin: 20px;
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    border: 1px solid #d1fae5;
    border-radius: 16px;
    overflow: hidden;
}

.student-card::before {
    content: '';
    display: block;
    height: 4px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.student-card-header {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
}

.student-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 18px;
    flex-shrink: 0;
    overflow: hidden;
}

.student-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.student-info { flex: 1; }
.student-name {
    font-weight: 700;
    font-size: 16px;
    margin: 0 0 4px 0;
}
.student-details {
    font-size: 12px;
    opacity: 0.9;
}

.student-card-body { padding: 20px; background: white; }

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #ecfdf5;
}

.info-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #059669;
}

.info-value {
    font-weight: 600;
    color: #1f2937;
}

.input-row {
    margin-top: 15px;
}

.input-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.input-label {
    font-size: 13px;
    font-weight: 600;
    color: #059669;
    display: flex;
    align-items: center;
    gap: 6px;
}

.nilai-input {
    padding: 12px 15px;
    border: 2px solid #d1fae5;
    border-radius: 10px;
    font-size: 18px;
    font-weight: 700;
    text-align: center;
    width: 120px;
    transition: all 0.3s ease;
}

.nilai-input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.keterangan-input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    resize: vertical;
    min-height: 80px;
}

.keterangan-input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* ALERTS */
.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .nilai-header-section { padding: 20px 15px; }
    .nilai-header-section .header-icon-large { width: 60px; height: 60px; font-size: 28px; }
    .nilai-header-section .page-title { font-size: 20px; }
    
    .form-grid { grid-template-columns: 1fr; }
    .action-bar { flex-direction: column; align-items: stretch; }
    .btn-back, .btn-save { justify-content: center; }
    
    .student-card-header { flex-direction: column; text-align: center; }
    .info-row { flex-direction: column; gap: 8px; text-align: center; }
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')
    
    <div class="main-content">
        <div class="edit-nilai-page">
            <!-- HEADER SECTION -->
            <div class="nilai-header-section">
                <div class="header-icon-large">
                    <i class="fas fa-edit"></i>
                </div>
                <h1 class="page-title">Edit Nilai</h1>
                <p class="subtitle">{{ $siswa->nama }}</p>
                <div class="subtitle-badges">
                    <span class="subtitle-badge">
                        <i class="fas fa-book"></i> {{ $mapel }}
                    </span>
                    <span class="subtitle-badge">
                        <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                    </span>
                    <span class="subtitle-badge">
                        <i class="fas fa-school"></i> {{ $namaRombel }}
                    </span>
                    <span class="subtitle-badge">
                        <i class="fas fa-calendar-alt"></i> {{ $tahunPelajaran }} - {{ $semesterAktif }}
                    </span>
                </div>
            </div>

            <!-- ALERTS -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- FORM -->
            <form action="{{ route('guru.edit-nilai-siswa.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id_rombel" value="{{ $idRombel }}">
                <input type="hidden" name="mapel" value="{{ $mapel }}">
                <input type="hidden" name="nama_rombel" value="{{ $namaRombel }}">
                <input type="hidden" name="tanggal_penilaian" value="{{ $tanggal }}">
                <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">
                <input type="hidden" name="nama_siswa" value="{{ $siswa->nama }}">

                <!-- ACTION BUTTONS TOP -->
                <div class="action-bar">
                    <a href="{{ route('guru.lihat-nilai', ['id_rombel' => $idRombel, 'mapel' => $mapel]) }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali ke Lihat Nilai
                    </a>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Update Nilai
                    </button>
                </div>

                <!-- FORM SECTION - MATERI -->
                <div class="form-section-card">
                    <div class="form-card-header">
                        <i class="fas fa-book"></i>
                        <h3>Detail Penilaian</h3>
                    </div>
                    <div class="form-card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Tanggal Penilaian
                                </label>
                                <input type="text" class="modern-input" value="{{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-book"></i> Mata Pelajaran
                                </label>
                                <input type="text" class="modern-input" value="{{ $mapel }}" readonly>
                            </div>
                            <div class="form-group full-width">
                                <label class="form-label">
                                    <i class="fas fa-file-alt"></i> Materi / Topik Penilaian <span style="color: #ef4444;">*</span>
                                </label>
                                <textarea name="materi" class="modern-input" rows="3" required placeholder="Masukkan materi penilaian...">{{ old('materi', $nilaiData->materi ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STUDENT SECTION -->
                <div class="student-section">
                    <div class="section-header">
                        <h2><i class="fas fa-user-graduate"></i> Data Siswa</h2>
                        <span class="badge">1 Siswa</span>
                    </div>

                    <div class="student-card">
                        <div class="student-card-header">
                            @php
                                $initials = collect(explode(' ', $siswa->nama))
                                    ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                                    ->take(2)
                                    ->join('');
                                $hasFoto = $siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto);
                            @endphp
                            <div class="student-avatar">
                                @if($hasFoto)
                                    <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}">
                                @else
                                    {{ $initials ?: 'S' }}
                                @endif
                            </div>
                            <div class="student-info">
                                <h4 class="student-name">{{ $siswa->nama }}</h4>
                                <div class="student-details">
                                    NIS: {{ $siswa->nis ?? '-' }} | NISN: {{ $siswa->nisn }}
                                </div>
                            </div>
                        </div>

                        <div class="student-card-body">
                            <div class="info-row">
                                <span class="info-label">
                                    <i class="fas fa-graduation-cap"></i> Angkatan
                                </span>
                                <span class="info-value">{{ $siswa->angkatan_masuk ?? '-' }}</span>
                            </div>

                            <div class="input-row">
                                <div class="input-group">
                                    <label class="input-label">
                                        <i class="fas fa-star"></i> Nilai <span style="color: #ef4444;">*</span>
                                    </label>
                                    <input type="number" name="nilai" class="nilai-input" 
                                           min="0" max="100" step="0.01"
                                           value="{{ old('nilai', $nilaiData->nilai ?? '') }}"
                                           placeholder="0-100" required>
                                </div>
                            </div>

                            <div class="input-row">
                                <div class="input-group">
                                    <label class="input-label">
                                        <i class="fas fa-comment"></i> Keterangan
                                    </label>
                                    <textarea name="keterangan" class="keterangan-input" rows="3" 
                                              placeholder="Berikan catatan penilaian...">{{ old('keterangan', $nilaiData->keterangan ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
