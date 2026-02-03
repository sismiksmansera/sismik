@extends('layouts.app-guru-bk')

@section('content')
<div class="main-content">
    <!-- HEADER SECTION - Dashboard Style -->
    <div class="bk-page-header">
        <div class="header-content-wrapper">
            <div class="header-icon-box">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="header-info">
                <div class="header-greeting">
                    <span class="greeting-text">Manajemen</span>
                    <h1>Semua Catatan Bimbingan</h1>
                </div>
                <div class="header-details">
                    <span class="detail-badge"><i class="fas fa-file-alt"></i> {{ $total_catatan }} catatan</span>
                    @if(!empty($tahun_pelajaran_aktif))
                    <span class="detail-badge"><i class="fas fa-calendar-alt"></i> {{ $tahun_pelajaran_aktif }} - {{ $semester_aktif }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="header-actions-box">
            <button type="button" onclick="openSearchModal()" class="btn-action-header btn-primary-header">
                <i class="fas fa-user-plus"></i> <span class="btn-text">Input Catatan Baru</span>
            </button>
            <a href="{{ route('guru_bk.dashboard') }}" class="btn-action-header btn-secondary-header">
                <i class="fas fa-arrow-left"></i> <span class="btn-text">Kembali</span>
            </a>
        </div>
    </div>

    <!-- STATISTICS SECTION - Dashboard Style -->
    <div class="chart-card">
        <div class="chart-header">
            <i class="fas fa-chart-pie"></i>
            <h3>Statistik Catatan Bimbingan</h3>
        </div>
        <div class="quick-stats-grid">
            <div class="stat-card-mini primary clickable-stat {{ empty($status_filter) ? 'active' : '' }}" data-status="all" onclick="filterByStatusCard('all')">
                <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                <div>
                    <h3>{{ $total_catatan }}</h3>
                    <p>Total</p>
                </div>
            </div>
            <div class="stat-card-mini danger clickable-stat {{ $status_filter == 'Belum Ditangani' ? 'active' : '' }}" data-status="Belum Ditangani" onclick="filterByStatusCard('Belum Ditangani')">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div>
                    <h3>{{ $status_stats['Belum'] ?? 0 }}</h3>
                    <p>Belum</p>
                </div>
            </div>
            <div class="stat-card-mini warning clickable-stat {{ $status_filter == 'Dalam Proses' ? 'active' : '' }}" data-status="Dalam Proses" onclick="filterByStatusCard('Dalam Proses')">
                <div class="stat-icon"><i class="fas fa-spinner"></i></div>
                <div>
                    <h3>{{ $status_stats['Proses'] ?? 0 }}</h3>
                    <p>Proses</p>
                </div>
            </div>
            <div class="stat-card-mini success clickable-stat {{ $status_filter == 'Selesai' ? 'active' : '' }}" data-status="Selesai" onclick="filterByStatusCard('Selesai')">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <h3>{{ $status_stats['Selesai'] ?? 0 }}</h3>
                    <p>Selesai</p>
                </div>
            </div>
        </div>
    </div>

    <!-- MOBILE ACTION BUTTONS (only visible on mobile) -->
    <div class="mobile-actions-wrapper">
        <button type="button" onclick="openSearchModal()" class="btn-mobile-action btn-mobile-primary">
            <i class="fas fa-user-plus"></i> Input Catatan Baru
        </button>
        <a href="{{ route('guru_bk.dashboard') }}" class="btn-mobile-action btn-mobile-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- FILTER BAR -->
    <div class="filter-bar">
        <form method="GET" class="filter-form-horizontal">
            <div class="filter-row">
                <div class="filter-item">
                    <label class="filter-label">Pencarian</label>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama siswa, NISN..." value="{{ $search }}">
                </div>
                
                <div class="filter-item">
                    <label class="filter-label">Tahun</label>
                    <select name="tahun" class="form-select modern-select">
                        <option value="">Semua</option>
                        @foreach($tahun_list as $tahun)
                        <option value="{{ $tahun }}" {{ $tahun_filter == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label class="filter-label">Semester</label>
                    <select name="semester" class="form-select modern-select">
                        <option value="">Semua</option>
                        <option value="Ganjil" {{ $semester_filter == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ $semester_filter == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
                
                <!-- Hidden input for status filter (controlled by stat cards) -->
                <input type="hidden" name="status" value="{{ $status_filter }}">

                <div class="filter-actions-horizontal">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('guru_bk.semua-catatan') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-refresh"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- CATATAN CARDS SECTION -->
    @if($total_catatan > 0)
    <div class="catatan-cards-container">
        <div class="cards-header">
            <div class="cards-header-left">
                <i class="fas fa-clipboard-list"></i>
                <span>Daftar Catatan Bimbingan</span>
            </div>
            <div class="cards-header-right">
                <button type="button" class="btn-expand-all" onclick="expandAllCards()">
                    <i class="fas fa-expand-alt"></i> Expand All
                </button>
                <button type="button" class="btn-collapse-all" onclick="collapseAllCards()">
                    <i class="fas fa-compress-alt"></i> Collapse All
                </button>
            </div>
        </div>
        
        <div class="catatan-cards-grid">
            @php $no = 1; @endphp
            @foreach($catatan_list as $catatan)
            @php
                // Status styling
                $status = $catatan->status ?? 'Belum';
                $status_display = $status;
                if ($status === 'Belum') $status_display = 'Belum Ditangani';
                elseif ($status === 'Proses') $status_display = 'Dalam Proses';
                
                $status_colors = [
                    'Belum' => ['color' => '#ef4444', 'bg' => '#fef2f2', 'icon' => 'fa-clock'],
                    'Proses' => ['color' => '#f59e0b', 'bg' => '#fffbeb', 'icon' => 'fa-spinner'],
                    'Selesai' => ['color' => '#10b981', 'bg' => '#f0fdf4', 'icon' => 'fa-check-circle'],
                ];
                $status_color = $status_colors[$status]['color'] ?? '#6b7280';
                $status_bg = $status_colors[$status]['bg'] ?? '#f3f4f6';
                $status_icon = $status_colors[$status]['icon'] ?? 'fa-question';
                
                // Jenis icon
                $jenis_icons = [
                    'pribadi' => ['icon' => 'fa-user', 'color' => '#8b5cf6'],
                    'sosial' => ['icon' => 'fa-users', 'color' => '#3b82f6'],
                    'belajar' => ['icon' => 'fa-book', 'color' => '#10b981'],
                    'karir' => ['icon' => 'fa-briefcase', 'color' => '#f59e0b'],
                ];
                $jenis_lower = strtolower($catatan->jenis_bimbingan);
                $jenis_icon = $jenis_icons[$jenis_lower]['icon'] ?? 'fa-clipboard';
                $jenis_color = $jenis_icons[$jenis_lower]['color'] ?? '#6b7280';
                
                $cardId = 'card-' . $catatan->id;
            @endphp
            
            <div class="catatan-card collapsed" data-card-id="{{ $cardId }}">
                <div class="catatan-card-header" onclick="toggleCatatanCard('{{ $cardId }}')">
                    <div class="card-header-left">
                        <div class="card-number">{{ $no++ }}</div>
                        <div class="card-main-info">
                            <div class="student-name">{{ $catatan->nama_siswa ?? '-' }}</div>
                            <div class="student-meta">
                                <span class="nisn-badge"><i class="fas fa-id-card"></i> {{ $catatan->nisn }}</span>
                                <span class="jenis-badge" style="background: {{ $jenis_color }}15; color: {{ $jenis_color }};">
                                    <i class="fas {{ $jenis_icon }}"></i> {{ $catatan->jenis_bimbingan }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-header-right">
                        <div class="card-status-date">
                            <span class="status-badge-card" style="background: {{ $status_bg }}; color: {{ $status_color }};">
                                <i class="fas {{ $status_icon }}"></i> {{ $status_display }}
                            </span>
                            <span class="date-badge">
                                <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($catatan->tanggal)->format('d M Y') }}
                            </span>
                        </div>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </div>
                </div>
                
                <div class="catatan-card-body" id="{{ $cardId }}">
                    <div class="card-info-grid">
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-user-tie"></i> Guru BK</div>
                            <div class="info-value">
                                @php
                                    $nama_guru_bk_aktual = \App\Http\Controllers\GuruBK\SemuaCatatanController::getGuruBKForCatatan($catatan);
                                @endphp
                                {{ $nama_guru_bk_aktual }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-pencil-alt"></i> Dicatat Oleh</div>
                            <div class="info-value">
                                {{ !empty($catatan->pencatat_nama) ? $catatan->pencatat_nama : '-' }}
                                @if(!empty($catatan->pencatat_role) && $catatan->pencatat_role !== 'guru_bk')
                                <span class="role-badge">{{ ucwords(str_replace('_', ' ', $catatan->pencatat_role)) }}</span>
                                @endif
                            </div>
                        </div>
                        @if(!empty($catatan->updated_at))
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-sync"></i> Update Terakhir</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($catatan->updated_at)->format('d M Y H:i') }}</div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="masalah-section">
                        <div class="section-title"><i class="fas fa-exclamation-circle"></i> Masalah</div>
                        <div class="section-content">{{ $catatan->masalah ?? '-' }}</div>
                    </div>
                    
                    @if(!empty($catatan->tindakan))
                    <div class="tindakan-section">
                        <div class="section-title"><i class="fas fa-hand-holding-medical"></i> Tindakan</div>
                        <div class="section-content">{{ $catatan->tindakan }}</div>
                    </div>
                    @endif
                    
                    @if(!empty($catatan->hasil))
                    <div class="hasil-section">
                        <div class="section-title"><i class="fas fa-clipboard-check"></i> Hasil</div>
                        <div class="section-content">{{ $catatan->hasil }}</div>
                    </div>
                    @endif
                    
                    <div class="card-actions">
                        <a href="{{ route('guru_bk.catatan-bimbingan', ['nisn' => $catatan->nisn]) }}" class="btn-card-action btn-view">
                            <i class="fas fa-eye"></i> Lihat Catatan Siswa
                        </a>
                        <a href="{{ route('guru_bk.catatan-bimbingan.edit', $catatan->id) }}" class="btn-card-action btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('guru_bk.catatan-bimbingan.print', $catatan->id) }}" class="btn-card-action btn-print" target="_blank">
                            <i class="fas fa-print"></i> Cetak
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <h3>Tidak Ada Catatan</h3>
        <p>
            @if(!empty($search) || !empty($tahun_filter) || !empty($semester_filter) || !empty($status_filter))
            Tidak ada catatan yang sesuai dengan filter.
            @else
            Belum ada catatan bimbingan yang tersimpan.
            @endif
        </p>
    </div>
    @endif
</div>

<!-- MODAL SEARCH SISWA -->
<div id="modalSearchSiswa" class="modal-search-overlay" style="display: none;">
    <div class="modal-search-content">
        <div class="modal-search-header">
            <div class="modal-header-content">
                <div class="modal-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <h3>Cari Siswa</h3>
                    <p>Pilih siswa untuk membuat catatan bimbingan baru</p>
                </div>
            </div>
            <button type="button" onclick="closeSearchModal()" class="modal-close-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-search-body">
            <div class="search-input-container">
                <i class="fas fa-search search-icon-input"></i>
                <input type="text" id="searchSiswaInput" placeholder="Ketik nama, NISN, atau NIS siswa..." onkeyup="searchSiswa(this.value)">
                <div id="searchLoading" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
            </div>
            
            <div id="searchResultsContainer">
                <div class="search-empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Cari Siswa</h4>
                    <p>Ketik minimal 2 karakter untuk mencari siswa</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Main Content Styles */
.main-content {
    background: #f9fafb;
    min-height: calc(100vh - 70px);
    padding: 25px;
    transition: all 0.3s ease;
}

/* ============================================
   DASHBOARD-STYLE HEADER
   ============================================ */
.bk-page-header {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
    border-radius: 16px;
    padding: 25px 30px;
    margin-bottom: 25px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(139, 92, 246, 0.3);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.header-content-wrapper {
    display: flex;
    align-items: center;
    gap: 20px;
}

.header-icon-box {
    width: 70px;
    height: 70px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.header-icon-box i {
    font-size: 28px;
    color: white;
}

.header-info .header-greeting .greeting-text {
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    display: block;
    margin-bottom: 4px;
}

.header-info .header-greeting h1 {
    color: white;
    font-size: 24px;
    font-weight: 700;
    margin: 0;
}

.header-details {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 12px;
}

.detail-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(5px);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.header-actions-box {
    display: flex;
    gap: 10px;
}

.btn-action-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: all 0.3s ease;
}

.btn-primary-header {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.btn-primary-header:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.btn-secondary-header {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    backdrop-filter: blur(5px);
}

.btn-secondary-header:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white;
}

/* ============================================
   DASHBOARD-STYLE STATS (CHART CARD)
   ============================================ */
.chart-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    margin-bottom: 25px;
}

.chart-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.chart-header h3 {
    margin: 0;
    font-size: 18px;
    color: #1f2937;
}

.chart-header i {
    color: #8b5cf6;
}

.quick-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.stat-card-mini {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    gap: 15px;
    border: 1px solid #f3f4f6;
    transition: all 0.3s ease;
}

.stat-card-mini:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-card-mini .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.stat-card-mini.primary .stat-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-card-mini.warning .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-card-mini.success .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
.stat-card-mini.danger .stat-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }

.stat-card-mini h3 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
}

.stat-card-mini p {
    margin: 4px 0 0 0;
    color: #6b7280;
    font-size: 13px;
}

/* Mobile Actions Wrapper - Hidden on desktop */
.mobile-actions-wrapper {
    display: none;
}

/* ============================================
   OLD HEADER STYLES (KEPT FOR COMPAT)
   ============================================ */
.subtitle-item {
    display: flex;
    gap: 20px;
    margin-top: 8px;
    font-size: 0.9rem;
    opacity: 0.9;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.btn-search-siswa {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    transition: all 0.3s ease;
}

.btn-search-siswa:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.btn-back {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: rgba(255,255,255,0.2);
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: rgba(255,255,255,0.3);
    color: white;
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
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
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

.stat-icon.primary { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
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
    margin-bottom: 25px;
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

.form-control, .form-select {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
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

.btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
}

/* Table Styles */
.table-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.table-responsive {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.data-table thead tr {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
}

.data-table th {
    padding: 15px;
    color: white;
    font-weight: 600;
    text-align: left;
}

.data-table th.text-center {
    text-align: center;
}

.data-table tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.2s;
}

.data-table tbody tr:hover {
    background: #f8fafc;
}

.data-table td {
    padding: 15px;
    color: #374151;
}

.data-table td.text-center {
    text-align: center;
}

.data-table td.text-muted {
    color: #6b7280;
}

.student-name {
    font-weight: 600;
    color: #1f2937;
}

.student-nisn {
    font-size: 0.8rem;
    color: #6b7280;
}

.badge-jenis {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.masalah-cell {
    max-width: 250px;
}

.masalah-text {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.badge-status {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.guru-name {
    font-weight: 500;
}

.guru-update {
    font-size: 0.75rem;
    color: #9ca3af;
}

.pencatat-name {
    font-weight: 500;
}

.badge-role {
    background: #e0e7ff;
    color: #4338ca;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.7rem;
}

.action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
}

.btn-action {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-action.btn-info {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.btn-action.btn-warning {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.btn-action.btn-success {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
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
    margin: 0;
    color: #6b7280;
}

/* Clickable Stat Cards */
.stat-card-mini.clickable-stat {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.stat-card-mini.clickable-stat:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
}

.stat-card-mini.clickable-stat.active {
    border-color: #4f46e5;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
}

/* MODAL SEARCH STYLES */
.modal-search-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-search-content {
    background: white;
    border-radius: 20px;
    width: 95%;
    max-width: 700px;
    max-height: 85vh;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-search-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.modal-icon {
    width: 45px;
    height: 45px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-search-header h3 {
    margin: 0;
    font-size: 1.25rem;
}

.modal-search-header p {
    margin: 0;
    font-size: 0.85rem;
    opacity: 0.9;
}

.modal-close-btn {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.2);
    border: none;
    border-radius: 50%;
    color: white;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.modal-close-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(90deg);
}

.modal-search-body {
    padding: 25px;
    max-height: calc(85vh - 100px);
    overflow-y: auto;
}

.search-input-container {
    position: relative;
    margin-bottom: 20px;
}

.search-input-container input {
    width: 100%;
    padding: 15px 50px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-input-container input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
}

.search-icon-input {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 18px;
}

#searchLoading {
    position: absolute;
    right: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: #10b981;
}

.search-empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6b7280;
}

.search-empty-state .empty-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.search-empty-state .empty-icon i {
    font-size: 32px;
    color: #10b981;
}

.search-empty-state h4 {
    margin: 0 0 8px;
    color: #374151;
    font-size: 1.1rem;
}

.search-empty-state p {
    margin: 0;
    font-size: 0.9rem;
}

.search-results-table {
    width: 100%;
    border-collapse: collapse;
}

.search-results-table th {
    background: #f8fafc;
    padding: 12px 15px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    font-size: 0.85rem;
    border-bottom: 2px solid #e5e7eb;
}

.search-results-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.9rem;
    color: #4b5563;
}

.search-results-table tr:hover {
    background: #f8fafc;
}

/* Card-based Search Results */
.search-results-cards {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.search-result-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 12px;
    transition: all 0.3s ease;
}

.search-result-card:hover {
    border-color: #10b981;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
}

.result-card-header {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 10px;
}

.result-number {
    width: 24px;
    height: 24px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 11px;
    flex-shrink: 0;
}

.result-student-info {
    flex: 1;
    min-width: 0;
}

.result-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 13px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.gender-badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
}

.gender-badge.male {
    background: #dbeafe;
    color: #1d4ed8;
}

.gender-badge.female {
    background: #fce7f3;
    color: #be185d;
}

.result-details {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.result-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 500;
}

.result-badge.nisn {
    background: #e0e7ff;
    color: #4338ca;
}

.result-badge.kelas {
    background: #dcfce7;
    color: #166534;
}

.result-badge.rombel {
    background: #fef3c7;
    color: #92400e;
}

.result-badge i {
    font-size: 9px;
}

.btn-buat-catatan-card {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    padding: 10px 16px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-buat-catatan-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    color: white;
}

.btn-buat-catatan {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-buat-catatan:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    color: white;
}

.student-name-cell {
    font-weight: 600;
    color: #1f2937;
}

.student-gender-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.student-gender-badge.male {
    background: #dbeafe;
    color: #1d4ed8;
}

.student-gender-badge.female {
    background: #fce7f3;
    color: #be185d;
}

/* RESPONSIVE DESIGN - MOBILE FIRST */
@media (max-width: 991px) {
    .main-content {
        margin-left: 0;
        width: 100%;
        padding: 15px;
    }
    
    .quick-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    /* Hide header actions on mobile */
    .header-actions-box {
        display: none;
    }
    
    /* New Header Responsive - Match Dashboard */
    .bk-page-header {
        padding: 20px;
        margin-bottom: 12px;
    }
    
    .header-content-wrapper {
        flex-direction: column;
        text-align: center;
        gap: 12px;
        width: 100%;
    }
    
    .header-icon-box {
        width: 50px;
        height: 50px;
        margin: 0 auto;
    }
    
    .header-icon-box i {
        font-size: 22px;
    }
    
    .header-info {
        text-align: center;
    }
    
    .header-info .header-greeting h1 {
        font-size: 16px !important;
    }
    
    .header-info .header-greeting .greeting-text {
        font-size: 11px;
    }
    
    .header-details {
        justify-content: center;
        gap: 6px;
    }
    
    .detail-badge {
        font-size: 9px;
        padding: 3px 6px;
    }
    
    /* Stats Grid - Single Row Horizontal */
    .chart-card {
        padding: 10px;
        margin-bottom: 10px;
    }
    
    .chart-header {
        margin-bottom: 8px;
    }
    
    .chart-header h3 {
        font-size: 12px;
    }
    
    .chart-header i {
        font-size: 14px;
    }
    
    .quick-stats-grid {
        display: flex;
        flex-direction: row;
        gap: 6px;
        overflow-x: visible;
    }
    
    .stat-card-mini {
        flex: 1;
        min-width: 0;
        flex-direction: column;
        text-align: center;
        padding: 8px 4px;
        gap: 4px;
    }
    
    .stat-card-mini .stat-icon {
        width: 28px;
        height: 28px;
        font-size: 12px !important;
    }
    
    .stat-card-mini h3 {
        font-size: 14px !important;
        line-height: 1.2;
    }
    
    .stat-card-mini p {
        font-size: 8px !important;
        line-height: 1.2;
    }
    
    /* Mobile Action Buttons */
    .mobile-actions-wrapper {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
    }
    
    .btn-mobile-action {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 11px;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-mobile-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .btn-mobile-secondary {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
    }
    
    /* Filter Bar Mobile */
    .filter-bar {
        padding: 12px;
        margin-bottom: 12px;
    }
    
    .filter-row {
        flex-direction: column;
        gap: 8px;
    }
    
    .filter-item {
        width: 100%;
    }
    
    .filter-label {
        font-size: 10px;
        margin-bottom: 3px;
    }
    
    .form-control, .form-select, .modern-select {
        font-size: 11px !important;
        padding: 8px 10px !important;
    }
    
    .filter-actions-horizontal {
        width: 100%;
        gap: 6px;
    }
    
    .filter-actions-horizontal .btn {
        flex: 1;
        font-size: 10px;
        padding: 8px 10px;
    }
    
    /* Catatan Cards Mobile - Extra Compact */
    .catatan-cards-container {
        border-radius: 8px;
    }
    
    .cards-header {
        padding: 8px 10px;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        gap: 6px;
    }
    
    .cards-header-left {
        font-size: 9px;
    }
    
    .cards-header-left i {
        font-size: 10px;
    }
    
    .cards-header-right {
        gap: 4px;
    }
    
    .btn-expand-all, .btn-collapse-all {
        padding: 4px 6px;
        font-size: 7px;
    }
    
    .catatan-cards-grid {
        padding: 6px;
        gap: 6px;
    }
    
    /* Card Header Mobile */
    .catatan-card {
        border-radius: 8px;
    }
    
    .catatan-card-header {
        padding: 8px;
        flex-direction: row;
        align-items: flex-start;
        gap: 8px;
    }
    
    .card-header-left {
        gap: 8px;
    }
    
    .card-number {
        width: 18px;
        height: 18px;
        font-size: 8px;
        border-radius: 4px;
    }
    
    .catatan-card .student-name {
        font-size: 10px;
        font-weight: 600;
        margin-bottom: 2px;
    }
    
    .student-meta {
        gap: 3px;
        flex-wrap: wrap;
    }
    
    .nisn-badge, .jenis-badge {
        font-size: 7px;
        padding: 1px 4px;
        border-radius: 6px;
    }
    
    .card-header-right {
        gap: 6px;
        flex-shrink: 0;
    }
    
    .card-status-date {
        gap: 2px;
    }
    
    .status-badge-card {
        font-size: 7px;
        padding: 2px 5px;
        border-radius: 10px;
    }
    
    .date-badge {
        font-size: 7px;
    }
    
    .expand-icon {
        font-size: 8px;
    }
    
    /* Card Body Mobile */
    .catatan-card-body {
        padding: 8px;
    }
    
    .card-info-grid {
        gap: 6px;
        margin-bottom: 8px;
        padding-bottom: 6px;
        grid-template-columns: 1fr;
    }
    
    .info-item {
        gap: 1px;
    }
    
    .info-label {
        font-size: 7px;
        gap: 3px;
    }
    
    .info-label i {
        font-size: 8px;
    }
    
    .info-value {
        font-size: 9px;
    }
    
    .role-badge {
        font-size: 6px;
        padding: 1px 3px;
    }
    
    /* Content Sections Mobile */
    .masalah-section, .tindakan-section, .hasil-section {
        padding: 6px 8px;
        margin-bottom: 6px;
        border-radius: 6px;
        border-left-width: 3px;
    }
    
    .section-title {
        font-size: 8px;
        margin-bottom: 3px;
        gap: 4px;
    }
    
    .section-title i {
        font-size: 9px;
    }
    
    .section-content {
        font-size: 9px;
        line-height: 1.35;
    }
    
    /* Card Actions Mobile */
    .card-actions {
        gap: 4px;
        padding-top: 6px;
        flex-direction: row;
    }
    
    .btn-card-action {
        padding: 5px 6px;
        font-size: 8px;
        border-radius: 5px;
        gap: 3px;
        flex: 1;
        justify-content: center;
    }
    
    .btn-card-action i {
        font-size: 9px;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .btn-text {
        display: none;
    }
    
    .modal-search-content {
        width: 100%;
        max-width: 100%;
        border-radius: 0;
        max-height: 100vh;
    }
    
    .modal-search-body {
        max-height: calc(100vh - 100px);
        padding: 15px;
    }
    
    /* Search Results Cards Mobile */
    .search-results-cards {
        gap: 8px;
    }
    
    .search-result-card {
        padding: 10px;
        border-radius: 8px;
    }
    
    .result-card-header {
        gap: 8px;
        margin-bottom: 8px;
    }
    
    .result-number {
        width: 20px;
        height: 20px;
        font-size: 9px;
        border-radius: 5px;
    }
    
    .result-name {
        font-size: 11px;
        margin-bottom: 4px;
    }
    
    .gender-badge {
        font-size: 9px;
        padding: 1px 5px;
    }
    
    .result-details {
        gap: 4px;
    }
    
    .result-badge {
        padding: 2px 6px;
        font-size: 9px;
        border-radius: 10px;
    }
    
    .result-badge i {
        font-size: 8px;
    }
    
    .btn-buat-catatan-card {
        padding: 8px 12px;
        font-size: 10px;
        border-radius: 6px;
    }
    
    .search-input-container input {
        padding: 12px 40px;
        font-size: 13px;
        border-radius: 10px;
    }
    
    .search-input-container i {
        font-size: 14px;
    }
    
    .search-empty-state h4 {
        font-size: 14px;
    }
    
    .search-empty-state p {
        font-size: 11px;
    }
    
    .empty-icon {
        width: 50px;
        height: 50px;
    }
    
    .empty-icon i {
        font-size: 20px;
    }
}

/* ============================================
   CATATAN CARDS STYLES
   ============================================ */
.catatan-cards-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.cards-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid #f3f4f6;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.cards-header-left {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    color: #4f46e5;
    font-size: 1.1rem;
}

.cards-header-left i {
    font-size: 20px;
}

.cards-header-right {
    display: flex;
    gap: 10px;
}

.btn-expand-all, .btn-collapse-all {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-expand-all {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white;
}

.btn-collapse-all {
    background: #e5e7eb;
    color: #374151;
}

.btn-expand-all:hover, .btn-collapse-all:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.catatan-cards-grid {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

/* Catatan Card */
.catatan-card {
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    transition: all 0.3s ease;
}

.catatan-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-color: #c7d2fe;
}

/* Card Header */
.catatan-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    cursor: pointer;
    transition: all 0.3s ease;
}

.catatan-card-header:hover {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
}

.card-header-left {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.card-number {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
}

.card-main-info {
    flex: 1;
}

.catatan-card .student-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 15px;
    margin-bottom: 6px;
}

.student-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

.nisn-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #e0e7ff;
    color: #4338ca;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.jenis-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.card-header-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

.card-status-date {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 5px;
}

.status-badge-card {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.date-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #6b7280;
    font-size: 0.75rem;
}

.expand-icon {
    color: #9ca3af;
    font-size: 14px;
    transition: transform 0.3s ease;
}

.catatan-card.collapsed .expand-icon {
    transform: rotate(-90deg);
}

/* Card Body */
.catatan-card-body {
    padding: 20px;
    background: white;
    border-top: 1px solid #f3f4f6;
    transition: all 0.3s ease;
    max-height: 1000px;
    overflow: hidden;
}

.catatan-card.collapsed .catatan-card-body {
    max-height: 0;
    padding: 0 20px;
    border-top: none;
}

.card-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f3f4f6;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-label {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}

.info-label i {
    color: #9ca3af;
}

.info-value {
    font-size: 0.9rem;
    color: #1f2937;
    font-weight: 500;
}

.role-badge {
    display: inline-block;
    background: #e0e7ff;
    color: #4338ca;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    margin-left: 6px;
}

/* Content Sections */
.masalah-section, .tindakan-section, .hasil-section {
    margin-bottom: 15px;
    padding: 15px;
    border-radius: 10px;
}

.masalah-section {
    background: #fef2f2;
    border-left: 4px solid #ef4444;
}

.tindakan-section {
    background: #fffbeb;
    border-left: 4px solid #f59e0b;
}

.hasil-section {
    background: #f0fdf4;
    border-left: 4px solid #10b981;
}

.section-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.section-content {
    font-size: 0.9rem;
    color: #4b5563;
    line-height: 1.6;
}

/* Card Actions */
.card-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding-top: 15px;
    border-top: 1px solid #f3f4f6;
}

.btn-card-action {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-card-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-view {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
}

.btn-edit {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.btn-print {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

/* Responsive Cards */
@media (max-width: 768px) {
    .cards-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .cards-header-right {
        width: 100%;
    }
    
    .btn-expand-all, .btn-collapse-all {
        flex: 1;
        justify-content: center;
    }
    
    .catatan-cards-grid {
        padding: 15px;
    }
    
    .catatan-card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
        padding: 15px;
    }
    
    .card-header-right {
        width: 100%;
        justify-content: space-between;
    }
    
    .card-status-date {
        flex-direction: row;
        gap: 10px;
        align-items: center;
    }
    
    .card-info-grid {
        grid-template-columns: 1fr;
    }
    
    .card-actions {
        flex-direction: column;
    }
    
    .btn-card-action {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .student-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .card-status-date {
        flex-direction: column;
        align-items: flex-start;
    }
    
    /* Filter Bar Mobile - Inline Layout */
    .filter-bar {
        padding: 12px;
        border-radius: 10px;
    }
    
    .filter-row {
        flex-direction: column;
        gap: 10px;
    }
    
    .filter-item {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 10px;
    }
    
    .filter-label {
        white-space: nowrap;
        min-width: 70px;
        font-size: 11px;
        margin-bottom: 0;
    }
    
    .filter-item .form-control,
    .filter-item .form-select {
        flex: 1;
        font-size: 12px;
        padding: 8px 10px;
    }
    
    .filter-actions-horizontal {
        justify-content: flex-end;
        gap: 8px;
    }
    
    .filter-actions-horizontal .btn {
        font-size: 11px;
        padding: 8px 12px;
    }
}
</style>

<script>
// CATATAN CARDS TOGGLE FUNCTIONS
function toggleCatatanCard(cardId) {
    const card = document.querySelector(`[data-card-id="${cardId}"]`);
    if (card) {
        card.classList.toggle('collapsed');
    }
}

function expandAllCards() {
    document.querySelectorAll('.catatan-card').forEach(card => {
        card.classList.remove('collapsed');
    });
}

function collapseAllCards() {
    document.querySelectorAll('.catatan-card').forEach(card => {
        card.classList.add('collapsed');
    });
}

// FILTER BY STATUS CARD
function filterByStatusCard(status) {
    // Update the status dropdown in the filter form
    const statusSelect = document.querySelector('select[name="status"]');
    if (statusSelect) {
        if (status === 'all') {
            statusSelect.value = '';
        } else {
            statusSelect.value = status;
        }
    }
    
    // Submit the filter form
    const filterForm = document.querySelector('.filter-form-horizontal');
    if (filterForm) {
        filterForm.submit();
    }
}

// Expand all cards on desktop, collapse on mobile
document.addEventListener('DOMContentLoaded', function() {
    if (window.innerWidth > 768) {
        expandAllCards();
    }
});

// SEARCH MODAL FUNCTIONS
let searchTimeout = null;

function openSearchModal() {
    document.getElementById('modalSearchSiswa').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    setTimeout(() => {
        document.getElementById('searchSiswaInput').focus();
    }, 100);
}

function closeSearchModal() {
    document.getElementById('modalSearchSiswa').style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('searchSiswaInput').value = '';
    document.getElementById('searchResultsContainer').innerHTML = `
        <div class="search-empty-state">
            <div class="empty-icon">
                <i class="fas fa-users"></i>
            </div>
            <h4>Cari Siswa</h4>
            <p>Ketik minimal 2 karakter untuk mencari siswa</p>
        </div>
    `;
}

function searchSiswa(query) {
    clearTimeout(searchTimeout);
    
    if (query.length < 2) {
        document.getElementById('searchResultsContainer').innerHTML = `
            <div class="search-empty-state">
                <div class="empty-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h4>Cari Siswa</h4>
                <p>Ketik minimal 2 karakter untuk mencari siswa</p>
            </div>
        `;
        return;
    }
    
    document.getElementById('searchLoading').style.display = 'block';
    
    searchTimeout = setTimeout(() => {
        fetch('{{ route("guru_bk.semua-catatan.search-students") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ search: query })
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('searchLoading').style.display = 'none';
            
            if (data.success && data.data.length > 0) {
                let html = `<div class="search-results-cards">`;
                
                data.data.forEach((s, i) => {
                    const genderClass = s.jk === 'Laki-laki' ? 'male' : 'female';
                    const genderIcon = s.jk === 'Laki-laki' ? '♂' : '♀';
                    html += `
                        <div class="search-result-card">
                            <div class="result-card-header">
                                <span class="result-number">${i + 1}</span>
                                <div class="result-student-info">
                                    <div class="result-name">
                                        ${s.nama}
                                        <span class="gender-badge ${genderClass}">${genderIcon}</span>
                                    </div>
                                    <div class="result-details">
                                        <span class="result-badge nisn"><i class="fas fa-id-card"></i> ${s.nisn}</span>
                                        <span class="result-badge kelas"><i class="fas fa-school"></i> ${s.kelas}</span>
                                        <span class="result-badge rombel"><i class="fas fa-users"></i> ${s.rombel}</span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ url('guru-bk/catatan-bimbingan/create') }}/${s.nisn}" class="btn-buat-catatan-card">
                                <i class="fas fa-plus"></i> Buat Catatan
                            </a>
                        </div>
                    `;
                });
                
                html += '</div>';
                document.getElementById('searchResultsContainer').innerHTML = html;
            } else if (data.success && data.data.length === 0) {
                document.getElementById('searchResultsContainer').innerHTML = `
                    <div class="search-empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4>Tidak Ditemukan</h4>
                        <p>Tidak ada siswa yang cocok dengan pencarian "${query}"</p>
                    </div>
                `;
            } else {
                document.getElementById('searchResultsContainer').innerHTML = `
                    <div class="search-empty-state">
                        <div class="empty-icon" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);">
                            <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                        </div>
                        <h4>Terjadi Kesalahan</h4>
                        <p>${data.message || 'Gagal mencari data siswa'}</p>
                    </div>
                `;
            }
        })
        .catch(err => {
            document.getElementById('searchLoading').style.display = 'none';
            console.error('Search error:', err);
        });
    }, 300);
}

// Close modal on overlay click
document.getElementById('modalSearchSiswa')?.addEventListener('click', function(e) {
    if (e.target === this) closeSearchModal();
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('modalSearchSiswa').style.display === 'flex') {
        closeSearchModal();
    }
});
</script>
@endsection


