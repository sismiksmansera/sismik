@extends('layouts.app')

@section('title', 'Tambah Nilai Siswa')

@push('styles')
<style>
/* HEADER SECTION - Green gradient */
.tambah-nilai-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    color: white;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

.header-icon {
    width: 70px;
    height: 70px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
}

.header-text h1 {
    margin: 0 0 8px 0;
    font-size: 24px;
    font-weight: 700;
}

.header-text p {
    margin: 0;
    font-size: 14px;
    opacity: 0.9;
}

/* FORM CARD */
.form-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 25px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.form-card-header {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    padding: 18px 25px;
    color: white;
    display: flex;
    align-items: center;
    gap: 12px;
}

.form-card-header i { font-size: 18px; }
.form-card-header h3 { margin: 0; font-size: 16px; font-weight: 600; }

.form-card-body {
    padding: 25px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    font-weight: 600;
    color: #374151;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-label i { color: #059669; font-size: 14px; }

.modern-input {
    padding: 12px 15px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s ease;
    width: 100%;
    box-sizing: border-box;
}

.modern-input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* ACTION BUTTONS */
.action-buttons {
    display: flex;
    gap: 12px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
    color: white;
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
    padding: 18px 25px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-header h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
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
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1px solid #bbf7d0;
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
    width: 55px;
    height: 55px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    overflow: hidden;
}

.student-avatar img { width: 100%; height: 100%; object-fit: cover; }

.student-info { flex: 1; }
.student-info h4 {
    margin: 0 0 4px 0;
    font-size: 16px;
    font-weight: 700;
}
.student-info .student-details {
    display: flex;
    flex-direction: column;
    gap: 2px;
    font-size: 12px;
    opacity: 0.9;
}

.student-card-body {
    padding: 20px;
    background: white;
}

.input-row {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}

.input-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
}

.input-label {
    font-size: 13px;
    font-weight: 600;
    color: #059669;
    display: flex;
    align-items: center;
    gap: 6px;
}

.input-label i { color: #047857; font-size: 12px; }

.nilai-input {
    padding: 12px 15px;
    border: 1px solid #bbf7d0;
    border-radius: 10px;
    font-size: 16px;
    text-align: center;
    font-weight: 700;
    color: #059669;
    transition: all 0.3s ease;
}

.nilai-input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.input-keterangan {
    padding: 12px 15px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    resize: vertical;
    min-height: 80px;
    font-family: inherit;
}

.input-keterangan:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .tambah-nilai-header { padding: 20px 15px; }
    .header-content { flex-direction: column; text-align: center; }
    .header-icon { width: 60px; height: 60px; font-size: 24px; }
    .header-text h1 { font-size: 20px; }
    .header-text p { font-size: 12px; }
    
    .form-grid { grid-template-columns: 1fr; }
    .form-card-body { padding: 15px; }
    
    .action-buttons { flex-direction: column; }
    .btn { width: 100%; justify-content: center; }
    
    .student-card { margin: 15px; }
    .student-card-header { flex-direction: column; text-align: center; }
    
    .input-row { flex-direction: column; gap: 15px; }
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')
    
    <div class="main-content">
        <div class="tambah-nilai-page">
            <!-- HEADER SECTION -->
            <div class="tambah-nilai-header">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="header-text">
                        <h1>Tambah Nilai Siswa</h1>
                        <p>
                            <strong>Siswa:</strong> {{ $siswa->nama }} &nbsp;|&nbsp;
                            <strong>Rombel:</strong> {{ $namaRombel }} &nbsp;|&nbsp;
                            <strong>Mapel:</strong> {{ $mapel }} &nbsp;|&nbsp;
                            <strong>Periode:</strong> {{ $tahunPelajaran }} - {{ ucfirst($semesterAktif) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- FORM -->
            <form action="{{ route('guru.tambah-nilai.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_rombel" value="{{ $idRombel }}">
                <input type="hidden" name="mapel" value="{{ $mapel }}">
                <input type="hidden" name="nama_rombel" value="{{ $namaRombel }}">
                <input type="hidden" name="guru" value="{{ $namaGuru }}">
                <input type="hidden" name="tahun_pelajaran" value="{{ $tahunPelajaran }}">
                <input type="hidden" name="semester" value="{{ $semesterAktif }}">
                <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">
                <input type="hidden" name="nis" value="{{ $siswa->nis }}">
                <input type="hidden" name="nama_siswa" value="{{ $siswa->nama }}">

                <!-- FORM DETAIL -->
                <div class="form-card">
                    <div class="form-card-header">
                        <i class="fas fa-edit"></i>
                        <h3>Detail Penilaian</h3>
                    </div>
                    <div class="form-card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Tanggal Penilaian
                                </label>
                                <input type="date" name="tanggal_penilaian" class="modern-input" required 
                                       value="{{ date('Y-m-d') }}" min="{{ $minDate }}" max="{{ $maxDate }}">
                            </div>
                            <div class="form-group full-width">
                                <label class="form-label">
                                    <i class="fas fa-book"></i> Materi / Topik Penilaian
                                </label>
                                <textarea name="materi" class="modern-input" rows="2" 
                                          placeholder="Masukkan materi penilaian" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="action-buttons">
                    <a href="{{ route('guru.lihat-nilai', ['id_rombel' => $idRombel, 'mapel' => $mapel]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Nilai
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Nilai
                    </button>
                </div>

                <!-- STUDENT SECTION -->
                <div class="student-section">
                    <div class="section-header">
                        <h2><i class="fas fa-user-graduate"></i> Data Siswa</h2>
                        <span class="badge">1 Siswa</span>
                    </div>

                    <div class="student-card">
                        <div class="student-card-header">
                            <div class="student-avatar">
                                @php
                                    $hasFoto = $siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto);
                                    $initials = collect(explode(' ', $siswa->nama))->map(fn($p) => strtoupper(substr($p, 0, 1)))->take(2)->join('');
                                @endphp
                                @if($hasFoto)
                                    <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}">
                                @else
                                    {{ $initials ?: 'S' }}
                                @endif
                            </div>
                            <div class="student-info">
                                <h4>{{ $siswa->nama }}</h4>
                                <div class="student-details">
                                    <span>NIS: {{ $siswa->nis }}</span>
                                    <span>NISN: {{ $siswa->nisn }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="student-card-body">
                            <div class="input-row">
                                <div class="input-group">
                                    <label class="input-label">
                                        <i class="fas fa-star"></i> Nilai
                                    </label>
                                    <input type="number" name="nilai" class="nilai-input" 
                                           min="0" max="100" step="0.01" placeholder="0-100" required>
                                </div>
                            </div>

                            <div class="input-row">
                                <div class="input-group">
                                    <label class="input-label">
                                        <i class="fas fa-comment"></i> Keterangan
                                    </label>
                                    <textarea name="keterangan" class="input-keterangan" 
                                              rows="3" placeholder="Berikan catatan penilaian (opsional)..."></textarea>
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
