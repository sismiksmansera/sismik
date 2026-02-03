@extends('layouts.app-guru-bk')

@section('content')
<div class="main-content catatan-bimbingan-page">
    {{-- HEADER SECTION - Dashboard Style --}}
    <div class="bk-page-header">
        <div class="header-content-wrapper">
            {{-- STUDENT PHOTO --}}
            <div class="header-icon-box foto-clickable" onclick="openFullPhotoModal()">
                @php
                    $foto_exists = $siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto);
                @endphp

                @if($foto_exists)
                    <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="Foto {{ $siswa->nama }}" class="header-foto">
                @else
                    <i class="fas fa-user-graduate"></i>
                @endif
            </div>
            <div class="header-info">
                <div class="header-greeting">
                    <span class="greeting-text">Catatan Bimbingan</span>
                    <h1>{{ $siswa->nama }}</h1>
                </div>
                <div class="header-details">
                    <span class="detail-badge"><i class="fas fa-id-card"></i> {{ $siswa->nisn }}</span>
                    @if(!empty($tahun_filter))
                    <span class="detail-badge"><i class="fas fa-calendar-alt"></i> {{ $tahun_filter }}</span>
                    @endif
                    @if(!empty($semester_filter))
                    <span class="detail-badge"><i class="fas fa-clock"></i> {{ $semester_filter }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="header-actions-box">
            <a href="{{ route('guru_bk.catatan-bimbingan.create', ['nisn' => $nisn, 'tahun' => $tahun_filter, 'semester' => $semester_filter]) }}" class="btn-action-header btn-primary-header">
                <i class="fas fa-plus"></i> <span class="btn-text">Catatan Baru</span>
            </a>
            <button type="button" onclick="history.back()" class="btn-action-header btn-secondary-header">
                <i class="fas fa-arrow-left"></i> <span class="btn-text">Kembali</span>
            </button>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- STATISTICS SECTION - Dashboard Style --}}
    <div class="chart-card">
        <div class="chart-header">
            <i class="fas fa-chart-pie"></i>
            <h3>Statistik Catatan</h3>
        </div>
        <div class="quick-stats-grid">
            <div class="stat-card-mini primary clickable-stat active" data-status="all" onclick="filterByStatus('all')">
                <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                <div>
                    <h3>{{ $total_catatan }}</h3>
                    <p>Total</p>
                </div>
            </div>
            <div class="stat-card-mini danger clickable-stat" data-status="Belum Ditangani" onclick="filterByStatus('Belum Ditangani')">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div>
                    <h3>{{ $status_stats['Belum Ditangani'] ?? 0 }}</h3>
                    <p>Belum</p>
                </div>
            </div>
            <div class="stat-card-mini warning clickable-stat" data-status="Dalam Proses" onclick="filterByStatus('Dalam Proses')">
                <div class="stat-icon"><i class="fas fa-spinner"></i></div>
                <div>
                    <h3>{{ $status_stats['Dalam Proses'] ?? 0 }}</h3>
                    <p>Proses</p>
                </div>
            </div>
            <div class="stat-card-mini success clickable-stat" data-status="Selesai" onclick="filterByStatus('Selesai')">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <h3>{{ $status_stats['Selesai'] ?? 0 }}</h3>
                    <p>Selesai</p>
                </div>
            </div>
        </div>
    </div>

    {{-- MOBILE ACTION BUTTONS (only visible on mobile) --}}
    <div class="mobile-actions-wrapper">
        <a href="{{ route('guru_bk.catatan-bimbingan.create', ['nisn' => $nisn, 'tahun' => $tahun_filter, 'semester' => $semester_filter]) }}" class="btn-mobile-action btn-mobile-primary">
            <i class="fas fa-plus"></i> Catatan Baru
        </a>
        <button type="button" onclick="history.back()" class="btn-mobile-action btn-mobile-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </button>
    </div>

    {{-- FILTER BAR --}}
    <div class="filter-bar">
        <form method="GET" class="filter-form-horizontal">
            <div class="filter-row">
                <div class="filter-item">
                    <label class="filter-label">Tahun Pelajaran</label>
                    <select name="tahun" class="form-select modern-select" onchange="this.form.submit()">
                        <option value="">Semua Tahun</option>
                        @foreach($tahun_list as $tahun)
                        <option value="{{ $tahun }}" {{ $tahun_filter == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label class="filter-label">Semester</label>
                    <select name="semester" class="form-select modern-select" onchange="this.form.submit()">
                        <option value="">Semua Semester</option>
                        <option value="Ganjil" {{ $semester_filter == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ $semester_filter == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>

                <div class="filter-actions-horizontal">
                    <a href="{{ route('guru_bk.catatan-bimbingan', ['nisn' => $nisn]) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-refresh"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>



    @if($total_catatan > 0)
    {{-- CATATAN LIST --}}
    <div class="catatan-container" id="catatanContainer">
        @foreach($catatan_list as $catatan)
        @php
            $catatan_id = 'catatan_' . $catatan->id;
            
            // Normalize status
            $status_raw = $catatan->status ?? '';
            $status_lower = strtolower(trim($status_raw));
            
            if (empty($status_raw) || $status_lower === 'belum' || $status_lower === 'belum ditangani') {
                $status = 'Belum Ditangani';
            } elseif ($status_lower === 'proses' || $status_lower === 'dalam proses') {
                $status = 'Dalam Proses';
            } elseif ($status_lower === 'selesai') {
                $status = 'Selesai';
            } else {
                $status = $status_raw;
            }
            
            // Status styling
            $status_config = [
                'Belum Ditangani' => [
                    'class' => 'danger',
                    'icon' => 'fa-clock',
                    'gradient' => 'linear-gradient(135deg, #f87171 0%, #ef4444 100%)',
                    'bg' => 'linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%)',
                    'border' => '#fecaca'
                ],
                'Dalam Proses' => [
                    'class' => 'warning',
                    'icon' => 'fa-spinner',
                    'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                    'bg' => 'linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%)',
                    'border' => '#fde68a'
                ],
                'Selesai' => [
                    'class' => 'success',
                    'icon' => 'fa-check-circle',
                    'gradient' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                    'bg' => 'linear-gradient(135deg, #f0fdf4 0%, #d1fae5 100%)',
                    'border' => '#a7f3d0'
                ]
            ];
            
            $config = $status_config[$status] ?? [
                'class' => 'secondary',
                'icon' => 'fa-question-circle',
                'gradient' => 'linear-gradient(135deg, #6b7280 0%, #4b5563 100%)',
                'bg' => 'linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%)',
                'border' => '#e5e7eb'
            ];
            
            // Jenis icon
            $jenis_icons = [
                'pribadi' => 'fa-user',
                'sosial' => 'fa-users',
                'belajar' => 'fa-book',
                'karir' => 'fa-briefcase'
            ];
            $jenis_lower = strtolower($catatan->jenis_bimbingan ?? '');
            $jenis_icon = $jenis_icons[$jenis_lower] ?? 'fa-clipboard';
            
            // Guru BK name
            $nama_guru_bk_catatan = \App\Http\Controllers\GuruBK\CatatanBimbinganController::getGuruBKForCatatan($catatan, $siswa);
        @endphp

        {{-- CATATAN CARD --}}
        <div class="catatan-card" data-catatan="{{ $catatan_id }}" data-status="{{ $status }}">
            {{-- TOP BAR --}}
            <div class="catatan-top-bar" style="background: {{ $config['gradient'] }};"></div>

            {{-- CARD HEADER --}}
            <div class="catatan-card-header" onclick="toggleCatatanCard('{{ $catatan_id }}')" style="background: {{ $config['bg'] }}; border-bottom: 1px solid {{ $config['border'] }};">
                <div class="catatan-info">
                    <div class="catatan-icon" style="background: {{ $config['gradient'] }};">
                        <i class="fas {{ $jenis_icon }}"></i>
                    </div>
                    <div class="catatan-details">
                        <div class="catatan-title-row">
                            <h3 class="catatan-jenis">{{ $catatan->jenis_bimbingan }}</h3>
                            <span class="catatan-date-mobile">
                                <i class="fas fa-calendar"></i>
                                {{ \Carbon\Carbon::parse($catatan->tanggal)->format('d M Y') }}
                            </span>
                        </div>
                        <div class="catatan-meta">
                            <span class="catatan-period">
                                {{ $catatan->tahun_pelajaran }} - {{ $catatan->semester }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="catatan-header-right">
                    <span class="status-badge {{ $config['class'] }}">
                        <i class="fas {{ $config['icon'] }}"></i>
                        {{ $status }}
                    </span>
                    <span class="catatan-date-desktop">
                        <i class="fas fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::parse($catatan->tanggal)->format('d M Y') }}
                    </span>
                    <div class="toggle-indicator">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            {{-- CARD CONTENT --}}
            <div class="catatan-card-content" id="{{ $catatan_id }}">
                <div class="catatan-body">
                    <div class="catatan-section">
                        <h4><i class="fas fa-exclamation-triangle"></i> Masalah</h4>
                        <p>{{ $catatan->masalah ?? '-' }}</p>
                    </div>

                    @if(!empty($catatan->penyelesaian))
                    <div class="catatan-section">
                        <h4><i class="fas fa-lightbulb"></i> Penyelesaian</h4>
                        <p>{{ $catatan->penyelesaian }}</p>
                    </div>
                    @endif

                    @if(!empty($catatan->tindak_lanjut))
                    <div class="catatan-section">
                        <h4><i class="fas fa-tasks"></i> Tindak Lanjut</h4>
                        <p>{{ $catatan->tindak_lanjut }}</p>
                    </div>
                    @endif

                    @if(!empty($catatan->keterangan))
                    <div class="catatan-section">
                        <h4><i class="fas fa-info-circle"></i> Keterangan</h4>
                        <p>{{ $catatan->keterangan }}</p>
                    </div>
                    @endif

                    <div class="catatan-meta-info">
                        <div class="meta-item">
                            <i class="fas fa-user-tie"></i>
                            <span>Guru BK: {{ $nama_guru_bk_catatan }}</span>
                        </div>
                        @if(!empty($catatan->pencatat_nama))
                        <div class="meta-item">
                            <i class="fas fa-pen"></i>
                            <span>Dicatat oleh: {{ $catatan->pencatat_nama }}</span>
                        </div>
                        @endif
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>Dibuat: {{ \Carbon\Carbon::parse($catatan->created_at)->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($catatan->updated_at && $catatan->updated_at != $catatan->created_at)
                        <div class="meta-item">
                            <i class="fas fa-edit"></i>
                            <span>Diupdate: {{ \Carbon\Carbon::parse($catatan->updated_at)->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="catatan-actions">
                    <a href="{{ route('guru_bk.catatan-bimbingan.edit', $catatan->id) }}" class="btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('guru_bk.catatan-bimbingan.print', $catatan->id) }}" class="btn-print" target="_blank">
                        <i class="fas fa-print"></i> Cetak
                    </a>
                    <button type="button" class="btn-delete" onclick="confirmDelete({{ $catatan->id }})">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    {{-- EMPTY STATE --}}
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <h3>Belum Ada Catatan</h3>
        <p>Belum ada catatan bimbingan untuk siswa ini.</p>
        <a href="{{ route('guru_bk.catatan-bimbingan.create', ['nisn' => $nisn]) }}" class="btn-add-new">
            <i class="fas fa-plus"></i> Buat Catatan Baru
        </a>
    </div>
    @endif
</div>

{{-- FULL PHOTO MODAL --}}
<div id="fullPhotoModal" class="full-photo-modal" style="display: none;">
    <div class="modal-content-photo">
        <button onclick="closeFullPhoto()" class="modal-close-photo">
            <i class="fas fa-times"></i>
        </button>
        @if($foto_exists ?? false)
            <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="Foto {{ $siswa->nama }}" class="full-photo-img">
            <div class="photo-info">
                <h4>{{ $siswa->nama }}</h4>
                <p>NISN: {{ $siswa->nisn }}</p>
            </div>
        @else
            <div class="no-photo-full">
                <div class="no-photo-icon-full">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h4>{{ $siswa->nama }}</h4>
                <p>Belum ada foto profil</p>
            </div>
        @endif
    </div>
</div>

{{-- DELETE CONFIRMATION MODAL --}}
<div id="deleteModal" class="delete-modal" style="display: none;">
    <div class="delete-modal-content">
        <div class="delete-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3>Konfirmasi Hapus</h3>
        <p>Apakah Anda yakin ingin menghapus catatan ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="delete-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="button" class="btn-confirm-delete" id="confirmDeleteBtn">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    </div>
</div>

<style>
/* Main Content */
.main-content.catatan-bimbingan-page {
    padding: 25px;
    background-color: #f8f9fa;
    min-height: calc(100vh - 60px);
}

/* ============== HEADER STYLES - Dashboard Style ============== */
.catatan-bimbingan-page .bk-page-header {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    border-radius: 16px;
    padding: 25px 30px;
    margin-bottom: 20px;
    box-shadow: 0 10px 30px rgba(124, 58, 237, 0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.catatan-bimbingan-page .header-content-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.catatan-bimbingan-page .header-icon-box {
    width: 70px;
    height: 70px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
    flex-shrink: 0;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.catatan-bimbingan-page .header-icon-box:hover {
    transform: scale(1.05);
}

.catatan-bimbingan-page .header-icon-box .header-foto {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.catatan-bimbingan-page .header-info {
    flex: 1;
}

.catatan-bimbingan-page .header-greeting .greeting-text {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
    display: block;
    margin-bottom: 4px;
}

.catatan-bimbingan-page .header-greeting h1 {
    font-size: 20px;
    font-weight: 700;
    color: white;
    margin: 0;
}

.catatan-bimbingan-page .header-details {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.catatan-bimbingan-page .detail-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 500;
}

.catatan-bimbingan-page .header-actions-box {
    display: flex;
    gap: 10px;
    flex-shrink: 0;
}

.catatan-bimbingan-page .btn-action-header {
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.catatan-bimbingan-page .btn-primary-header {
    background: rgba(255, 255, 255, 0.95);
    color: #7c3aed;
}

.catatan-bimbingan-page .btn-primary-header:hover {
    background: white;
    transform: translateY(-2px);
}

.catatan-bimbingan-page .btn-secondary-header {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.catatan-bimbingan-page .btn-secondary-header:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* ============== STATS CHART CARD ============== */
.catatan-bimbingan-page .chart-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.catatan-bimbingan-page .chart-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f3f4f6;
    margin-bottom: 15px;
}

.catatan-bimbingan-page .chart-header i {
    color: #7c3aed;
    font-size: 18px;
}

.catatan-bimbingan-page .chart-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.catatan-bimbingan-page .quick-stats-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.catatan-bimbingan-page .stat-card-mini {
    flex: 1;
    min-width: 100px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    border-radius: 12px;
    text-decoration: none;
}

.catatan-bimbingan-page .stat-card-mini.primary {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
}

.catatan-bimbingan-page .stat-card-mini.danger {
    background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
}

.catatan-bimbingan-page .stat-card-mini.warning {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.catatan-bimbingan-page .stat-card-mini.success {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
}

.catatan-bimbingan-page .stat-card-mini .stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}

.catatan-bimbingan-page .stat-card-mini.primary .stat-icon {
    background: #0ea5e9;
    color: white;
}

.catatan-bimbingan-page .stat-card-mini.danger .stat-icon {
    background: #ef4444;
    color: white;
}

.catatan-bimbingan-page .stat-card-mini.warning .stat-icon {
    background: #f59e0b;
    color: white;
}

.catatan-bimbingan-page .stat-card-mini.success .stat-icon {
    background: #10b981;
    color: white;
}

.catatan-bimbingan-page .stat-card-mini h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.catatan-bimbingan-page .stat-card-mini p {
    font-size: 11px;
    color: #6b7280;
    margin: 0;
}

/* Clickable Stat Cards */
.catatan-bimbingan-page .stat-card-mini.clickable-stat {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.catatan-bimbingan-page .stat-card-mini.clickable-stat:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
}

.catatan-bimbingan-page .stat-card-mini.clickable-stat.active {
    border-color: #7c3aed;
    box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.15);
}

/* ============== MOBILE ACTION BUTTONS ============== */
.catatan-bimbingan-page .mobile-actions-wrapper {
    display: none;
}

.catatan-bimbingan-page .btn-mobile-action {
    flex: 1;
    padding: 10px 15px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.catatan-bimbingan-page .btn-mobile-primary {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    color: white;
}

.catatan-bimbingan-page .btn-mobile-secondary {
    background: #f3f4f6;
    color: #374151;
}

/* Header */
.page-header-rekap {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 25px;
    color: white;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
}

.header-title-section {
    display: flex;
    align-items: center;
    gap: 20px;
    flex: 1;
}

.header-icon {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.header-icon:hover {
    transform: scale(1.05);
}

.foto-wrapper {
    width: 100%;
    height: 100%;
    position: relative;
}

.foto-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 20px;
}

.foto-overlay-full {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 20px;
}

.header-icon:hover .foto-overlay-full {
    opacity: 1;
}

.no-foto-wrapper {
    text-align: center;
    color: rgba(255,255,255,0.8);
}

.no-foto-wrapper i {
    font-size: 28px;
    margin-bottom: 5px;
}

.no-foto-text {
    font-size: 10px;
    opacity: 0.8;
}

.header-text {
    flex: 1;
}

.page-title-rekap {
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 10px;
}

.subtitle-item {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    font-size: 14px;
    opacity: 0.95;
}

.header-actions {
    display: flex;
    align-items: center;
}

.action-buttons-header {
    display: flex;
    gap: 10px;
}

.btn-back, .btn-action-primary {
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-back {
    background: rgba(255,255,255,0.2);
    color: white;
    border: 1px solid rgba(255,255,255,0.3);
}

.btn-back:hover {
    background: rgba(255,255,255,0.3);
}

.btn-action-primary {
    background: white;
    color: #7c3aed;
}

.btn-action-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.stat-icon {
    width: 55px;
    height: 55px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-icon.primary { background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%); }
.stat-icon.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.stat-icon.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.stat-icon.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

.stat-info h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 700;
    color: #1f2937;
}

.stat-info p {
    margin: 4px 0 0;
    color: #6b7280;
    font-size: 0.85rem;
}

/* Filter Bar */
.filter-bar {
    background: white;
    border-radius: 16px;
    padding: 20px 25px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: flex-end;
}

.filter-item {
    flex: 1;
    min-width: 150px;
}

.filter-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

.form-select {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.form-select:focus {
    outline: none;
    border-color: #7c3aed;
    box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
}

.filter-actions-horizontal {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    border: none;
}

.btn-secondary {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
}

/* Status Filter Chips */
.status-filter-chips {
    margin-bottom: 20px;
}

.chips-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.filter-chip {
    padding: 10px 18px;
    border-radius: 25px;
    border: 2px solid #e5e7eb;
    background: white;
    color: #6b7280;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.filter-chip:hover {
    border-color: #7c3aed;
    color: #7c3aed;
}

.filter-chip.active {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    color: white;
    border-color: transparent;
}

.chip-badge {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.chip-badge.danger { background: #ef4444; }
.chip-badge.warning { background: #f59e0b; }
.chip-badge.success { background: #10b981; }

/* Catatan Card */
.catatan-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.catatan-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.catatan-top-bar {
    height: 4px;
}

.catatan-card-header {
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.catatan-card-header:hover {
    opacity: 0.95;
}

.catatan-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.catatan-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.catatan-jenis {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

.catatan-meta {
    font-size: 0.8rem;
    color: #6b7280;
    margin-top: 4px;
}

.catatan-header-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.status-badge.danger { background: #fef2f2; color: #ef4444; }
.status-badge.warning { background: #fffbeb; color: #d97706; }
.status-badge.success { background: #f0fdf4; color: #059669; }

.catatan-date-desktop {
    font-size: 0.85rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 6px;
}

.catatan-date-mobile {
    display: none;
}

.toggle-indicator {
    transition: transform 0.3s ease;
}

.catatan-card.expanded .toggle-indicator {
    transform: rotate(180deg);
}

/* Card Content */
.catatan-card-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease;
}

.catatan-card.expanded .catatan-card-content {
    max-height: 2000px;
}

.catatan-body {
    padding: 20px;
    border-top: 1px solid #e5e7eb;
}

.catatan-section {
    margin-bottom: 20px;
}

.catatan-section h4 {
    margin: 0 0 10px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 8px;
}

.catatan-section h4 i {
    color: #7c3aed;
}

.catatan-section p {
    margin: 0;
    color: #4b5563;
    line-height: 1.6;
}

.catatan-meta-info {
    background: #f9fafb;
    padding: 15px;
    border-radius: 10px;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.catatan-meta-info .meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    color: #6b7280;
}

.catatan-meta-info .meta-item i {
    color: #9ca3af;
}

.catatan-actions {
    padding: 15px 20px;
    background: #f9fafb;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn-edit, .btn-delete, .btn-print {
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    text-decoration: none;
}

.btn-edit {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.btn-delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.btn-print {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-edit:hover, .btn-delete:hover, .btn-print:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}


/* Empty State */
.empty-state {
    background: white;
    border-radius: 16px;
    padding: 60px 30px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.empty-state .empty-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-state .empty-icon i {
    font-size: 32px;
    color: white;
}

.empty-state h3 {
    margin: 0 0 10px;
    color: #1f2937;
    font-weight: 600;
}

.empty-state p {
    margin: 0 0 20px;
    color: #6b7280;
}

.btn-add-new {
    padding: 12px 24px;
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    color: white;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

/* Modals */
.full-photo-modal, .delete-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content-photo {
    background: white;
    border-radius: 20px;
    padding: 30px;
    max-width: 500px;
    text-align: center;
    position: relative;
}

.modal-close-photo {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #ef4444;
    color: white;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
}

.full-photo-img {
    max-width: 100%;
    max-height: 400px;
    border-radius: 15px;
}

.delete-modal-content {
    background: white;
    border-radius: 20px;
    padding: 40px;
    max-width: 400px;
    text-align: center;
}

.delete-icon {
    width: 70px;
    height: 70px;
    background: #fef2f2;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.delete-icon i {
    font-size: 30px;
    color: #ef4444;
}

.delete-modal-content h3 {
    margin: 0 0 15px;
    color: #1f2937;
}

.delete-modal-content p {
    margin: 0 0 25px;
    color: #6b7280;
}

.delete-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.btn-cancel, .btn-confirm-delete {
    padding: 12px 25px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-cancel {
    background: #f3f4f6;
    color: #374151;
}

.btn-confirm-delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

/* Alert */
.alert {
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert-success {
    background: #f0fdf4;
    color: #059669;
    border: 1px solid #a7f3d0;
}

.alert-danger {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .main-content.catatan-bimbingan-page {
        padding: 15px;
    }

    .page-header-rekap {
        padding: 20px;
    }

    .header-content {
        flex-direction: column;
    }

    .header-title-section {
        flex-direction: column;
        text-align: center;
    }

    .header-icon {
        width: 70px;
        height: 70px;
    }

    .subtitle-item {
        justify-content: center;
    }

    .action-buttons-header {
        width: 100%;
        flex-direction: column;
    }

    .btn-back, .btn-action-primary {
        width: 100%;
        justify-content: center;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .filter-row {
        flex-direction: column;
    }

    .filter-item {
        width: 100%;
    }

    .catatan-header-right {
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }

    .catatan-date-desktop {
        display: none;
    }

    .catatan-date-mobile {
        display: inline-flex;
        font-size: 0.75rem;
        color: #9ca3af;
    }

    .catatan-actions {
        flex-direction: column;
    }

    .btn-edit, .btn-delete {
        width: 100%;
        justify-content: center;
    }
    
    /* Header Mobile - Centered */
    .catatan-bimbingan-page .bk-page-header {
        padding: 15px;
        border-radius: 12px;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .catatan-bimbingan-page .header-content-wrapper {
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    
    .catatan-bimbingan-page .header-icon-box {
        width: 60px;
        height: 60px;
        font-size: 24px;
        border-radius: 50%;
    }
    
    .catatan-bimbingan-page .header-info {
        text-align: center;
    }
    
    .catatan-bimbingan-page .header-greeting .greeting-text {
        font-size: 11px;
    }
    
    .catatan-bimbingan-page .header-greeting h1 {
        font-size: 16px;
    }
    
    .catatan-bimbingan-page .header-details {
        justify-content: center;
        gap: 6px;
        margin-top: 8px;
    }
    
    .catatan-bimbingan-page .detail-badge {
        font-size: 9px;
        padding: 4px 8px;
    }
    
    .catatan-bimbingan-page .header-actions-box {
        display: none;
    }
    
    /* Stats Card Mobile */
    .catatan-bimbingan-page .chart-card {
        padding: 12px;
        border-radius: 12px;
        margin-bottom: 12px;
    }
    
    .catatan-bimbingan-page .chart-header {
        padding-bottom: 10px;
        margin-bottom: 10px;
    }
    
    .catatan-bimbingan-page .chart-header i {
        font-size: 14px;
    }
    
    .catatan-bimbingan-page .chart-header h3 {
        font-size: 12px;
    }
    
    .catatan-bimbingan-page .quick-stats-grid {
        display: flex;
        flex-wrap: nowrap;
        gap: 6px;
    }
    
    .catatan-bimbingan-page .stat-card-mini {
        flex: 1;
        min-width: 60px;
        padding: 8px;
        border-radius: 8px;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 4px;
        cursor: pointer;
    }
    
    .catatan-bimbingan-page .stat-card-mini.clickable-stat {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .catatan-bimbingan-page .stat-card-mini.clickable-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .catatan-bimbingan-page .stat-card-mini.clickable-stat.active {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
    }
    
    .catatan-bimbingan-page .stat-card-mini .stat-icon {
        width: 26px;
        height: 26px;
        font-size: 10px;
        border-radius: 6px;
    }
    
    .catatan-bimbingan-page .stat-card-mini h3 {
        font-size: 14px;
    }
    
    .catatan-bimbingan-page .stat-card-mini p {
        font-size: 8px;
    }
    
    /* Mobile Action Buttons */
    .catatan-bimbingan-page .mobile-actions-wrapper {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
    }
    
    /* Filter Bar Mobile - Inline Labels */
    .catatan-bimbingan-page .filter-bar {
        padding: 12px;
        border-radius: 10px;
    }
    
    .catatan-bimbingan-page .filter-row {
        flex-direction: column;
        gap: 10px;
    }
    
    .catatan-bimbingan-page .filter-item {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 10px;
    }
    
    .catatan-bimbingan-page .filter-label {
        white-space: nowrap;
        min-width: 60px;
        font-size: 11px;
    }
    
    .catatan-bimbingan-page .form-select {
        flex: 1;
        font-size: 12px;
        padding: 8px 10px;
    }
    
    .catatan-bimbingan-page .filter-actions-horizontal {
        justify-content: flex-end;
    }
}
</style>

<script>
let deleteId = null;

function toggleCatatanCard(catatanId) {
    const card = document.querySelector(`[data-catatan="${catatanId}"]`);
    if (card) {
        card.classList.toggle('expanded');
    }
}

function filterByStatus(status) {
    const statCards = document.querySelectorAll('.stat-card-mini.clickable-stat');
    const cards = document.querySelectorAll('.catatan-card');

    // Update active state on stat cards
    statCards.forEach(card => {
        card.classList.remove('active');
        if (card.dataset.status === status) {
            card.classList.add('active');
        }
    });

    // Filter catatan cards
    cards.forEach(card => {
        if (status === 'all' || card.dataset.status === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function openFullPhotoModal() {
    document.getElementById('fullPhotoModal').style.display = 'flex';
}

function closeFullPhoto() {
    document.getElementById('fullPhotoModal').style.display = 'none';
}

function confirmDelete(id) {
    deleteId = id;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    deleteId = null;
    document.getElementById('deleteModal').style.display = 'none';
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!deleteId) return;

    fetch('{{ route("guru_bk.catatan-bimbingan.delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id: deleteId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const card = document.querySelector(`[data-catatan="catatan_${deleteId}"]`);
            if (card) {
                card.remove();
            }
            closeDeleteModal();
            alert('Catatan berhasil dihapus!');
            location.reload();
        } else {
            alert(data.message || 'Gagal menghapus catatan.');
            closeDeleteModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan sistem.');
        closeDeleteModal();
    });
});

// Close modals when clicking outside
document.getElementById('fullPhotoModal').addEventListener('click', function(e) {
    if (e.target === this) closeFullPhoto();
});

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endsection
