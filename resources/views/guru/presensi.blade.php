@extends('layouts.app')

@section('title', 'Presensi Siswa | SISMIK')

<style>
    /* ============================
       PRESENSI SISWA STYLES
    ============================ */
    
    /* HEADER SECTION - Green gradient like dashboard */
    .presensi-header-section {
        background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 20px;
        text-align: center;
        color: white;
        box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
    }
    
    .presensi-header-section .header-icon-large {
        font-size: 50px;
        margin-bottom: 10px;
        opacity: 0.9;
    }
    
    .presensi-header-section .page-title-rekap {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* STATS GRID */
    .presensi-siswa .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .presensi-siswa .stat-card {
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

    .presensi-siswa .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border-color: #10b981;
    }

    .presensi-siswa .stat-icon {
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

    .presensi-siswa .stat-icon.primary { background: linear-gradient(135deg, #059669, #047857); }
    .presensi-siswa .stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
    .presensi-siswa .stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }

    .presensi-siswa .stat-info { flex: 1; min-width: 0; }
    .presensi-siswa .stat-info h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #333333;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .presensi-siswa .stat-info p {
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

    .btn-jam {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-jam:hover {
        background: linear-gradient(135deg, #d97706, #b45309);
        transform: translateY(-2px);
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
        border-color: #007bff;
        box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.2);
        outline: none;
    }

    .jam-input-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
        flex: 1;
    }

    .jam-info-text {
        font-size: 11px;
        color: #6b7280;
    }

    /* PRESENSI CARDS */
    :root {
        --hadir-color: #28a745;
        --dispen-color: #17a2b8;
        --izin-color: #ffc107;
        --sakit-color: #fd7e14;
        --alfa-color: #dc3545;
        --bolos-color: #6c757d;
    }

    .presensi-cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .presensi-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 3px solid #e9ecef;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .presensi-card.hadir { border-color: #28a745 !important; background: linear-gradient(135deg, #f8fff9, #f0fff4) !important; }
    .presensi-card.dispen { border-color: #17a2b8 !important; background: linear-gradient(135deg, #f0fdff, #e6f7ff) !important; }
    .presensi-card.izin { border-color: #ffc107 !important; background: linear-gradient(135deg, #fffdf0, #fff9e6) !important; }
    .presensi-card.sakit { border-color: #fd7e14 !important; background: linear-gradient(135deg, #fff5f0, #fff0e6) !important; }
    .presensi-card.alfa { border-color: #dc3545 !important; background: linear-gradient(135deg, #fff0f0, #ffe6e6) !important; }
    .presensi-card.bolos { border-color: #6c757d !important; background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important; }

    .presensi-card .card-header {
        display: flex;
        align-items: center;
        padding: 15px;
        gap: 12px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .student-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        border: 2px solid transparent;
        background: linear-gradient(135deg, #007bff, #0056b3);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .student-avatar:hover { transform: scale(1.05); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }

    .student-avatar-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
    .student-avatar:hover .student-avatar-img { transform: scale(1.1); }

    .student-avatar-initial {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
        background: linear-gradient(135deg, #007bff, #0056b3);
    }

    .presensi-card.hadir .student-avatar { border-color: var(--hadir-color); }
    .presensi-card.dispen .student-avatar { border-color: var(--dispen-color); }
    .presensi-card.izin .student-avatar { border-color: var(--izin-color); }
    .presensi-card.sakit .student-avatar { border-color: var(--sakit-color); }
    .presensi-card.alfa .student-avatar { border-color: var(--alfa-color); }
    .presensi-card.bolos .student-avatar { border-color: var(--bolos-color); }

    .student-info { flex: 1; min-width: 0; }
    .student-name { margin: 0; font-size: 14px; font-weight: 700; color: #2c3e50; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .student-nisn, .student-agama { margin: 2px 0 0 0; font-size: 11px; color: #6c757d; }

    .presensi-card .card-body { padding: 12px 15px; }

    .presensi-status-clickable {
        padding: 10px;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 8px;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .presensi-status-clickable:hover {
        background: rgba(255, 255, 255, 0.9);
        border-color: #007bff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .status-content { display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 3px; }
    .status-text { color: #6c757d; font-size: 12px; }
    .status-value { font-size: 13px; font-weight: 600; }
    .edit-icon { color: #6c757d; font-size: 10px; }
    .click-hint { color: #9ca3af; font-size: 10px; }

    .presensi-card.hadir .status-value { color: #28a745 !important; }
    .presensi-card.dispen .status-value { color: #17a2b8 !important; }
    .presensi-card.izin .status-value { color: #ffc107 !important; }
    .presensi-card.sakit .status-value { color: #fd7e14 !important; }
    .presensi-card.alfa .status-value { color: #dc3545 !important; }
    .presensi-card.bolos .status-value { color: #6c757d !important; }

    .card-status { position: absolute; top: 10px; right: 10px; }
    .status-indicator {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: white;
    }

    .status-indicator.hadir { background: #28a745 !important; }
    .status-indicator.dispen { background: #17a2b8 !important; }
    .status-indicator.izin { background: #ffc107 !important; color: #212529 !important; }
    .status-indicator.sakit { background: #fd7e14 !important; }
    .status-indicator.alfa { background: #dc3545 !important; }
    .status-indicator.bolos { background: #6c757d !important; }

    /* MODAL STYLES */
    .jam-modal-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }

    .jam-modal-option {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .jam-modal-checkbox { transform: scale(1.2); cursor: pointer; }
    .jam-modal-label { font-size: 14px; font-weight: 500; cursor: pointer; }

    .presensi-modal-options-compact {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }

    .presensi-modal-option-compact {
        position: relative;
        cursor: pointer;
        margin: 0;
        padding: 0;
        transition: all 0.2s ease;
    }

    .modal-option-content-compact {
        padding: 10px 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        background: white;
        transition: all 0.2s ease;
        min-width: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .modal-option-content-compact:hover { transform: translateY(-2px); box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1); }

    .modal-option-content-compact i { font-size: 20px; display: block; line-height: 1; }
    .modal-option-content-compact span { font-weight: 600; font-size: 12px; line-height: 1; text-align: center; white-space: nowrap; }

    .presensi-modal-option-compact.hadir .modal-option-content-compact { color: #28a745; border-color: rgba(40, 167, 69, 0.3); }
    .presensi-modal-option-compact.dispen .modal-option-content-compact { color: #17a2b8; border-color: rgba(23, 162, 184, 0.3); }
    .presensi-modal-option-compact.izin .modal-option-content-compact { color: #ffc107; border-color: rgba(255, 193, 7, 0.3); }
    .presensi-modal-option-compact.sakit .modal-option-content-compact { color: #fd7e14; border-color: rgba(253, 126, 20, 0.3); }
    .presensi-modal-option-compact.alfa .modal-option-content-compact { color: #dc3545; border-color: rgba(220, 53, 69, 0.3); }
    .presensi-modal-option-compact.bolos .modal-option-content-compact { color: #6c757d; border-color: rgba(108, 117, 125, 0.3); }

    .presensi-modal-option-compact.selected .modal-option-content-compact { border-width: 3px; transform: scale(0.95); }
    .presensi-modal-option-compact.hadir.selected .modal-option-content-compact { background: #28a745; color: white; border-color: #28a745; }
    .presensi-modal-option-compact.dispen.selected .modal-option-content-compact { background: #17a2b8; color: white; border-color: #17a2b8; }
    .presensi-modal-option-compact.izin.selected .modal-option-content-compact { background: #ffc107; color: #212529; border-color: #ffc107; }
    .presensi-modal-option-compact.sakit.selected .modal-option-content-compact { background: #fd7e14; color: white; border-color: #fd7e14; }
    .presensi-modal-option-compact.alfa.selected .modal-option-content-compact { background: #dc3545; color: white; border-color: #dc3545; }
    .presensi-modal-option-compact.bolos.selected .modal-option-content-compact { background: #6c757d; color: white; border-color: #6c757d; }

    /* Modal Photo */
    .student-photo-section { padding: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px; border: 2px solid #dee2e6; }
    .photo-container-large { position: relative; display: inline-block; margin-bottom: 15px; border-radius: 15px; overflow: hidden; transition: all 0.3s ease; cursor: pointer; }
    .photo-container-large:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); }

    #modalLargePhotoContainer {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: 4px solid #10b981;
    }

    .large-photo-img { width: 100%; height: 100%; object-fit: cover; }
    .large-photo-initial {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
        font-weight: bold;
        color: white;
        background: linear-gradient(135deg, #007bff, #0056b3);
    }

    .btn-modern { display: inline-flex; align-items: center; justify-content: center; padding: 10px 24px; font-weight: 600; border-radius: 10px; border: none; cursor: pointer; font-size: 15px; transition: all 0.25s ease; text-decoration: none; }
    .btn-modern.btn-primary { background: linear-gradient(135deg, #007bff, #0d6efd); color: white; }
    .btn-modern.btn-primary:hover { background: linear-gradient(135deg, #0056b3, #0069d9); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3); }
    .btn-modern.btn-outline-secondary { background: transparent; border: 2px solid #6c757d; color: #6c757d; }
    .btn-modern.btn-outline-secondary:hover { background: #6c757d; color: white; }

    /* EMPTY STATE */
    .empty-state { text-align: center; padding: 40px; }
    .empty-state i { font-size: 48px; color: #d1d5db; margin-bottom: 15px; }
    .empty-state h4 { color: #4b5563; }
    .empty-state p { color: #9ca3af; }

    /* RESPONSIVE - MOBILE */
    @media (max-width: 768px) {
        .presensi-header-section {
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .presensi-header-section .header-icon-large {
            font-size: 36px;
            margin-bottom: 8px;
        }
        
        .presensi-header-section .page-title-rekap {
            font-size: 18px;
        }
        
        .presensi-siswa .stats-grid { 
            grid-template-columns: repeat(3, 1fr); 
            gap: 8px; 
        }
        .presensi-siswa .stat-card { 
            flex-direction: column; 
            text-align: center; 
            padding: 10px; 
            gap: 6px; 
        }
        .presensi-siswa .stat-icon { width: 32px; height: 32px; font-size: 12px; }
        .presensi-siswa .stat-info h3 { font-size: 10px; white-space: normal; line-height: 1.2; }
        .presensi-siswa .stat-info p { font-size: 8px; }
        
        .card-modern .card-body { padding: 15px; }
        
        .btn-action-group { flex-direction: column; gap: 10px; }
        .btn-back, .btn-save { width: 100%; justify-content: center; font-size: 12px; padding: 10px 16px; }
        
        .form-section { padding: 12px; }
        .filter-group { flex-direction: row; align-items: center; gap: 8px; margin-bottom: 8px; }
        .filter-label { min-width: 90px; font-size: 11px; }
        .modern-input { font-size: 12px; padding: 8px 10px; }
        .btn-jam { font-size: 11px; padding: 8px 12px; }
        .jam-info-text { font-size: 10px; }
        
        .presensi-cards-container { grid-template-columns: 1fr; gap: 12px; margin-top: 15px; }
        
        .presensi-card .card-header { padding: 10px 12px; gap: 10px; }
        .student-avatar { width: 40px; height: 40px; }
        .student-avatar-initial { font-size: 14px; }
        .student-name { font-size: 12px; }
        .student-nisn, .student-agama { font-size: 10px; }
        
        .presensi-card .card-body { padding: 8px 12px; }
        .presensi-status-clickable { padding: 8px; }
        .status-text { font-size: 10px; }
        .status-value { font-size: 11px; }
        .click-hint { font-size: 9px; }
        
        .card-status { top: 8px; right: 8px; }
        .status-indicator { font-size: 8px; padding: 2px 8px; }
        
        /* Modal mobile */
        #modalLargePhotoContainer { width: 100px; height: 100px; }
        .large-photo-initial { font-size: 40px; }
        .student-photo-section { padding: 15px; }
        #modalStudentName { font-size: 16px !important; }
        
        .modal-option-content-compact { padding: 8px 10px; min-width: 60px; }
        .modal-option-content-compact i { font-size: 16px; }
        .modal-option-content-compact span { font-size: 10px; }
    }
</style>

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')

    <div class="main-content presensi-siswa">
        <!-- Header Section - Green Gradient -->
        <div class="presensi-header-section">
            <div class="header-icon-large">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <h1 class="page-title-rekap">PRESENSI SISWA</h1>
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
                <form action="{{ route('guru.presensi.store') }}" method="POST" id="formPresensi">
                    @csrf
                    <input type="hidden" name="id_rombel" value="{{ $idRombel }}">
                    <input type="hidden" name="mapel" value="{{ $mapel }}">
                    <input type="hidden" name="hidden_koordinat" id="hidden_koordinat" value="">
                    <input type="hidden" name="from" value="{{ $fromPage }}">

                    <!-- Action Buttons -->
                    <div class="btn-action-group">
                        @php
                            $backUrl = route('guru.dashboard');
                            if ($fromPage === 'tugas-mengajar') {
                                $backUrl = route('guru.tugas-mengajar');
                            } elseif ($fromPage === 'presensi-selector') {
                                $backUrl = route('guru.presensi-selector');
                            }
                        @endphp
                        <a href="{{ $backUrl }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn-save" id="btnSimpanPresensi">
                            <i class="fas fa-save"></i> Simpan Presensi
                        </button>
                    </div>

                    <!-- Form Input Presensi -->
                    <div class="form-section mb-4">
                        <div class="filter-group">
                            <label class="filter-label">Tanggal Presensi
                                @if($isLockedMode)
                                    <span style="color: #f59e0b; font-size: 11px;"><i class="fas fa-lock"></i> Terkunci</span>
                                @endif
                            </label>
                            <input type="date" name="tanggal_presensi" class="form-control modern-input"
                                value="{{ $isLockedMode ? $lockedTanggal : date('Y-m-d') }}"
                                min="{{ $minDate }}" max="{{ $maxDate }}"
                                {{ $isLockedMode ? 'readonly style="background-color: #f3f4f6; cursor: not-allowed;"' : '' }} required>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Jam Pelajaran
                                @if($isLockedMode)
                                    <span style="color: #f59e0b; font-size: 11px;"><i class="fas fa-lock"></i> Terkunci</span>
                                @endif
                            </label>
                            <div class="jam-input-wrapper">
                                @if($isLockedMode)
                                    <button type="button" class="btn-jam" disabled style="background-color: #f3f4f6; cursor: not-allowed; opacity: 0.8;">
                                        <i class="fas fa-clock"></i> Jam {{ $lockedJamKe }}
                                    </button>
                                    @foreach($lockedJamList as $jam)
                                        <input type="hidden" name="jam_pelajaran[]" value="{{ $jam }}">
                                    @endforeach
                                @else
                                    <button type="button" class="btn-jam" onclick="openJamModal()">
                                        <i class="fas fa-clock"></i> Pilih Jam
                                    </button>
                                @endif
                            </div>
                        </div>
                        <span id="selectedJamInfo" class="jam-info-text">{{ $isLockedMode ? 'Jam ke-' . $lockedJamKe . ' (terkunci dari jadwal)' : 'Belum ada jam dipilih' }}</span>
                    </div>

                    <!-- Student Presensi Cards -->
                    <div class="presensi-cards-container">
                        @if($siswaList->count() > 0)
                            @foreach($siswaList as $s)
                                @php
                                    $savedStatus = 'H';
                                    $savedStatusText = 'Hadir';
                                    $savedStatusClass = 'hadir';
                                    if ($isViewMode && isset($existingPresensi[$s->nisn]) && !empty($lockedJamList)) {
                                        $savedPresensi = $existingPresensi[$s->nisn];
                                        // Get status from specific jam_ke column, not generic 'presensi'
                                        $firstJam = min($lockedJamList);
                                        $jamColumn = 'jam_ke_' . $firstJam;
                                        $savedStatus = $savedPresensi[$jamColumn] ?? 'H';
                                        $statusMap = [
                                            'H' => ['text' => 'Hadir', 'class' => 'hadir'],
                                            'S' => ['text' => 'Sakit', 'class' => 'sakit'],
                                            'I' => ['text' => 'Izin', 'class' => 'izin'],
                                            'A' => ['text' => 'Alpha', 'class' => 'alfa'],
                                            'D' => ['text' => 'Dispensasi', 'class' => 'dispen'],
                                            'B' => ['text' => 'Bolos', 'class' => 'bolos']
                                        ];
                                        $savedStatusText = $statusMap[$savedStatus]['text'] ?? 'Hadir';
                                        $savedStatusClass = $statusMap[$savedStatus]['class'] ?? 'hadir';
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
                                <div class="presensi-card {{ $savedStatusClass }}" data-siswa-id="{{ $s->id }}">
                                    <div class="card-header">
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

                                    <div class="card-body">
                                        <div class="presensi-status-clickable" onclick="openPresensiModal({{ $s->id }}, '{{ $s->nama }}', '{{ $initials }}', '{{ $fotoExists ? $fotoPath : '' }}')">
                                            <div class="status-content">
                                                <span class="status-text">Status: </span>
                                                <strong id="statusText_{{ $s->id }}" class="status-value {{ $savedStatusClass }}">{{ $savedStatusText }}</strong>
                                                <i class="fas fa-pencil-alt edit-icon"></i>
                                            </div>
                                            <small class="click-hint">Klik untuk mengubah status</small>
                                        </div>
                                    </div>

                                    <div class="card-status">
                                        <span id="statusIndicator_{{ $s->id }}" class="status-indicator {{ $savedStatusClass }}">{{ strtoupper($savedStatusText) }}</span>
                                    </div>
                                    <input type="hidden" name="presensi[{{ $s->id }}]" value="{{ $savedStatus }}" id="presensiInput_{{ $s->id }}">
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state text-center py-5">
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

<!-- Modal Pilih Jam Pelajaran -->
<div class="modal fade" id="jamModal" tabindex="-1" aria-labelledby="jamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jamModalLabel">
                    <i class="fas fa-clock me-2"></i> Pilih Jam Pelajaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="jam-modal-container">
                    @for($i = 1; $i <= 11; $i++)
                        <label class="jam-modal-option">
                            <input type="checkbox" name="jam_pelajaran_modal[]" value="{{ $i }}" class="jam-modal-checkbox">
                            <span class="jam-modal-label">Jam ke - {{ $i }}</span>
                        </label>
                    @endfor
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modern btn-primary" onclick="saveJamSelection()">
                    <i class="fas fa-save me-1"></i> Simpan Pilihan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah Status Presensi -->
<div class="modal fade" id="presensiModal" tabindex="-1" aria-labelledby="presensiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="presensiModalLabel">
                    <i class="fas fa-user-edit me-2"></i> Ubah Status Presensi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="student-photo-section text-center mb-4">
                    <div class="photo-container-large">
                        <div id="modalLargePhotoContainer">
                            <!-- Photo will be filled by JavaScript -->
                        </div>
                    </div>
                    <h4 id="modalStudentName" class="mt-3 mb-1"></h4>
                    <p class="text-muted mb-3" id="modalStudentNISN"></p>
                </div>

                <div class="presensi-modal-options-compact">
                    <div class="row g-1 justify-content-center">
                        <div class="col-auto">
                            <label class="presensi-modal-option-compact hadir" onclick="changePresensiStatus('H')">
                                <div class="modal-option-content-compact text-center">
                                    <i class="fas fa-check-circle"></i>
                                    <span class="d-block">Hadir</span>
                                </div>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="presensi-modal-option-compact dispen" onclick="changePresensiStatus('D')">
                                <div class="modal-option-content-compact text-center">
                                    <i class="fas fa-user-tie"></i>
                                    <span class="d-block">Dispen</span>
                                </div>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="presensi-modal-option-compact izin" onclick="changePresensiStatus('I')">
                                <div class="modal-option-content-compact text-center">
                                    <i class="fas fa-clock"></i>
                                    <span class="d-block">Izin</span>
                                </div>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="presensi-modal-option-compact sakit" onclick="changePresensiStatus('S')">
                                <div class="modal-option-content-compact text-center">
                                    <i class="fas fa-first-aid"></i>
                                    <span class="d-block">Sakit</span>
                                </div>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="presensi-modal-option-compact alfa" onclick="changePresensiStatus('A')">
                                <div class="modal-option-content-compact text-center">
                                    <i class="fas fa-times-circle"></i>
                                    <span class="d-block">Alfa</span>
                                </div>
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="presensi-modal-option-compact bolos" onclick="changePresensiStatus('B')">
                                <div class="modal-option-content-compact text-center">
                                    <i class="fas fa-running"></i>
                                    <span class="d-block">Bolos</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Status akan langsung tersimpan ketika dipilih
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modern btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentSiswaId = null;
    let selectedJamPelajaran = [];

    // Get geolocation on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('hidden_koordinat').value = position.coords.latitude + ',' + position.coords.longitude;
            }, function(error) {
                console.log('Geolocation error:', error.message);
            });
        }
    });

    function openJamModal() {
        // Reset checkboxes
        document.querySelectorAll('.jam-modal-checkbox').forEach(cb => {
            cb.checked = selectedJamPelajaran.includes(parseInt(cb.value));
        });
        new bootstrap.Modal(document.getElementById('jamModal')).show();
    }

    function saveJamSelection() {
        selectedJamPelajaran = [];
        document.querySelectorAll('.jam-modal-checkbox:checked').forEach(cb => {
            selectedJamPelajaran.push(parseInt(cb.value));
        });

        // Remove old hidden inputs and add new ones
        document.querySelectorAll('input[name="jam_pelajaran[]"]').forEach(el => el.remove());
        selectedJamPelajaran.forEach(jam => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'jam_pelajaran[]';
            input.value = jam;
            document.getElementById('formPresensi').appendChild(input);
        });

        // Update display
        if (selectedJamPelajaran.length > 0) {
            selectedJamPelajaran.sort((a, b) => a - b);
            document.getElementById('selectedJamInfo').textContent = 'Jam ke-' + selectedJamPelajaran.join(', ');
        } else {
            document.getElementById('selectedJamInfo').textContent = 'Belum ada jam dipilih';
        }

        bootstrap.Modal.getInstance(document.getElementById('jamModal')).hide();
    }

    function openPresensiModal(siswaId, nama, initials, fotoPath) {
        currentSiswaId = siswaId;
        document.getElementById('modalStudentName').textContent = nama;
        
        const photoContainer = document.getElementById('modalLargePhotoContainer');
        if (fotoPath) {
            photoContainer.innerHTML = '<img src="' + fotoPath + '" alt="Foto ' + nama + '" class="large-photo-img">';
        } else {
            photoContainer.innerHTML = '<div class="large-photo-initial">' + initials + '</div>';
        }

        // Highlight current status
        const currentStatus = document.getElementById('presensiInput_' + siswaId).value;
        document.querySelectorAll('.presensi-modal-option-compact').forEach(opt => {
            opt.classList.remove('selected');
        });
        const statusMap = { 'H': 'hadir', 'D': 'dispen', 'I': 'izin', 'S': 'sakit', 'A': 'alfa', 'B': 'bolos' };
        const statusClass = statusMap[currentStatus] || 'hadir';
        document.querySelector('.presensi-modal-option-compact.' + statusClass)?.classList.add('selected');

        new bootstrap.Modal(document.getElementById('presensiModal')).show();
    }

    function changePresensiStatus(status) {
        if (!currentSiswaId) return;

        const statusMap = {
            'H': { text: 'Hadir', class: 'hadir' },
            'D': { text: 'Dispensasi', class: 'dispen' },
            'I': { text: 'Izin', class: 'izin' },
            'S': { text: 'Sakit', class: 'sakit' },
            'A': { text: 'Alpha', class: 'alfa' },
            'B': { text: 'Bolos', class: 'bolos' }
        };

        const info = statusMap[status];
        if (!info) return;

        // Update hidden input
        document.getElementById('presensiInput_' + currentSiswaId).value = status;

        // Update status text
        const statusText = document.getElementById('statusText_' + currentSiswaId);
        statusText.textContent = info.text;
        statusText.className = 'status-value ' + info.class;

        // Update status indicator
        const statusIndicator = document.getElementById('statusIndicator_' + currentSiswaId);
        statusIndicator.textContent = info.text.toUpperCase();
        statusIndicator.className = 'status-indicator ' + info.class;

        // Update card class
        const card = document.querySelector('.presensi-card[data-siswa-id="' + currentSiswaId + '"]');
        card.className = 'presensi-card ' + info.class;

        // Update modal selection
        document.querySelectorAll('.presensi-modal-option-compact').forEach(opt => {
            opt.classList.remove('selected');
        });
        document.querySelector('.presensi-modal-option-compact.' + info.class)?.classList.add('selected');

        // Auto close modal after selection
        setTimeout(() => {
            bootstrap.Modal.getInstance(document.getElementById('presensiModal'))?.hide();
        }, 300);
    }

    function showFullPhoto(el, siswaId, nama, nisn) {
        // Prevent opening presensi modal when clicking on photo
        event.stopPropagation();
        // Could implement full photo modal here if needed
        console.log('Show full photo for:', nama, nisn);
    }
</script>
@endpush
