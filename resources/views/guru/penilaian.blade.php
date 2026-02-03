@extends('layouts.app')

@section('title', 'Input Penilaian | SISMIK')

@push('styles')
<style>
    /* ============================
       INPUT PENILAIAN STYLES
    ============================ */
    
    /* HEADER SECTION - Green gradient like dashboard */
    .penilaian-header-section {
        background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 20px;
        text-align: center;
        color: white;
        box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
    }
    
    .penilaian-header-section .header-icon-large {
        font-size: 50px;
        margin-bottom: 10px;
        opacity: 0.9;
    }
    
    .penilaian-header-section .page-title-rekap {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* STATS GRID */
    .input-penilaian .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .input-penilaian .stat-card {
        background: var(--bg-card, #ffffff);
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .input-penilaian .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border-color: #10b981;
    }

    .input-penilaian .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #ffffff;
        flex-shrink: 0;
    }

    .input-penilaian .stat-icon.primary { background: linear-gradient(135deg, #059669, #047857); }
    .input-penilaian .stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
    .input-penilaian .stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }

    .input-penilaian .stat-info { flex: 1; min-width: 0; }
    .input-penilaian .stat-info h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #333333;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .input-penilaian .stat-info p {
        margin: 4px 0 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 500;
    }

    /* CARD MODERN */
    .card-modern {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        border: none;
        margin-bottom: 25px;
    }

    .card-modern .card-body { padding: 25px; }

    /* BUTTON STYLING */
    .btn-action-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: linear-gradient(135deg, #4b5563, #374151);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
        color: white;
    }

    .btn-save {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        background: linear-gradient(135deg, #047857, #065f46);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
    }

    /* FORM SECTION */
    .form-section {
        background: #f8fafc;
        padding: 15px 20px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
    }

    .filter-group {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .filter-group:last-child { margin-bottom: 0; }

    .filter-label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        min-width: 130px;
        flex-shrink: 0;
    }

    .modern-input {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
    }

    .modern-input:focus {
        border-color: #059669;
        box-shadow: 0 0 0 0.15rem rgba(5, 150, 105, 0.2);
        outline: none;
    }

    textarea.modern-input {
        resize: vertical;
        min-height: 60px;
    }

    /* STUDENT CARDS GRID */
    .students-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .student-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .student-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        border-color: #059669;
    }

    .student-card-header {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        gap: 15px;
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
    }

    .student-avatar {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid rgba(255, 255, 255, 0.3);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .student-avatar:hover { transform: scale(1.05); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2); }

    .student-avatar-img { width: 100%; height: 100%; object-fit: cover; }
    .student-avatar-initial {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .student-info { flex: 1; min-width: 0; }
    .student-name { margin: 0; font-size: 15px; font-weight: 700; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .student-nisn, .student-agama { margin: 2px 0 0 0; font-size: 11px; color: rgba(255, 255, 255, 0.85); }

    .student-card-body { padding: 20px; }

    .input-row { margin-bottom: 15px; }
    .input-row:last-child { margin-bottom: 0; }

    .input-group { display: flex; flex-direction: column; gap: 6px; }
    .input-group.full-width { width: 100%; }

    .input-label {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .input-label i { color: #059669; }

    .nilai-input {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        text-align: center;
        transition: all 0.3s ease;
    }

    .nilai-input:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15);
        outline: none;
    }

    .input-keterangan {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        resize: vertical;
        min-height: 50px;
    }

    .input-keterangan:focus {
        border-color: #059669;
        box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.1);
        outline: none;
    }

    /* SECTION HEADER */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 25px;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e5e7eb;
    }

    .section-header h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-header h2 i { color: #059669; }

    .section-header .badge {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    /* EMPTY STATE */
    .empty-state { text-align: center; padding: 40px; }
    .empty-state i { font-size: 48px; color: #d1d5db; margin-bottom: 15px; }
    .empty-state h4 { color: #4b5563; margin-bottom: 10px; }
    .empty-state p { color: #9ca3af; }

    /* RESPONSIVE - MOBILE */
    @media (max-width: 768px) {
        .penilaian-header-section {
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .penilaian-header-section .header-icon-large {
            font-size: 36px;
            margin-bottom: 8px;
        }
        
        .penilaian-header-section .page-title-rekap {
            font-size: 18px;
        }
        
        .input-penilaian .stats-grid { 
            grid-template-columns: repeat(3, 1fr); 
            gap: 8px; 
        }
        .input-penilaian .stat-card { 
            flex-direction: column; 
            text-align: center; 
            padding: 10px; 
            gap: 6px; 
        }
        .input-penilaian .stat-icon { width: 32px; height: 32px; font-size: 12px; }
        .input-penilaian .stat-info h3 { font-size: 10px; white-space: normal; line-height: 1.2; }
        .input-penilaian .stat-info p { font-size: 8px; }
        
        .card-modern .card-body { padding: 15px; }
        
        .btn-action-group { flex-direction: column; gap: 10px; }
        .btn-back, .btn-save { width: 100%; justify-content: center; font-size: 12px; padding: 10px 16px; }
        
        .form-section { padding: 12px; }
        .filter-group { flex-direction: column; align-items: flex-start; gap: 6px; }
        .filter-label { min-width: auto; font-size: 11px; }
        .modern-input { font-size: 12px; padding: 8px 10px; }
        
        .section-header { margin-top: 15px; margin-bottom: 10px; }
        .section-header h2 { font-size: 14px; }
        .section-header .badge { font-size: 10px; padding: 4px 10px; }
        
        .students-cards-grid { grid-template-columns: 1fr; gap: 12px; margin-top: 15px; }
        
        .student-card-header { padding: 10px 12px; gap: 10px; }
        .student-avatar { width: 40px; height: 40px; }
        .student-avatar-initial { font-size: 14px; }
        .student-name { font-size: 12px; }
        .student-nisn, .student-agama { font-size: 10px; }
        
        .student-card-body { padding: 12px; }
        .input-label { font-size: 10px; }
        .nilai-input { font-size: 14px; padding: 8px 10px; }
        .input-keterangan { font-size: 11px; padding: 8px 10px; min-height: 40px; }
        
        .empty-state { padding: 25px; }
        .empty-state i { font-size: 36px; }
        .empty-state h4 { font-size: 14px; }
        .empty-state p { font-size: 12px; }
        
        /* Modal mobile */
        .initial-full { width: 100px; height: 100px; font-size: 40px; }
        .student-info-foto { padding: 15px; }
        .student-info-foto h4 { font-size: 14px; }
        .student-info-foto p { font-size: 12px; }
    }

    /* Modal Photo */
    .foto-full-size {
        max-width: 100%;
        max-height: 70vh;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        object-fit: contain;
        background: #f8f9fa;
        padding: 10px;
    }

    .initial-full {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 80px;
        font-weight: bold;
        color: white;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .student-info-foto {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
        border-left: 4px solid #667eea;
    }

    .student-info-foto h4 {
        color: #2c3e50;
        margin-bottom: 8px;
        font-weight: 700;
    }

    .student-info-foto p {
        color: #6c757d;
        margin-bottom: 5px;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')

    <div class="main-content input-penilaian">
        <!-- Header Section - Green Gradient -->
        <div class="penilaian-header-section">
            <div class="header-icon-large">
                <i class="fas fa-edit"></i>
            </div>
            <h1 class="page-title-rekap">INPUT PENILAIAN</h1>
        </div>

        <!-- Info Cards Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-school"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $namaRombel }}</h3>
                    <p>Rombel</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $mapel }}</h3>
                    <p>Mata Pelajaran</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $tahunPelajaranAktif }}</h3>
                    <p>{{ $semesterAktif }}</p>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card-modern">
            <div class="card-body">
                <form action="{{ route('guru.penilaian.store') }}" method="POST" id="formPenilaian">
                    @csrf
                    <input type="hidden" name="id_rombel" value="{{ $idRombel }}">
                    <input type="hidden" name="mapel" value="{{ $mapel }}">
                    <input type="hidden" name="nama_rombel" value="{{ $namaRombel }}">
                    <input type="hidden" name="jam_ke" value="{{ $jamKe ?? '' }}">
                    <input type="hidden" name="from" value="{{ $fromPage }}">

                    <!-- Action Buttons -->
                    <div class="btn-action-group">
                        @php
                            $backUrl = route('guru.dashboard');
                            if ($fromPage === 'tugas-mengajar') {
                                $backUrl = route('guru.tugas-mengajar');
                            } elseif ($fromPage === 'lihat-nilai') {
                                $backUrl = route('guru.lihat-nilai', ['id_rombel' => $idRombel, 'mapel' => $mapel]);
                            }
                        @endphp
                        <a href="{{ $backUrl }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn-save" id="btnSimpanPenilaian">
                            <i class="fas fa-save"></i> Simpan Penilaian
                        </button>
                    </div>

                    <!-- Form Input Penilaian -->
                    <div class="form-section mb-4">
                        <div class="filter-group">
                            <label class="filter-label">Tanggal Penilaian
                                @if($isLockedMode)
                                    <span style="color: #f59e0b; font-size: 11px;"><i class="fas fa-lock"></i> Terkunci</span>
                                @endif
                            </label>
                            <input type="date" name="tanggal_penilaian" class="form-control modern-input"
                                value="{{ $isLockedMode ? $lockedTanggal : date('Y-m-d') }}"
                                min="{{ $minDate }}" max="{{ $maxDate }}"
                                {{ $isLockedMode ? 'readonly style="background-color: #f3f4f6; cursor: not-allowed;"' : '' }} required>
                        </div>
                        @if(!empty($jamKe))
                        <div class="filter-group">
                            <label class="filter-label">Jam Pelajaran
                                <span style="color: #f59e0b; font-size: 11px;"><i class="fas fa-lock"></i> Terkunci</span>
                            </label>
                            <div style="flex: 1; padding: 10px 14px; background: #f3f4f6; border-radius: 8px; border: 1px solid #e5e7eb; font-weight: 600; color: #374151;">
                                <i class="fas fa-clock" style="color: #8b5cf6;"></i> Jam ke-{{ $jamKe }}
                            </div>
                        </div>
                        @endif
                        <div class="filter-group">
                            <label class="filter-label">Materi / Topik</label>
                            <textarea name="materi" class="modern-input" placeholder="Masukkan materi penilaian" rows="2">{{ $savedMateri ?? '' }}</textarea>
                        </div>
                    </div>

                    <!-- Section Header -->
                    <div class="section-header">
                        <h2><i class="fas fa-users"></i> Daftar Siswa</h2>
                        <span class="badge">{{ $siswaList->count() }} Siswa</span>
                    </div>

                    <!-- Student Cards -->
                    <div class="students-cards-grid">
                        @if($siswaList->count() > 0)
                            @foreach($siswaList as $s)
                                @php
                                    $savedNilai = '';
                                    $savedKeterangan = '';
                                    if ($isViewMode && isset($existingPenilaian[$s->nisn])) {
                                        $savedNilai = $existingPenilaian[$s->nisn]['nilai'] ?? '';
                                        $savedKeterangan = $existingPenilaian[$s->nisn]['keterangan'] ?? '';
                                    }
                                    // Check foto using Storage like in siswa profil page
                                    $fotoExists = !empty($s->foto) && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $s->foto);
                                    $fotoPath = $fotoExists ? asset('storage/siswa/' . $s->foto) : '';
                                    $namaParts = explode(' ', $s->nama);
                                    $initials = '';
                                    foreach ($namaParts as $part) {
                                        if (!empty($part)) {
                                            $initials .= strtoupper(substr($part, 0, 1));
                                            if (strlen($initials) >= 2) break;
                                        }
                                    }
                                    $initials = $initials ?: strtoupper(substr($s->nama, 0, 1));
                                @endphp
                                <div class="student-card" data-siswa-id="{{ $s->id }}">
                                    <div class="student-card-header">
                                        <div class="student-avatar" onclick="showFullPhoto(this, {{ $s->id }}, '{{ $s->nama }}', '{{ $s->nisn }}')">
                                            @if($fotoExists)
                                                <img src="{{ $fotoPath }}" alt="Foto {{ $s->nama }}" class="student-avatar-img"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                    data-foto-path="{{ $fotoPath }}">
                                                <div class="student-avatar-initial" style="display: none;">{{ $initials }}</div>
                                            @else
                                                <div class="student-avatar-initial">{{ $initials }}</div>
                                            @endif
                                        </div>
                                        <div class="student-info">
                                            <h4 class="student-name">{{ $s->nama ?? '' }}</h4>
                                            <p class="student-nisn">NISN: {{ $s->nisn ?? '' }}</p>
                                            @if($isMapelAgama)
                                                <p class="student-agama">Agama: {{ $s->agama ?? '' }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="student-card-body">
                                        <div class="input-row">
                                            <div class="input-group">
                                                <label class="input-label">
                                                    <i class="fas fa-star"></i> Nilai
                                                </label>
                                                <input type="number" name="nilai[{{ $s->id }}]" class="nilai-input" 
                                                    min="0" max="100" step="0.01" placeholder="0-100"
                                                    value="{{ $savedNilai }}">
                                            </div>
                                        </div>
                                        <div class="input-row">
                                            <div class="input-group full-width">
                                                <label class="input-label">
                                                    <i class="fas fa-comment"></i> Keterangan
                                                </label>
                                                <textarea name="keterangan[{{ $s->id }}]" class="input-keterangan" rows="2"
                                                    placeholder="Berikan catatan penilaian...">{{ $savedKeterangan }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hidden Inputs -->
                                    <input type="hidden" name="nis[{{ $s->id }}]" value="{{ $s->nis ?? '' }}">
                                    <input type="hidden" name="nisn[{{ $s->id }}]" value="{{ $s->nisn ?? '' }}">
                                    <input type="hidden" name="nama_siswa[{{ $s->id }}]" value="{{ $s->nama ?? '' }}">
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state text-center py-5" style="grid-column: 1 / -1;">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Tidak Ada Data Siswa</h4>
                                <p class="text-muted">
                                    @if($isMapelAgama && !empty($agamaMapel))
                                        Tidak ada siswa dengan agama {{ $agamaMapel }} ditemukan.
                                    @else
                                        Tidak ada siswa ditemukan dalam rombel ini.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Foto Full -->
<div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fotoModalLabel">
                    <i class="fas fa-user-circle me-2"></i> Foto Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="fotoModalContent">
                    <!-- Photo will be filled by JavaScript -->
                </div>
                <div class="student-info-foto mt-3" id="studentInfoModal">
                    <!-- Student info will be filled by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Show full photo modal
    function showFullPhoto(element, siswaId, nama, nisn) {
        const avatarImg = element.querySelector('.student-avatar-img');
        const avatarInitial = element.querySelector('.student-avatar-initial');
        const fotoModalContent = document.getElementById('fotoModalContent');
        const studentInfoModal = document.getElementById('studentInfoModal');
        
        if (avatarImg && avatarImg.style.display !== 'none' && avatarImg.dataset.fotoPath) {
            fotoModalContent.innerHTML = `<img src="${avatarImg.dataset.fotoPath}" alt="Foto ${nama}" class="foto-full-size">`;
        } else {
            const initial = avatarInitial ? avatarInitial.textContent : nama.charAt(0).toUpperCase();
            fotoModalContent.innerHTML = `<div class="initial-full">${initial}</div>`;
        }
        
        studentInfoModal.innerHTML = `
            <h4>${nama}</h4>
            <p><strong>NISN:</strong> ${nisn}</p>
        `;
        
        const modal = new bootstrap.Modal(document.getElementById('fotoModal'));
        modal.show();
    }
</script>
@endpush
