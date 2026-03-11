@extends('layouts.app')

@section('title', 'Izin Guru | SISMIK')

@push('styles')
<style>
    /* ============================
       INPUT IZIN GURU STYLES
    ============================ */
    
    /* HEADER SECTION - Orange gradient for izin theme */
    .izin-header-section {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 20px;
        text-align: center;
        color: white;
        box-shadow: 0 10px 40px rgba(245, 158, 11, 0.3);
    }
    
    .izin-header-section .header-icon-large {
        font-size: 50px;
        margin-bottom: 10px;
        opacity: 0.9;
    }
    
    .izin-header-section .page-title-rekap {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* STATS GRID */
    .input-izin-guru .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .input-izin-guru .stat-card {
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

    .input-izin-guru .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border-color: #f59e0b;
    }

    .input-izin-guru .stat-icon {
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

    .input-izin-guru .stat-icon.primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .input-izin-guru .stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
    .input-izin-guru .stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }

    .input-izin-guru .stat-info { flex: 1; min-width: 0; }
    .input-izin-guru .stat-info h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #333333;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .input-izin-guru .stat-info p {
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
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        background: linear-gradient(135deg, #d97706, #b45309);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    /* FORM SECTION */
    .form-section {
        background: #f8fafc;
        padding: 25px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-label i { color: #f59e0b; }
    .form-label i.text-danger { color: #ef4444; }

    .modern-input {
        padding: 12px 15px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .modern-input:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }

    textarea.modern-input {
        resize: vertical;
        min-height: 80px;
    }

    .input-readonly {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }

    .text-danger { color: #ef4444; }
    .text-warning { color: #f59e0b; }

    /* RESPONSIVE - MOBILE */
    @media (max-width: 768px) {
        .izin-header-section {
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .izin-header-section .header-icon-large {
            font-size: 36px;
            margin-bottom: 8px;
        }
        
        .izin-header-section .page-title-rekap {
            font-size: 16px;
        }
        
        .input-izin-guru .stats-grid { 
            grid-template-columns: repeat(3, 1fr); 
            gap: 8px; 
        }
        .input-izin-guru .stat-card { 
            flex-direction: column; 
            text-align: center; 
            padding: 10px; 
            gap: 6px; 
        }
        .input-izin-guru .stat-icon { width: 32px; height: 32px; font-size: 12px; }
        .input-izin-guru .stat-info h3 { font-size: 10px; white-space: normal; line-height: 1.2; }
        .input-izin-guru .stat-info p { font-size: 8px; }
        
        .card-modern .card-body { padding: 15px; }
        
        .btn-action-group { flex-direction: column; gap: 10px; }
        .btn-back, .btn-save { width: 100%; justify-content: center; font-size: 12px; padding: 10px 16px; }
        
        .form-section { padding: 15px; }
        .form-grid { grid-template-columns: 1fr; gap: 12px; }
        .form-label { font-size: 11px; margin-bottom: 6px; }
        .modern-input { font-size: 12px; padding: 10px 12px; }
        textarea.modern-input { min-height: 60px; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')

    <div class="main-content input-izin-guru">
        <!-- Header Section - Orange Gradient -->
        <div class="izin-header-section">
            <div class="header-icon-large">
                <i class="fas fa-user-clock"></i>
            </div>
            <h1 class="page-title-rekap">
                {{ $isViewMode ? 'LIHAT / EDIT IZIN' : 'INPUT IZIN GURU' }}
            </h1>
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
                <form action="{{ route('guru.izin-guru.store') }}" method="POST" id="formIzinGuru">
                    @csrf
                    <input type="hidden" name="id_rombel" value="{{ $idRombel }}">
                    <input type="hidden" name="nama_rombel" value="{{ $namaRombel }}">
                    <input type="hidden" name="mapel" value="{{ $mapel }}">
                    <input type="hidden" name="tanggal_izin" value="{{ $lockedTanggal }}">
                    <input type="hidden" name="jam_ke" value="{{ $lockedJamKe }}">
                    <input type="hidden" name="from" value="{{ $fromPage }}">
                    @if($isViewMode && $existingIzin)
                        <input type="hidden" name="izin_id" value="{{ $existingIzin->id }}">
                    @endif

                    <!-- Action Buttons -->
                    <div class="btn-action-group">
                        <a href="{{ route('guru.dashboard') }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                        </a>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> {{ $isViewMode ? 'Update Izin' : 'Simpan Izin' }}
                        </button>
                    </div>

                    <!-- Form Input -->
                    <div class="form-section">
                        <div class="form-grid">
                            <!-- Tanggal (Terkunci) -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Tanggal Izin
                                    <span class="text-warning" style="font-size: 11px;"><i class="fas fa-lock"></i> Terkunci</span>
                                </label>
                                <input type="date" class="modern-input input-readonly" value="{{ $lockedTanggal }}" readonly>
                            </div>

                            <!-- Jam Pelajaran (Terkunci) -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i> Jam Pelajaran
                                    <span class="text-warning" style="font-size: 11px;"><i class="fas fa-lock"></i> Terkunci</span>
                                </label>
                                <input type="text" class="modern-input input-readonly" value="{{ $jamDisplay }}" readonly>
                            </div>

                            <!-- Alasan Izin -->
                            <div class="form-group full-width">
                                <label class="form-label">
                                    <i class="fas fa-exclamation-circle text-danger"></i> Alasan Izin <span class="text-danger">*</span>
                                </label>
                                <textarea name="alasan_izin" class="modern-input" rows="3" required
                                    placeholder="Contoh: Sakit, Keperluan keluarga, Dinas luar, dll.">{{ $isViewMode && $existingIzin ? $existingIzin->alasan_izin : '' }}</textarea>
                            </div>

                            <!-- Materi / Pokok Bahasan -->
                            <div class="form-group full-width">
                                <label class="form-label">
                                    <i class="fas fa-book-open"></i> Materi / Pokok Bahasan
                                </label>
                                <textarea name="materi" class="modern-input" rows="2"
                                    placeholder="Materi yang seharusnya diajarkan hari ini...">{{ $isViewMode && $existingIzin ? $existingIzin->materi : '' }}</textarea>
                            </div>

                            <!-- Uraian Tugas ke Siswa -->
                            <div class="form-group full-width">
                                <label class="form-label">
                                    <i class="fas fa-tasks"></i> Uraian Tugas ke Siswa
                                </label>
                                <textarea name="uraian_tugas" class="modern-input" rows="3"
                                    placeholder="Tugas yang diberikan kepada siswa selama guru izin...">{{ $isViewMode && $existingIzin ? $existingIzin->uraian_tugas : '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
