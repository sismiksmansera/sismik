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
        margin: 0 0 8px 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .penilaian-header-section .page-subtitle {
        font-size: 14px;
        font-weight: 500;
        margin: 0;
        color: rgba(255, 255, 255, 0.9);
    }

    /* SELECTION AND INFO ROW */
    .selection-info-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }

    .selection-card, .info-card {
        background: var(--bg-card, #ffffff);
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }

    .selection-card {
        cursor: pointer;
    }

    .selection-card:hover {
        border-color: #10b981;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.15);
    }

    .selection-card.selected {
        border: 2px solid #10b981;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    }

    .selection-card .card-icon,
    .info-card .card-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
        flex-shrink: 0;
    }

    .selection-card .card-icon.mapel-icon,
    .info-card .card-icon.mapel-icon {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }

    .selection-card .card-icon.rombel-icon,
    .info-card .card-icon.rombel-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .info-card .card-icon.period-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .card-content {
        flex: 1;
        min-width: 0;
    }

    .card-content .card-value {
        font-size: 15px;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 2px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-content .card-placeholder {
        font-size: 14px;
        color: #9ca3af;
        margin: 0 0 2px 0;
    }

    .card-content .card-label {
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin: 0;
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

    /* FORM SECTION */
    .form-section {
        background: #f8fafc;
        padding: 15px 20px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        margin-bottom: 20px;
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

    /* BUTTON STYLING */
    .btn-action-group {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 20px;
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

    .btn-save:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
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

    /* MODAL STYLES */
    .modal-option-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        max-height: 400px;
        overflow-y: auto;
        padding: 10px;
    }

    .option-card {
        background: #fff;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .option-card:hover {
        border-color: #10b981;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    }

    .option-card .option-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }

    .option-card .option-icon.mapel {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }

    .option-card .option-icon.rombel {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .option-card .option-name {
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    /* LOADING SPINNER */
    .loading-spinner {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px;
    }

    .loading-spinner .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e5e7eb;
        border-top-color: #10b981;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* STUDENTS SECTION */
    #studentsSection {
        display: none;
    }

    #studentsSection.show {
        display: block;
    }

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

        .selection-info-row {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .selection-card, .info-card {
            padding: 12px 15px;
        }

        .selection-card .card-icon,
        .info-card .card-icon {
            width: 38px;
            height: 38px;
            font-size: 16px;
        }

        .card-content .card-value {
            font-size: 13px;
        }

        .card-content .card-placeholder {
            font-size: 12px;
        }

        .card-content .card-label {
            font-size: 10px;
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

        .modal-option-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Photo Modal */
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
            <p class="page-subtitle">{{ $tahunPelajaranAktif }} - Semester {{ ucfirst($semesterAktif) }}</p>
        </div>

        <!-- Selection Cards Row -->
        <div class="selection-info-row">
            <div class="selection-card" id="mapelCard" onclick="openMapelModal()">
                <div class="card-icon mapel-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="card-content">
                    <p class="card-value" id="mapelValue" style="display: none;"></p>
                    <p class="card-placeholder" id="mapelPlaceholder">Pilih Mata Pelajaran...</p>
                    <span class="card-label">Mata Pelajaran</span>
                </div>
            </div>
            <div class="selection-card" id="rombelCard" onclick="openRombelModal()">
                <div class="card-icon rombel-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-content">
                    <p class="card-value" id="rombelValue" style="display: none;"></p>
                    <p class="card-placeholder" id="rombelPlaceholder">Pilih Rombel...</p>
                    <span class="card-label">Rombel</span>
                </div>
            </div>
        </div>

        <!-- Students Section (hidden initially) -->
        <div id="studentsSection">
            <div class="card-modern">
                <div class="card-body">
                    <form action="{{ route('guru.penilaian.store') }}" method="POST" id="formPenilaian">
                        @csrf
                        <input type="hidden" name="id_rombel" id="inputIdRombel" value="">
                        <input type="hidden" name="mapel" id="inputMapel" value="">
                        <input type="hidden" name="nama_rombel" id="inputNamaRombel" value="">
                        <input type="hidden" name="jam_ke" value="">
                        <input type="hidden" name="from" value="input-penilaian">

                        <!-- Form Input Penilaian -->
                        <div class="form-section mb-4">
                            <div class="filter-group">
                                <label class="filter-label">Tanggal Penilaian</label>
                                <input type="date" name="tanggal_penilaian" class="form-control modern-input"
                                    value="{{ date('Y-m-d') }}"
                                    min="{{ $minDate }}" max="{{ $maxDate }}" required>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">Materi / Topik</label>
                                <textarea name="materi" class="modern-input" placeholder="Masukkan materi penilaian" rows="2"></textarea>
                            </div>
                        </div>

                        <!-- Section Header -->
                        <div class="section-header">
                            <h2><i class="fas fa-users"></i> Daftar Siswa</h2>
                            <span class="badge" id="siswaCount">0 Siswa</span>
                        </div>

                        <!-- Student Cards Container -->
                        <div class="students-cards-grid" id="studentsContainer">
                            <!-- Students will be loaded here via AJAX -->
                        </div>

                        <!-- Action Buttons -->
                        <div class="btn-action-group">
                            <button type="submit" class="btn-save" id="btnSimpanPenilaian" disabled>
                                <i class="fas fa-save"></i> Simpan Penilaian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Mapel -->
<div class="modal fade" id="mapelModal" tabindex="-1" aria-labelledby="mapelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapelModalLabel">
                    <i class="fas fa-book me-2"></i> Pilih Mata Pelajaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="mapelOptionsContainer">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Rombel -->
<div class="modal fade" id="rombelModal" tabindex="-1" aria-labelledby="rombelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rombelModalLabel">
                    <i class="fas fa-users me-2"></i> Pilih Rombel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="rombelOptionsContainer">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                    </div>
                </div>
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
    // Selected data
    let selectedMapel = null;
    let selectedRombel = null;
    let isMapelAgama = false;

    // Open Mapel Modal
    function openMapelModal() {
        const mapelModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('mapelModal'));
        mapelModal.show();
        loadMapelOptions();
    }

    // Open Rombel Modal
    function openRombelModal() {
        if (!selectedMapel) {
            alert('Silakan pilih mata pelajaran terlebih dahulu!');
            return;
        }
        const rombelModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('rombelModal'));
        rombelModal.show();
        loadRombelOptions();
    }

    // Load Mapel Options
    function loadMapelOptions() {
        const container = document.getElementById('mapelOptionsContainer');
        container.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';

        fetch('{{ route("guru.input-penilaian.mapel") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    let html = '<div class="modal-option-grid">';
                    data.data.forEach(mapel => {
                        html += `
                            <div class="option-card" onclick="selectMapel(${mapel.id}, '${escapeHtml(mapel.nama_mapel)}')">
                                <div class="option-icon mapel">
                                    <i class="fas fa-book"></i>
                                </div>
                                <p class="option-name">${escapeHtml(mapel.nama_mapel)}</p>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-book-open"></i>
                            <h4>Tidak Ada Mata Pelajaran</h4>
                            <p>Anda belum memiliki penugasan mata pelajaran di periode ini.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>Error</h4>
                        <p>Gagal memuat data mata pelajaran.</p>
                    </div>
                `;
            });
    }

    // Load Rombel Options
    function loadRombelOptions() {
        const container = document.getElementById('rombelOptionsContainer');
        container.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';

        fetch(`{{ route("guru.input-penilaian.rombel") }}?id_mapel=${selectedMapel.id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    let html = '<div class="modal-option-grid">';
                    data.data.forEach(rombel => {
                        html += `
                            <div class="option-card" onclick="selectRombel(${rombel.id}, '${escapeHtml(rombel.nama_rombel)}')">
                                <div class="option-icon rombel">
                                    <i class="fas fa-users"></i>
                                </div>
                                <p class="option-name">${escapeHtml(rombel.nama_rombel)}</p>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-users-slash"></i>
                            <h4>Tidak Ada Rombel</h4>
                            <p>Tidak ada rombel untuk mata pelajaran ini.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>Error</h4>
                        <p>Gagal memuat data rombel.</p>
                    </div>
                `;
            });
    }

    // Select Mapel
    function selectMapel(id, name) {
        selectedMapel = { id, name };
        selectedRombel = null;

        // Update UI
        document.getElementById('mapelCard').classList.add('selected');
        document.getElementById('mapelPlaceholder').style.display = 'none';
        document.getElementById('mapelValue').textContent = name;
        document.getElementById('mapelValue').style.display = 'block';
        document.getElementById('inputMapel').value = name;

        // Reset rombel
        document.getElementById('rombelCard').classList.remove('selected');
        document.getElementById('rombelPlaceholder').textContent = 'Klik untuk memilih...';
        document.getElementById('rombelPlaceholder').style.display = 'block';
        document.getElementById('rombelValue').style.display = 'none';

        // Hide students section
        document.getElementById('studentsSection').classList.remove('show');
        document.getElementById('btnSimpanPenilaian').disabled = true;

        // Close modal
        bootstrap.Modal.getOrCreateInstance(document.getElementById('mapelModal')).hide();
    }

    // Select Rombel
    function selectRombel(id, name) {
        selectedRombel = { id, name };

        // Update UI
        document.getElementById('rombelCard').classList.add('selected');
        document.getElementById('rombelPlaceholder').style.display = 'none';
        document.getElementById('rombelValue').textContent = name;
        document.getElementById('rombelValue').style.display = 'block';
        document.getElementById('inputIdRombel').value = id;
        document.getElementById('inputNamaRombel').value = name;

        // Close modal
        bootstrap.Modal.getOrCreateInstance(document.getElementById('rombelModal')).hide();

        // Load students
        loadStudents();
    }

    // Load Students
    function loadStudents() {
        const container = document.getElementById('studentsContainer');
        const section = document.getElementById('studentsSection');
        
        section.classList.add('show');
        container.innerHTML = '<div class="loading-spinner" style="grid-column: 1 / -1;"><div class="spinner"></div></div>';

        fetch(`{{ route("guru.input-penilaian.siswa") }}?id_rombel=${selectedRombel.id}&nama_mapel=${encodeURIComponent(selectedMapel.name)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    isMapelAgama = data.is_mapel_agama;
                    document.getElementById('siswaCount').textContent = data.data.length + ' Siswa';
                    
                    let html = '';
                    data.data.forEach(siswa => {
                        html += createStudentCard(siswa, isMapelAgama);
                    });
                    container.innerHTML = html;
                    document.getElementById('btnSimpanPenilaian').disabled = false;
                } else {
                    container.innerHTML = `
                        <div class="empty-state" style="grid-column: 1 / -1;">
                            <i class="fas fa-users-slash"></i>
                            <h4>Tidak Ada Data Siswa</h4>
                            <p>${data.is_mapel_agama ? 'Tidak ada siswa dengan agama ' + data.agama_mapel + ' ditemukan.' : 'Tidak ada siswa ditemukan dalam rombel ini.'}</p>
                        </div>
                    `;
                    document.getElementById('siswaCount').textContent = '0 Siswa';
                    document.getElementById('btnSimpanPenilaian').disabled = true;
                }
            })
            .catch(error => {
                container.innerHTML = `
                    <div class="empty-state" style="grid-column: 1 / -1;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>Error</h4>
                        <p>Gagal memuat data siswa.</p>
                    </div>
                `;
                document.getElementById('btnSimpanPenilaian').disabled = true;
            });
    }

    // Create Student Card HTML
    function createStudentCard(siswa, isAgama) {
        const avatarContent = siswa.foto_exists
            ? `<img src="${siswa.foto_path}" alt="Foto ${escapeHtml(siswa.nama)}" class="student-avatar-img"
                  onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                  data-foto-path="${siswa.foto_path}">
               <div class="student-avatar-initial" style="display: none;">${siswa.initials}</div>`
            : `<div class="student-avatar-initial">${siswa.initials}</div>`;

        const agamaHtml = isAgama ? `<p class="student-agama">Agama: ${escapeHtml(siswa.agama)}</p>` : '';

        return `
            <div class="student-card" data-siswa-id="${siswa.id}">
                <div class="student-card-header">
                    <div class="student-avatar" onclick="showFullPhoto(this, ${siswa.id}, '${escapeHtml(siswa.nama)}', '${siswa.nisn}')">
                        ${avatarContent}
                    </div>
                    <div class="student-info">
                        <h4 class="student-name">${escapeHtml(siswa.nama)}</h4>
                        <p class="student-nisn">NISN: ${siswa.nisn}</p>
                        ${agamaHtml}
                    </div>
                </div>

                <div class="student-card-body">
                    <div class="input-row">
                        <div class="input-group">
                            <label class="input-label">
                                <i class="fas fa-star"></i> Nilai
                            </label>
                            <input type="number" name="nilai[${siswa.id}]" class="nilai-input" 
                                min="0" max="100" step="0.01" placeholder="0-100">
                        </div>
                    </div>
                    <div class="input-row">
                        <div class="input-group full-width">
                            <label class="input-label">
                                <i class="fas fa-comment"></i> Keterangan
                            </label>
                            <textarea name="keterangan[${siswa.id}]" class="input-keterangan" rows="2"
                                placeholder="Berikan catatan penilaian..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Hidden Inputs -->
                <input type="hidden" name="nis[${siswa.id}]" value="${siswa.nis}">
                <input type="hidden" name="nisn[${siswa.id}]" value="${siswa.nisn}">
                <input type="hidden" name="nama_siswa[${siswa.id}]" value="${escapeHtml(siswa.nama)}">
            </div>
        `;
    }

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

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
        
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('fotoModal'));
        modal.show();
    }
</script>
@endpush
